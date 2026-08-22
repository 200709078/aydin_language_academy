<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class PlacementTestQuestion extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $question): void {
            $hasContent = $question->placement_test_question_content_id !== null;
            $position = $question->content_position;
            $hasPosition = $position !== null;

            if ($hasContent !== $hasPosition || ($hasPosition && $position < 1)) {
                throw new LogicException(
                    'Ortak içerik ve content_position birlikte tanımlanmalı; sıra pozitif olmalıdır.',
                );
            }

            $points = $question->points;

            if ($points === null || ! is_numeric($points) || (float) $points <= 0) {
                throw new LogicException('Her sorunun puanı sıfırdan büyük olmalıdır.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'placement_test_level_id',
        'placement_test_question_content_id',
        'content_position',
        'question_text',
        'points',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_position' => 'integer',
            'points' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(PlacementTestLevel::class, 'placement_test_level_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PlacementTestQuestionOption::class);
    }

    public function questionContent(): BelongsTo
    {
        return $this->belongsTo(PlacementTestQuestionContent::class, 'placement_test_question_content_id');
    }
}
