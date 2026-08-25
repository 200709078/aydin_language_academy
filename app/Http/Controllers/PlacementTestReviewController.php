<?php

namespace App\Http\Controllers;

use App\Exceptions\PlacementTestStateException;
use App\Models\PlacementTest;
use App\Models\PlacementTestLevelResultContent;
use App\Services\PlacementTestAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PlacementTestReviewController extends Controller
{
    public function __construct(private readonly PlacementTestAttemptService $attempts)
    {
    }

    /**
     * List submitted attempts so an administrator can open a full snapshot review.
     */
    public function index(): View
    {
        $attempts = PlacementTest::query()
            ->whereIn('status', ['pending_approval', 'approved'])
            ->with(['user', 'resultLevel', 'approver'])
            ->orderByRaw("CASE status WHEN 'pending_approval' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE WHEN status = 'pending_approval' THEN submitted_at END ASC")
            ->orderByDesc('approved_at')
            ->paginate(20);

        return view('admin.placement-test.attempts.index', compact('attempts'));
    }

    /**
     * Render immutable question and option snapshots for one submitted attempt.
     */
    public function show(PlacementTest $placementTest): View
    {
        $this->ensureReviewable($placementTest);

        $placementTest->load([
            'user',
            'resultLevel',
            'approver',
            'levelResults.level',
            'levelResults.levelQuestions' => fn ($query) => $query
                ->with('contentSnapshot')
                ->orderBy('display_position'),
        ]);

        $levelResults = $placementTest->levelResults
            ->sortBy(fn ($levelResult): int => $levelResult->level?->sequence ?? PHP_INT_MAX)
            ->values();

        return view('admin.placement-test.attempts.show', compact('placementTest', 'levelResults'));
    }

    /**
     * Approve only a pending attempt. Snapshots and calculated result are never changed here.
     */
    public function approve(Request $request, PlacementTest $placementTest): RedirectResponse
    {
        try {
            $this->attempts->approveByAdmin($request->user(), $placementTest);
        } catch (PlacementTestStateException) {
            return redirect()
                ->route('placement_test_attempts_show', $placementTest)
                ->with('error', __('dictt.placement_test_attempt_not_pending'));
        }

        return redirect()
            ->route('placement_test_attempts_show', $placementTest)
            ->with('success', __('dictt.placement_test_attempt_approved_success'));
    }

    /**
     * Stream a historical shared-media snapshot only for the reviewed attempt.
     */
    public function media(
        PlacementTest $placementTest,
        PlacementTestLevelResultContent $placementTestLevelResultContent,
    ) {
        $this->ensureReviewable($placementTest);

        $contentSnapshot = PlacementTestLevelResultContent::query()
            ->whereKey($placementTestLevelResultContent->id)
            ->whereHas('levelResult', fn ($query) => $query->where('placement_test_id', $placementTest->id))
            ->firstOrFail();

        $mediaPath = trim((string) $contentSnapshot->media_path_snapshot);
        $pathSegments = explode('/', $mediaPath);

        if (
            ! in_array($contentSnapshot->type_snapshot, ['audio', 'image', 'video'], true)
            || $contentSnapshot->media_disk_snapshot !== 'local'
            || $mediaPath === ''
            || str_contains($mediaPath, '\\')
            || in_array('..', $pathSegments, true)
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

    private function ensureReviewable(PlacementTest $placementTest): void
    {
        if (! in_array($placementTest->status, ['pending_approval', 'approved'], true)) {
            abort(404);
        }
    }
}
