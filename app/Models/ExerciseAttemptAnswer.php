<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseAttemptAnswer extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'exercise_attempt_id',
        'question_id',
        'question_option_id',
        'answered_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExerciseAttempt::class, 'exercise_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(model_questions::class, 'question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
