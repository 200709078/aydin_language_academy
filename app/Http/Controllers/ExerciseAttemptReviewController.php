<?php

namespace App\Http\Controllers;

use App\Models\ExerciseAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExerciseAttemptReviewController extends Controller
{
    /**
     * List every legacy exercise attempt for administrator review.
     */
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'all');
        $allowedFilters = ['all', 'in_progress', 'completed'];

        if (! in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $attempts = ExerciseAttempt::query()
            ->with([
                'user',
                'exercise.theme',
                'exercise.questions.options',
                'answers.selectedOption',
            ]);

        if ($filter === 'in_progress') {
            $attempts->where('status', 'in_progress')
                ->orderBy('started_at');
        } elseif ($filter === 'completed') {
            $attempts->where('status', 'completed')
                ->orderByDesc('completed_at');
        } else {
            $attempts->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END")
                ->orderByRaw("CASE WHEN status = 'in_progress' THEN started_at END ASC")
                ->orderByRaw("CASE WHEN status = 'completed' THEN completed_at END DESC")
                ->orderByDesc('id');
        }

        $attempts = $attempts->paginate(20)->withQueryString();

        return view('admin.exercise-attempts.index', compact('attempts', 'filter'));
    }

    /**
     * Show an administrator-only, read-only review using current exercise data.
     */
    public function show(ExerciseAttempt $exerciseAttempt): View
    {
        $exerciseAttempt->load([
            'user',
            'exercise.theme',
            'exercise.questions' => fn ($query) => $query
                ->with('options')
                ->orderBy('id'),
            'answers.selectedOption',
        ]);

        $exercise = $exerciseAttempt->exercise;
        abort_unless($exercise !== null, 404);

        return view('admin.exercise-attempts.show', [
            'exerciseAttempt' => $exerciseAttempt,
            'exercise' => $exercise,
            'theme' => $exercise->theme,
            'summary' => $exerciseAttempt->summaryFor($exercise->questions),
        ]);
    }
}
