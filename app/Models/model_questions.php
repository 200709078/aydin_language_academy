<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class model_questions extends Model
{
    use HasFactory;
    public $table = "questions";
    
    protected $fillable = [
        'exercise_id',
        'question',
        'image',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(model_exercises::class, 'exercise_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')
            ->orderBy('display_position');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(ExerciseAttemptAnswer::class, 'question_id');
    }
}
