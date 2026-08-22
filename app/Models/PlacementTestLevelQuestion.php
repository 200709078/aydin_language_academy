<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementTestLevelQuestion extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'display_position' => 'integer',
            'options_snapshot' => 'array',
            'correct_option_snapshot' => 'integer',
            'points_snapshot' => 'decimal:2',
            'selected_option' => 'integer',
            'answered_at' => 'datetime',
        ];
    }

    public function levelResult(): BelongsTo
    {
        return $this->belongsTo(PlacementTestLevelResult::class, 'placement_test_level_result_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PlacementTestQuestion::class, 'placement_test_question_id');
    }

    public function contentSnapshot(): BelongsTo
    {
        return $this->belongsTo(
            PlacementTestLevelResultContent::class,
            'placement_test_level_result_content_id',
        );
    }
}
