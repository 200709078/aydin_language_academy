<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class PlacementTestLevelResultContent extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $contentSnapshot): void {
            $levelResult = PlacementTestLevelResult::query()
                ->find($contentSnapshot->placement_test_level_result_id);

            if (
                $levelResult === null
                || (int) $contentSnapshot->placement_test_level_id !== (int) $levelResult->placement_test_level_id
            ) {
                throw new LogicException('İçerik snapshot seviyesi, bağlı sonuç seviyesiyle eşleşmelidir.');
            }

            if ($contentSnapshot->placement_test_question_content_id === null) {
                return;
            }

            $questionContent = PlacementTestQuestionContent::query()
                ->find($contentSnapshot->placement_test_question_content_id);

            if (
                $questionContent === null
                || (int) $questionContent->placement_test_level_id !== (int) $contentSnapshot->placement_test_level_id
            ) {
                throw new LogicException('İçerik snapshot kaynağı aynı seviyedeki ortak içerik olmalıdır.');
            }
        });
    }

    public function levelResult(): BelongsTo
    {
        return $this->belongsTo(PlacementTestLevelResult::class, 'placement_test_level_result_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(PlacementTestLevel::class, 'placement_test_level_id');
    }

    public function questionContent(): BelongsTo
    {
        return $this->belongsTo(PlacementTestQuestionContent::class, 'placement_test_question_content_id');
    }

    public function levelQuestions(): HasMany
    {
        return $this->hasMany(PlacementTestLevelQuestion::class, 'placement_test_level_result_content_id');
    }
}
