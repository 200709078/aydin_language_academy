<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use LogicException;

class MediaAsset extends Model
{
    public const KIND_IMAGE = 'image';

    public const KIND_AUDIO = 'audio';

    public const KIND_VIDEO = 'video';

    public const KIND_FILE = 'file';

    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITY_PUBLIC = 'public';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'visibility' => self::VISIBILITY_PUBLIC,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $mediaAsset): void {
            if (! in_array($mediaAsset->kind, self::kinds(), true)) {
                throw new LogicException('Geçersiz medya türü.');
            }

            if (! in_array($mediaAsset->visibility, self::visibilities(), true)) {
                throw new LogicException('Geçersiz medya görünürlüğü.');
            }

            $disk = trim((string) $mediaAsset->disk);
            $path = trim((string) $mediaAsset->path);

            if ($disk === '' || ! self::isSafeStoragePath($path)) {
                throw new LogicException('Medya için geçerli bir sunucu dosya yolu gerekir.');
            }

            if (trim((string) $mediaAsset->original_filename) === '' || trim((string) $mediaAsset->mime_type) === '') {
                throw new LogicException('Medyanın özgün dosya adı ve MIME türü zorunludur.');
            }

            if (! is_numeric($mediaAsset->size_bytes) || (int) $mediaAsset->size_bytes < 0) {
                throw new LogicException('Medya dosya boyutu negatif olamaz.');
            }

            foreach (['width', 'height'] as $attribute) {
                if ($mediaAsset->{$attribute} !== null && (int) $mediaAsset->{$attribute} < 0) {
                    throw new LogicException('Medya ölçü değerleri negatif olamaz.');
                }
            }

            $mediaAsset->disk = $disk;
            $mediaAsset->path = $path;
            $mediaAsset->path_hash = hash('sha256', $disk."\0".$path);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'disk',
        'path',
        'kind',
        'visibility',
        'original_filename',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'checksum',
        'uploaded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function coverNews(): HasMany
    {
        return $this->hasMany(News::class, 'cover_media_asset_id');
    }

    public function newsContentBlocks(): HasMany
    {
        return $this->hasMany(NewsContentBlock::class);
    }

    /**
     * Return the direct, web-server-served URL for a public media asset.
     *
     * Public site media must live on Laravel's public disk so the web server
     * can serve it from /storage without a PHP controller in the request path.
     */
    public function publicUrl(): string
    {
        $path = trim((string) $this->path);

        if (
            $this->visibility !== self::VISIBILITY_PUBLIC
            || $this->disk !== 'public'
            || ! self::isSafeStoragePath($path)
        ) {
            throw new LogicException('Yalnız public diskteki public medya için doğrudan URL üretilebilir.');
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * @return list<string>
     */
    public static function kinds(): array
    {
        return [
            self::KIND_IMAGE,
            self::KIND_AUDIO,
            self::KIND_VIDEO,
            self::KIND_FILE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function visibilities(): array
    {
        return [
            self::VISIBILITY_PRIVATE,
            self::VISIBILITY_PUBLIC,
        ];
    }

    private static function isSafeStoragePath(string $path): bool
    {
        if (
            $path === ''
            || str_contains(strtolower($path), '://')
            || str_starts_with($path, '/')
            || str_starts_with($path, '//')
            || str_contains($path, '\\')
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }
}
