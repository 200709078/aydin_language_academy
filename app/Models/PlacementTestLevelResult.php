<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementTestLevelResult extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_count_snapshot' => 'integer',
            'pass_percentage_snapshot' => 'decimal:2',
            'total_points_snapshot' => 'decimal:2',
            'correct_points' => 'decimal:2',
            'correct_count' => 'integer',
            'wrong_count' => 'integer',
            'blank_count' => 'integer',
            'score_percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function placementTest(): BelongsTo
    {
        return $this->belongsTo(PlacementTest::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(PlacementTestLevel::class, 'placement_test_level_id');
    }

    public function levelQuestions(): HasMany
    {
        return $this->hasMany(PlacementTestLevelQuestion::class);
    }

    public function contentSnapshots(): HasMany
    {
        return $this->hasMany(PlacementTestLevelResultContent::class);
    }
}
