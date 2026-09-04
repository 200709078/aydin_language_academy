<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

class News extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const DISPLAY_NONE = 'none';

    public const DISPLAY_HOMEPAGE = 'homepage';

    public const DISPLAY_HERO = 'hero';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'display_location' => self::DISPLAY_NONE,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $news): void {
            if (! in_array($news->status, self::statuses(), true)) {
                throw new LogicException('Geçersiz haber yayın durumu.');
            }

            if (! in_array($news->display_location, self::displayLocations(), true)) {
                throw new LogicException('Geçersiz haber gösterim konumu.');
            }

            if ($news->status === self::STATUS_PUBLISHED && $news->published_at === null) {
                throw new LogicException('Yayımlanmış haberin yayın tarihi zorunludur.');
            }

            if ($news->unpublished_at !== null && $news->published_at === null) {
                throw new LogicException('Yayından kaldırma tarihi için yayın tarihi gerekir.');
            }

            if (
                $news->published_at !== null
                && $news->unpublished_at !== null
                && $news->unpublished_at->lessThanOrEqualTo($news->published_at)
            ) {
                throw new LogicException('Yayından kaldırma tarihi yayın tarihinden sonra olmalıdır.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'cover_media_asset_id',
        'status',
        'published_at',
        'unpublished_at',
        'display_location',
        'sort_order',
        'author_id',
        'published_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'unpublished_at' => 'datetime',
        ];
    }

    public function coverMediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_asset_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function contentBlocks(): HasMany
    {
        return $this->hasMany(NewsContentBlock::class)->orderBy('position');
    }

    public function activeContentBlocks(): HasMany
    {
        return $this->hasMany(NewsContentBlock::class)
            ->where('is_active', true)
            ->orderBy('position');
    }

    /**
     * Limit a query to news that may be shown publicly at this moment.
     */
    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $query): void {
                $query->whereNull('unpublished_at')
                    ->orWhere('unpublished_at', '>', now());
            });
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
            self::STATUS_ARCHIVED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function displayLocations(): array
    {
        return [
            self::DISPLAY_NONE,
            self::DISPLAY_HOMEPAGE,
            self::DISPLAY_HERO,
        ];
    }
}
