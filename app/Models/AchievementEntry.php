<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class AchievementEntry extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const NAME_PERMISSION_UNKNOWN = 'unknown';

    public const NAME_PERMISSION_GRANTED = 'granted';

    public const NAME_PERMISSION_DENIED = 'denied';

    public const BRANCHES = ['ortaca', 'dalaman', 'koycegiz'];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'name_permission_status' => self::NAME_PERMISSION_UNKNOWN,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $achievementEntry): void {
            if (! in_array($achievementEntry->status, self::statuses(), true)) {
                throw new LogicException('Geçersiz başarı kaydı yayın durumu.');
            }

            if (! in_array($achievementEntry->name_permission_status, self::namePermissionStatuses(), true)) {
                throw new LogicException('Geçersiz isim yayın izni durumu.');
            }

            $fullName = trim((string) $achievementEntry->full_name);

            if ($fullName === '') {
                throw new LogicException('Öğrencinin gerçek adı admin kaydında zorunludur.');
            }

            if (
                $achievementEntry->sort_order !== null
                && (! is_numeric($achievementEntry->sort_order) || (int) $achievementEntry->sort_order < 1)
            ) {
                throw new LogicException('Başarı kaydı sıralaması en az 1 olmalıdır.');
            }

            $achievementEntry->full_name = $fullName;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'achievements_id',
        'full_name',
        'sort_order',
        'university_name',
        'department_name',
        'description',
        'branch',
        'card_sub_title',
        'status',
        'name_permission_status',
    ];

    /**
     * Keep private identity, consent, and verification notes out of accidental JSON serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'full_name',
        'name_permission_status',
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

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievements_id');
    }

    /**
     * Return the name that is allowed in public pages and shareable exports.
     * Consumers must never use full_name directly outside admin-only views.
     */
    public function publicDisplayName(): ?string
    {
        if ($this->name_permission_status !== self::NAME_PERMISSION_GRANTED) {
            return null;
        }

        $fullName = trim((string) $this->full_name);

        return $fullName === '' ? null : self::titleCasePublicName($fullName);
    }

    /**
     * Format public names without relying on the default Unicode casing rules,
     * which do not preserve Turkish dotted and dotless I characters correctly.
     */
    private static function titleCasePublicName(string $fullName): string
    {
        $lowercaseName = mb_strtolower(strtr($fullName, [
            'I' => 'ı',
            'İ' => 'i',
        ]), 'UTF-8');

        return preg_replace_callback(
            "/(^|[\\s\\-’'])(\\p{L})/u",
            static fn (array $matches): string => $matches[1].mb_strtoupper(strtr($matches[2], [
                'i' => 'İ',
                'ı' => 'I',
            ]), 'UTF-8'),
            $lowercaseName,
        ) ?? $lowercaseName;
    }

    /**
     * Limit a query to published entries belonging to published achievements.
     */
    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereHas('achievement', function (Builder $achievementQuery): void {
                $achievementQuery->where('status', Achievement::STATUS_PUBLISHED);
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
        ];
    }

    /**
     * @return list<string>
     */
    public static function namePermissionStatuses(): array
    {
        return [
            self::NAME_PERMISSION_UNKNOWN,
            self::NAME_PERMISSION_GRANTED,
            self::NAME_PERMISSION_DENIED,
        ];
    }
}
