<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class Achievement extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $achievement): void {
            if (! in_array($achievement->status, self::statuses(), true)) {
                throw new LogicException('Geçersiz başarı yayın durumu.');
            }

            $title = trim((string) $achievement->title);

            if ($title === '') {
                throw new LogicException('Başarı başlığı zorunludur.');
            }

            if (
                $achievement->sort_order !== null
                && (! is_numeric($achievement->sort_order) || (int) $achievement->sort_order < 1)
            ) {
                throw new LogicException('Başarı sıralaması en az 1 olmalıdır.');
            }

            $achievement->title = $title;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(AchievementEntry::class, 'achievements_id');
    }

    /**
     * Return student records that can be shown with this public achievement.
     */
    public function publicEntries(): HasMany
    {
        return $this->entries()
            ->where('status', AchievementEntry::STATUS_PUBLISHED)
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Limit a query to achievements that can be shown publicly.
     */
    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
        ];
    }
}
