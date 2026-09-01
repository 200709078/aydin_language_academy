<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class AchievementPageSetting extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $achievementPageSetting): void {
            foreach (['title_tr', 'title_en', 'description_tr', 'description_en'] as $attribute) {
                $value = trim((string) $achievementPageSetting->{$attribute});

                if ($value === '') {
                    throw new LogicException('Başarılarımız sayfası başlığı ve açıklaması her iki dilde de zorunludur.');
                }

                $achievementPageSetting->{$attribute} = $value;
            }

            if (
                $achievementPageSetting->hero_media_asset_id !== null
                && (! is_numeric($achievementPageSetting->hero_media_asset_id) || (int) $achievementPageSetting->hero_media_asset_id < 1)
            ) {
                throw new LogicException('Geçerli bir Başarılarımız sayfası görseli seçilmelidir.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title_tr',
        'title_en',
        'description_tr',
        'description_en',
        'hero_media_asset_id',
    ];

    public function heroMediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'hero_media_asset_id');
    }

    public function getLocalizedTitleAttribute(): string
    {
        return $this->titleForLocale(app()->getLocale());
    }

    public function titleForLocale(string $locale): string
    {
        return (string) $this->{self::localizedColumn('title', $locale)};
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return $this->descriptionForLocale(app()->getLocale());
    }

    public function descriptionForLocale(string $locale): string
    {
        return (string) $this->{self::localizedColumn('description', $locale)};
    }

    private static function localizedColumn(string $baseColumn, string $locale): string
    {
        return $baseColumn.'_'.($locale === 'en' ? 'en' : 'tr');
    }
}
