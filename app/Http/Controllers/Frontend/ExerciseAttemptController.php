<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ExerciseAttempt;
use App\Models\ExerciseAttemptAnswer;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Services\ExerciseAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class ExerciseAttemptController extends Controller
{
    public function __construct(private readonly ExerciseAttemptService $attempts)
    {
    }

    public function start(Request $request, model_exercises $exercise): RedirectResponse
    {
        try {
            $this->attempts->beginOrResume($request->user(), $exercise);
        } catch (LogicException $exception) {
            return $this->redirectToExercise($exercise)->with('error', $exception->getMessage());
        }

        return $this->redirectToExercise($exercise);
    }

    public function saveAnswer(
        Request $request,
        model_exercises $exercise,
        ExerciseAttempt $exerciseAttempt,
        model_questions $question,
    ): JsonResponse|RedirectResponse {
        $exerciseAttempt = $this->ownedAttempt($request, $exercise, $exerciseAttempt);
        $this->ensureQuestionBelongsToExercise($exercise, $question);

        $validated = $request->validate([
            'selected_option' => ['required', 'integer'],
        ], [
            'selected_option.required' => __('dictt.exercise_attempt_option_invalid'),
            'selected_option.integer' => __('dictt.exercise_attempt_option_invalid'),
        ]);

        try {
            $this->attempts->saveAnswer($exerciseAttempt, $question, (int) $validated['selected_option']);
        } catch (ValidationException | LogicException $exception) {
            $message = $exception instanceof ValidationException
                ? ($exception->errors()['selected_option'][0] ?? __('dictt.exercise_attempt_save_failed'))
                : $exception->getMessage();

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return $this->redirectToExercise($exercise)->with('error', $message);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('dictt.exercise_attempt_answer_saved'),
                'answered_count' => ExerciseAttemptAnswer::query()
                    ->where('exercise_attempt_id', $exerciseAttempt->id)
                    ->whereNotNull('question_option_id')
                    ->count(),
            ]);
        }

        return $this->redirectToExercise($exercise)->with('success', __('dictt.exercise_attempt_answer_saved'));
    }

    public function complete(
        Request $request,
        model_exercises $exercise,
        ExerciseAttempt $exerciseAttempt,
    ): RedirectResponse {
        $exerciseAttempt = $this->ownedAttempt($request, $exercise, $exerciseAttempt);
        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        try {
            $this->attempts->complete($exerciseAttempt, $exercise, $validated['answers'] ?? []);
        } catch (LogicException $exception) {
            return $this->redirectToExercise($exercise)->with('error', $exception->getMessage());
        }

        return $this->redirectToExercise($exercise)->with('success', __('dictt.exercise_attempt_completed'));
    }

    public function history(Request $request, model_exercises $exercise): View
    {
        $exercise->load([
            'theme.levels',
            'theme.sub_levels',
            'questions' => fn ($query) => $query->with('options')->orderBy('id'),
        ]);

        $attempts = ExerciseAttempt::query()
            ->where('user_id', $request->user()->id)
            ->where('exercise_id', $exercise->id)
            ->with('answers.selectedOption')
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END")
            ->orderByDesc('started_at')
            ->paginate(20);

        return view('frontend.exercise-attempts.index', [
            'exercise' => $exercise,
            'theme' => $exercise->theme,
            'attempts' => $attempts,
        ]);
    }

    public function show(
        Request $request,
        model_exercises $exercise,
        ExerciseAttempt $exerciseAttempt,
    ): View|RedirectResponse {
        $exerciseAttempt = $this->ownedAttempt($request, $exercise, $exerciseAttempt);

        if ($exerciseAttempt->status !== 'completed') {
            return $this->redirectToExercise($exercise)->with('error', __('dictt.exercise_attempt_not_completed'));
        }

        $exercise->load([
            'theme.levels',
            'theme.sub_levels',
            'questions' => fn ($query) => $query->with('options')->orderBy('id'),
        ]);
        $exerciseAttempt->load('answers.selectedOption');

        return view('frontend.exercise-attempts.show', [
            'exercise' => $exercise,
            'theme' => $exercise->theme,
            'exerciseAttempt' => $exerciseAttempt,
            'summary' => $exerciseAttempt->summaryFor($exercise->questions),
        ]);
    }

    private function ownedAttempt(
        Request $request,
        model_exercises $exercise,
        ExerciseAttempt $exerciseAttempt,
    ): ExerciseAttempt {
        return ExerciseAttempt::query()
            ->whereKey($exerciseAttempt->id)
            ->where('user_id', $request->user()->id)
            ->where('exercise_id', $exercise->id)
            ->firstOrFail();
    }

    private function ensureQuestionBelongsToExercise(model_exercises $exercise, model_questions $question): void
    {
        if ((int) $question->exercise_id !== (int) $exercise->id) {
            abort(404);
        }
    }

    private function redirectToExercise(model_exercises $exercise): RedirectResponse
    {
        return redirect()->to(
            route('frontend.themes.detail', ['theme_id' => $exercise->theme_id]) . '#exercise-' . $exercise->id,
        )->with('open_exercise_id', $exercise->id);
    }
}
