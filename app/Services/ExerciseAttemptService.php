<?php

namespace App\Services;

use App\Models\ExerciseAttempt;
use App\Models\ExerciseAttemptAnswer;
use App\Models\QuestionOption;
use App\Models\User;
use App\Models\model_exercises;
use App\Models\model_questions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class ExerciseAttemptService
{
    public function beginOrResume(User $user, model_exercises $exercise): ExerciseAttempt
    {
        return DB::transaction(function () use ($user, $exercise): ExerciseAttempt {
            if (! $exercise->questions()->exists()) {
                throw new LogicException(__('dictt.exercise_attempt_no_questions'));
            }

            $openAttempt = ExerciseAttempt::query()
                ->where('user_id', $user->id)
                ->where('exercise_id', $exercise->id)
                ->where('status', 'in_progress')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($openAttempt !== null) {
                return $openAttempt;
            }

            return ExerciseAttempt::query()->create([
                'user_id' => $user->id,
                'exercise_id' => $exercise->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        });
    }

    public function saveAnswer(
        ExerciseAttempt $attempt,
        model_questions $question,
        int $optionId,
    ): ExerciseAttemptAnswer {
        return DB::transaction(function () use ($attempt, $question, $optionId): ExerciseAttemptAnswer {
            $attempt = ExerciseAttempt::query()
                ->lockForUpdate()
                ->findOrFail($attempt->id);
            $this->ensureInProgress($attempt);

            $option = QuestionOption::query()
                ->whereKey($optionId)
                ->where('question_id', $question->id)
                ->first();

            if ($option === null) {
                throw ValidationException::withMessages([
                    'selected_option' => __('dictt.exercise_attempt_option_invalid'),
                ]);
            }

            return ExerciseAttemptAnswer::query()->updateOrCreate(
                [
                    'exercise_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_option_id' => $option->id,
                    'answered_at' => now(),
                ],
            );
        });
    }

    /**
     * @param  array<array-key, mixed>  $answers
     */
    public function complete(
        ExerciseAttempt $attempt,
        model_exercises $exercise,
        array $answers,
    ): ExerciseAttempt {
        return DB::transaction(function () use ($attempt, $exercise, $answers): ExerciseAttempt {
            $attempt = ExerciseAttempt::query()
                ->lockForUpdate()
                ->findOrFail($attempt->id);
            $this->ensureInProgress($attempt);

            $questionIds = collect(array_keys($answers))
                ->filter(static fn ($id): bool => ctype_digit((string) $id))
                ->map(static fn ($id): int => (int) $id)
                ->values()
                ->all();
            $questions = model_questions::query()
                ->where('exercise_id', $exercise->id)
                ->whereIn('id', $questionIds)
                ->get()
                ->keyBy('id');

            foreach ($questions as $question) {
                $optionId = $answers[$question->id] ?? null;

                if (! is_numeric($optionId)) {
                    continue;
                }

                $option = QuestionOption::query()
                    ->whereKey((int) $optionId)
                    ->where('question_id', $question->id)
                    ->first();

                if ($option === null) {
                    ExerciseAttemptAnswer::query()
                        ->where('exercise_attempt_id', $attempt->id)
                        ->where('question_id', $question->id)
                        ->delete();

                    continue;
                }

                ExerciseAttemptAnswer::query()->updateOrCreate(
                    [
                        'exercise_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'question_option_id' => $option->id,
                        'answered_at' => now(),
                    ],
                );
            }

            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $attempt;
        });
    }

    private function ensureInProgress(ExerciseAttempt $attempt): void
    {
        if ($attempt->status !== 'in_progress') {
            throw new LogicException(__('dictt.exercise_attempt_not_in_progress'));
        }
    }
}
