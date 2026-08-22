<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class PlacementTestQuestionContent extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $content): void {
            if (! in_array($content->type, ['text', 'audio', 'image', 'video'], true)) {
                throw new LogicException('Geçersiz ortak içerik türü.');
            }

            if ($content->type === 'text') {
                if (trim((string) $content->text_content) === '' || $content->media_disk !== null || $content->media_path !== null) {
                    throw new LogicException('Metin ortak içeriği yalnız boş olmayan metin taşımalıdır.');
                }
            } else {
                $mediaPath = trim((string) $content->media_path);

                if (
                    $content->text_content !== null
                    || trim((string) $content->media_disk) === ''
                    || $mediaPath === ''
                    || str_contains(strtolower($mediaPath), '://')
                    || str_starts_with($mediaPath, '//')
                ) {
                    throw new LogicException('Medya ortak içeriği yalnız geçerli bir sunucu dosya yolu taşımalıdır.');
                }
            }

            if (PlacementTestLevel::query()
                ->whereKey($content->placement_test_level_id)
                ->where('has_exam', false)
                ->exists()) {
                throw new LogicException('C2 seviyesine ortak içerik atanamaz.');
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
