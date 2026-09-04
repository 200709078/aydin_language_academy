<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class NewsContentBlock extends Model
{
    public const TYPE_RICH_TEXT = 'rich_text';

    public const TYPE_IMAGE = 'image';

    public const TYPE_AUDIO = 'audio';

    public const TYPE_VIDEO = 'video';

    public const TYPE_FILE = 'file';

    public const TYPE_EXTERNAL_LINK = 'external_link';

    public const TYPE_INTERNAL_LINK = 'internal_link';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string|bool>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $block): void {
            if (! in_array($block->type, self::types(), true)) {
                throw new LogicException('Geçersiz haber içerik türü.');
            }

            if (! is_numeric($block->position) || (int) $block->position < 1) {
                throw new LogicException('Haber içerik sırası pozitif olmalıdır.');
            }

            $hasMediaAsset = $block->media_asset_id !== null && $block->media_asset_id !== '';

            if ($hasMediaAsset && (! is_numeric($block->media_asset_id) || (int) $block->media_asset_id < 1)) {
                throw new LogicException('Geçerli bir medya kaydı seçilmelidir.');
            }

            if (! $hasMediaAsset) {
                $block->media_asset_id = null;
            }

            $externalUrl = trim((string) $block->external_url);
            $hasExternalUrl = $externalUrl !== '';

            if ($hasExternalUrl && ! self::isSecureExternalUrl($externalUrl)) {
                throw new LogicException('Harici içerik bağlantısı geçerli bir HTTPS URL olmalıdır.');
            }

            $block->external_url = $hasExternalUrl ? $externalUrl : null;

            $internalDestination = trim((string) $block->internal_destination);

            if ($block->type === self::TYPE_INTERNAL_LINK) {
                if (
                    ! Campaign::isAllowedInternalDestination($internalDestination)
                    || $hasMediaAsset
                    || $hasExternalUrl
                ) {
                    throw new LogicException('Site içi bağlantı bloğu yalnız izinli bir site içi hedef içermelidir.');
                }

                $block->internal_destination = $internalDestination;

                return;
            }

            $block->internal_destination = null;

            if ($block->type === self::TYPE_RICH_TEXT) {
                if (trim((string) $block->body) === '' || $hasMediaAsset || $hasExternalUrl) {
                    throw new LogicException('Metin bloğu yalnız boş olmayan metin içermelidir.');
                }

                return;
            }

            if ($block->type === self::TYPE_EXTERNAL_LINK) {
                if (! $hasExternalUrl || $hasMediaAsset) {
                    throw new LogicException('Harici bağlantı bloğu yalnız HTTPS bağlantısı içermelidir.');
                }

                return;
            }

            if ($hasMediaAsset === $hasExternalUrl) {
                throw new LogicException('Medya bloğu için yalnız bir dosya veya bir harici bağlantı seçilmelidir.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'news_id',
        'position',
        'type',
        'heading',
        'body',
        'media_asset_id',
        'external_url',
        'internal_destination',
        'link_label',
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

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    /**
     * Return a public URL only when this block uses one of Campaign's approved internal destinations.
     */
    public function publicInternalLinkUrl(): ?string
    {
        if (
            $this->type !== self::TYPE_INTERNAL_LINK
            || ! Campaign::isAllowedInternalDestination($this->internal_destination)
        ) {
            return null;
        }

        return route($this->internal_destination);
    }

    public function internalDestinationLabel(): ?string
    {
        if (! Campaign::isAllowedInternalDestination($this->internal_destination)) {
            return null;
        }

        return Campaign::internalDestinations()[$this->internal_destination];
    }

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_RICH_TEXT,
            self::TYPE_IMAGE,
            self::TYPE_AUDIO,
            self::TYPE_VIDEO,
            self::TYPE_FILE,
            self::TYPE_EXTERNAL_LINK,
            self::TYPE_INTERNAL_LINK,
        ];
    }

    private static function isSecureExternalUrl(string $url): bool
    {
        $parts = parse_url($url);

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && ($parts['scheme'] ?? null) === 'https'
            && isset($parts['host']);
    }
}
