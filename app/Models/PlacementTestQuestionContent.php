<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementTestQuestionContent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'placement_test_level_id',
        'type',
        'text_content',
        'media_disk',
        'media_path',
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
            'is_active' => 'boolean',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(PlacementTestLevel::class, 'placement_test_level_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PlacementTestQuestion::class, 'placement_test_question_content_id');
    }

    public function resultContentSnapshots(): HasMany
    {
        return $this->hasMany(PlacementTestLevelResultContent::class, 'placement_test_question_content_id');
    }
}
