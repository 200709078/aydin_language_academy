<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ExerciseAttempt extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'exercise_id',
        'status',
        'started_at',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(model_exercises::class, 'exercise_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExerciseAttemptAnswer::class);
    }

    /**
     * Resolve a summary from the current questions and choices, intentionally
     * without a historical snapshot.
     *
     * @param  Collection<int, model_questions>  $questions
     * @return array{total: int, answered: int, correct: int, wrong: int, blank: int}
     */
    public function summaryFor(Collection $questions): array
    {
        $answersByQuestion = $this->answers->keyBy('question_id');
        $summary = [
            'total' => $questions->count(),
            'answered' => 0,
            'correct' => 0,
            'wrong' => 0,
            'blank' => 0,
        ];

        foreach ($questions as $question) {
            $answer = $answersByQuestion->get($question->id);
            $selectedOption = $answer?->selectedOption;

            if ($selectedOption === null || (int) $selectedOption->question_id !== (int) $question->id) {
                $summary['blank']++;

                continue;
            }

            $summary['answered']++;

            if ($selectedOption->is_correct) {
                $summary['correct']++;
            } else {
                $summary['wrong']++;
            }
        }

        return $summary;
    }
}
