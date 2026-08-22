<?php

namespace App\Http\Controllers;

use App\Exceptions\PlacementTestConfigurationException;
use App\Exceptions\PlacementTestStateException;
use App\Models\PlacementTest;
use App\Models\PlacementTestLevelQuestion;
use App\Models\PlacementTestLevelResultContent;
use App\Services\PlacementTestAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PlacementTestAttemptController extends Controller
{
    public function __construct(private readonly PlacementTestAttemptService $attempts)
    {
    }

    /**
     * Show the public introduction and an authenticated user's open-attempt state.
     */
    public function landing(Request $request): View
    {
        $openAttempt = $request->user() === null
            ? null
            : $this->attempts->openAttemptFor($request->user());

        return view('frontend.placement-test', compact('openAttempt'));
    }

    /**
     * Start a new attempt or resume the user's existing in-progress attempt.
     */
    public function start(Request $request): RedirectResponse
    {
        try {
            $attempt = $this->attempts->beginOrResume($request->user());
        } catch (PlacementTestConfigurationException | PlacementTestStateException $exception) {
            return redirect()
                ->route('frontend.placement-test')
                ->with('error', $exception->getMessage());
        }

        if ($attempt->status === 'pending_approval') {
            return redirect()->route('frontend.placement-test.completed', $attempt);
        }

        return redirect()->route('frontend.placement-test.exam', $attempt);
    }

    /**
     * Redirect an in-progress attempt to its first blank, or last saved, question.
     */
    public function resume(Request $request, PlacementTest $placementTest): RedirectResponse
    {
        $this->ensureOwnership($request, $placementTest);
        $placementTest->refresh();

        if ($placementTest->status !== 'in_progress') {
            return redirect()->route('frontend.placement-test.completed', $placementTest);
        }

        try {
            $question = $this->attempts->resumeQuestionForUser($request->user(), $placementTest);
        } catch (PlacementTestStateException $exception) {
            return redirect()
                ->route('frontend.placement-test')
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('frontend.placement-test.question', [
            'placementTest' => $placementTest,
            'placementTestLevelQuestion' => $question,
        ]);
    }

    /**
     * Display exactly one question from the user's current level snapshot.
     */
    public function showQuestion(
        Request $request,
        PlacementTest $placementTest,
        PlacementTestLevelQuestion $placementTestLevelQuestion,
    ): View|RedirectResponse {
        $this->ensureOwnership($request, $placementTest);
        $placementTest->refresh();

        if ($placementTest->status !== 'in_progress') {
            return redirect()->route('frontend.placement-test.completed', $placementTest);
        }

        try {
            $levelResult = $this->attempts->currentLevelResultForUser($request->user(), $placementTest);
            $currentQuestion = $this->attempts->currentQuestionForUser(
                $request->user(),
                $placementTest,
                $placementTestLevelQuestion,
            );
        } catch (PlacementTestStateException $exception) {
            return redirect()
                ->route('frontend.placement-test')
                ->with('error', $exception->getMessage());
        }

        $questions = $this->attempts->questionsForLevelResult($levelResult);
        $currentIndex = $questions->search(
            static fn (PlacementTestLevelQuestion $question): bool => $question->id === $currentQuestion->id,
        );

        if ($currentIndex === false) {
            return redirect()->route('frontend.placement-test.exam', $placementTest);
        }

        return view('frontend.placement-test.exam', [
            'placementTest' => $placementTest,
            'level' => $levelResult->level,
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'previousQuestion' => $questions->get($currentIndex - 1),
            'nextQuestion' => $questions->get($currentIndex + 1),
            'answeredQuestionCount' => $questions->where('answer_status', '!=', 'blank')->count(),
            'contentSnapshot' => $currentQuestion->contentSnapshot,
        ]);
    }

    /**
     * Persist one answer. A normal form redirect is kept as a no-JavaScript fallback.
     */
    public function saveAnswer(
        Request $request,
        PlacementTest $placementTest,
        PlacementTestLevelQuestion $placementTestLevelQuestion,
    ): RedirectResponse|JsonResponse {
        $this->ensureOwnership($request, $placementTest);

        $validated = $request->validate([
            'selected_option' => ['nullable', 'integer'],
            'clear_answer' => ['nullable', 'boolean'],
            'finish_level' => ['nullable', 'boolean'],
            'go_to_question' => ['nullable', 'integer'],
        ], [
            'selected_option.integer' => 'Seçilen şık geçersiz.',
            'go_to_question.integer' => 'Hedef soru geçersiz.',
        ]);

        $selectedOption = $request->boolean('clear_answer')
            ? null
            : ($validated['selected_option'] ?? null);

        try {
            $savedQuestion = $this->attempts->saveAnswer(
                $request->user(),
                $placementTest,
                $placementTestLevelQuestion,
                $selectedOption,
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cevabınız kaydedildi.',
                ]);
            }

            if ($request->boolean('finish_level')) {
                $outcome = $this->attempts->completeCurrentLevel($request->user(), $placementTest);

                if ($outcome['submitted']) {
                    return redirect()->route('frontend.placement-test.completed', $placementTest);
                }

                return redirect()->route('frontend.placement-test.exam', $placementTest);
            }

            $targetQuestionId = $validated['go_to_question'] ?? null;

            if ($targetQuestionId !== null) {
                $targetQuestion = PlacementTestLevelQuestion::query()->find($targetQuestionId);

                if ($targetQuestion !== null) {
                    $targetQuestion = $this->attempts->currentQuestionForUser(
                        $request->user(),
                        $placementTest,
                        $targetQuestion,
                    );

                    return redirect()->route('frontend.placement-test.question', [
                        'placementTest' => $placementTest,
                        'placementTestLevelQuestion' => $targetQuestion,
                    ]);
                }
            }

            return redirect()->route('frontend.placement-test.question', [
                'placementTest' => $placementTest,
                'placementTestLevelQuestion' => $savedQuestion,
            ]);
        } catch (PlacementTestConfigurationException | PlacementTestStateException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return redirect()
                ->route('frontend.placement-test')
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the post-submission state without exposing an unapproved result.
     */
    public function completed(Request $request, PlacementTest $placementTest): View|RedirectResponse
    {
        $this->ensureOwnership($request, $placementTest);
        $placementTest->refresh()->load('resultLevel');

        if ($placementTest->status === 'in_progress') {
            return redirect()->route('frontend.placement-test.exam', $placementTest);
        }

        if (! in_array($placementTest->status, ['pending_approval', 'approved'], true)) {
            return redirect()->route('frontend.placement-test');
        }

        return view('frontend.placement-test.completed', compact('placementTest'));
    }

    /**
     * Stream a private media snapshot only while its owner is answering that level.
     */
    public function media(
        Request $request,
        PlacementTest $placementTest,
        PlacementTestLevelResultContent $placementTestLevelResultContent,
    ) {
        $this->ensureOwnership($request, $placementTest);

        try {
            $contentSnapshot = $this->attempts->currentContentSnapshotForUser(
                $request->user(),
                $placementTest,
                $placementTestLevelResultContent,
            );
        } catch (PlacementTestStateException) {
            abort(404);
        }

        $mediaPath = trim((string) $contentSnapshot->media_path_snapshot);

        if (
            $contentSnapshot->type_snapshot === 'text'
            || $contentSnapshot->media_disk_snapshot !== 'local'
            || $mediaPath === ''
            || ! str_starts_with($mediaPath, 'placement-test/question-contents/')
        ) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($mediaPath)) {
            abort(404);
        }

        return $disk->response($mediaPath);
    }

    private function ensureOwnership(Request $request, PlacementTest $placementTest): void
    {
        if ((int) $placementTest->user_id !== (int) $request->user()->id) {
            abort(404);
        }
    }
}
