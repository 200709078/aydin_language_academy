<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class Campaign extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const LINK_TYPE_NONE = 'none';

    public const LINK_TYPE_INTERNAL = 'internal';

    public const LINK_TYPE_EXTERNAL = 'external';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string|int>
     */
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'link_type' => self::LINK_TYPE_NONE,
        'sort_order' => 1,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $campaign): void {
            if (! in_array($campaign->status, self::statuses(), true)) {
                throw new LogicException('Geçersiz kampanya yayın durumu.');
            }

            if (! in_array($campaign->link_type, self::linkTypes(), true)) {
                throw new LogicException('Geçersiz kampanya bağlantı türü.');
            }

            foreach (['title_tr', 'title_en', 'description_tr', 'description_en'] as $attribute) {
                $value = trim((string) $campaign->{$attribute});

                if ($value === '') {
                    throw new LogicException('Kampanya başlığı ve açıklaması her iki dilde de zorunludur.');
                }

                $campaign->{$attribute} = $value;
            }

            if (! is_numeric($campaign->sort_order) || (int) $campaign->sort_order < 1) {
                throw new LogicException('Kampanya sıralaması en az 1 olmalıdır.');
            }

            $campaign->sort_order = (int) $campaign->sort_order;
            $campaign->normalizeLink();
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
        'link_type',
        'internal_destination',
        'external_url',
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

    /**
     * Limit a query to campaigns that can be shown publicly.
     */
    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function getLocalizedTitleAttribute(): string
    {
        return $this->titleForLocale(app()->getLocale());
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return $this->descriptionForLocale(app()->getLocale());
    }

    public function titleForLocale(string $locale): string
    {
        return (string) $this->{self::localizedColumn('title', $locale)};
    }

    public function descriptionForLocale(string $locale): string
    {
        return (string) $this->{self::localizedColumn('description', $locale)};
    }

    /**
     * Return the only URL type accepted for the selected link configuration.
     */
    public function publicLinkUrl(): ?string
    {
        if ($this->link_type === self::LINK_TYPE_INTERNAL && self::isAllowedInternalDestination($this->internal_destination)) {
            return route($this->internal_destination);
        }

        if ($this->link_type === self::LINK_TYPE_EXTERNAL && self::isSecureExternalUrl((string) $this->external_url)) {
            return $this->external_url;
        }

        return null;
    }

    public function opensInNewWindow(): bool
    {
        return $this->link_type === self::LINK_TYPE_EXTERNAL && $this->publicLinkUrl() !== null;
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
    public static function linkTypes(): array
    {
        return [
            self::LINK_TYPE_NONE,
            self::LINK_TYPE_INTERNAL,
            self::LINK_TYPE_EXTERNAL,
        ];
    }

    /**
     * A fixed whitelist prevents campaigns from becoming an open redirect into admin or member routes.
     *
     * @return array<string, string>
     */
    public static function internalDestinations(): array
    {
        return [
            'home' => __('dictt.home'),
            'frontend.achievements' => __('dictt.achievements'),
            'frontend.campaigns' => __('dictt.campaigns'),
            'frontend.news.index' => __('dictt.news'),
            'frontend.reviews' => __('dictt.reviews'),
            'frontend.placement-test' => __('dictt.placement_test'),
            'frontend.program-finder' => __('dictt.program_finder_title'),
            'frontend.courses.preschool' => __('dictt.ourcourses').' — '.__('dictt.preschool'),
            'frontend.courses.primary-school' => __('dictt.ourcourses').' — '.__('dictt.primary_school'),
            'frontend.courses.middle-school' => __('dictt.ourcourses').' — '.__('dictt.middle_school'),
            'frontend.courses.high-school' => __('dictt.ourcourses').' — '.__('dictt.high_school'),
            'frontend.courses.general-english' => __('dictt.ourcourses').' — '.__('dictt.general_english'),
            'frontend.courses.ielts' => __('dictt.ourcourses').' — '.__('dictt.ielts_prep'),
            'frontend.courses.yks-dil' => __('dictt.ourcourses').' — '.__('dictt.yks_dil_prep'),
            'frontend.courses.yds-yokdil' => __('dictt.ourcourses').' — '.__('dictt.yds_yokdil'),
            'frontend.courses.toefl' => __('dictt.ourcourses').' — '.__('dictt.toefl'),
            'frontend.courses.pte-academic' => __('dictt.ourcourses').' — '.__('dictt.pte_academic'),
            'frontend.courses.test-of-english' => __('dictt.ourcourses').' — '.__('dictt.test_of_english'),
            'frontend.courses.sat' => __('dictt.ourcourses').' — '.__('dictt.sat'),
            'frontend.courses.speaking-clubs' => __('dictt.ourcourses').' — '.__('dictt.speaking_clubs'),
            'frontend.branches' => __('dictt.branches'),
            'frontend.contact' => __('dictt.contact'),
        ];
    }

    public static function isAllowedInternalDestination(mixed $destination): bool
    {
        return is_string($destination) && array_key_exists($destination, self::internalDestinations());
    }

    private function normalizeLink(): void
    {
        $internalDestination = trim((string) $this->internal_destination);
        $externalUrl = trim((string) $this->external_url);

        if ($this->link_type === self::LINK_TYPE_NONE) {
            $this->internal_destination = null;
            $this->external_url = null;

            return;
        }

        if ($this->link_type === self::LINK_TYPE_INTERNAL) {
            if (! self::isAllowedInternalDestination($internalDestination)) {
                throw new LogicException('Kampanya için yalnız izinli bir site içi hedef seçilebilir.');
            }

            $this->internal_destination = $internalDestination;
            $this->external_url = null;

            return;
        }

        if (! self::isSecureExternalUrl($externalUrl)) {
            throw new LogicException('Kampanya harici bağlantısı geçerli bir HTTPS URL olmalıdır.');
        }

        $this->internal_destination = null;
        $this->external_url = $externalUrl;
    }

    private static function localizedColumn(string $baseColumn, string $locale): string
    {
        return $baseColumn.'_'.($locale === 'en' ? 'en' : 'tr');
    }

    private static function isSecureExternalUrl(string $url): bool
    {
        $parts = parse_url($url);

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && strtolower((string) ($parts['scheme'] ?? '')) === 'https'
            && isset($parts['host']);
    }
}
