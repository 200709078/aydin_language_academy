<?php

namespace App\Console\Commands;

use App\Models\AchievementPageSetting;
use App\Models\CampaignPageSetting;
use App\Models\MediaAsset;
use App\Models\News;
use App\Models\NewsContentBlock;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class PublishCmsMediaAssets extends Command
{
    private const SOURCE_DISK = 'local';

    private const TARGET_DISK = 'public';

    private const TEMPORARY_PATH_PREFIX = '.cms-media-publication/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media-assets:publish-cms
        {--dry-run : Dosya veya veritabanı değiştirmeden yapılacak işlemleri denetler}
        {--chunk=100 : Bir seferde işlenecek kayıt sayısı}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bağlı CMS medya dosyalarını private local diskten public diske doğrulayarak kopyalar.';

    /**
     * Copy only media attached to a public-site CMS record. Orphan MediaAsset
     * rows and placement-test media are deliberately outside this command.
     */
    public function handle(): int
    {
        $chunkSize = $this->chunkSize();

        if ($chunkSize === null) {
            $this->error('--chunk en az 1 olan bir tam sayı olmalıdır.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $summary = [
            'candidates' => 0,
            'migrated' => 0,
            'would_migrate' => 0,
            'blocked' => 0,
        ];

        $this->info($dryRun
            ? 'Dry-run: bağlı CMS medya dosyaları yalnız denetleniyor.'
            : 'Bağlı CMS medya dosyaları public diske kopyalanıyor. Private kaynak dosyalar silinmeyecek.');

        $this->eligibleMediaAssets()
            ->lazyById($chunkSize)
            ->each(function (MediaAsset $mediaAsset) use ($dryRun, &$summary): void {
                $summary['candidates']++;
                $result = $this->publish($mediaAsset, $dryRun);

                $summary[$result['outcome']]++;

                $prefix = sprintf('[%d] %s', $mediaAsset->getKey(), $result['message']);

                if ($result['outcome'] === 'blocked') {
                    $this->warn($prefix);

                    return;
                }

                $this->line($prefix);
            });

        if ($summary['candidates'] === 0) {
            $this->info('Uygun private CMS medya kaydı bulunamadı.');
        }

        $this->newLine();
        $this->table(
            ['Sonuç', 'Adet'],
            [
                ['Aday', $summary['candidates']],
                [$dryRun ? 'Kopyalanacak' : 'Kopyalandı', $dryRun ? $summary['would_migrate'] : $summary['migrated']],
                ['Engellendi', $summary['blocked']],
            ],
        );

        if ($summary['blocked'] > 0) {
            $this->error('Engellenen kayıtlar düzeltilmeden ilgili medya için PHP-servisinden bağımsız public yayın geçişi tamamlanmış sayılmaz.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array{outcome: 'migrated'|'would_migrate'|'blocked', message: string}
     */
    private function publish(MediaAsset $mediaAsset, bool $dryRun): array
    {
        $path = trim((string) $mediaAsset->path);

        if (! $this->isSafeRelativePath($path)) {
            return $this->blocked('Geçersiz veya güvenli olmayan dosya yolu; atlandı.');
        }

        try {
            $sourceDisk = Storage::disk(self::SOURCE_DISK);

            if (! $sourceDisk->exists($path)) {
                return $this->blocked('Private kaynak dosya bulunamadı; veritabanı kaydı değiştirilmedi.');
            }

            $sourceFingerprint = $this->fingerprint(self::SOURCE_DISK, $path);
        } catch (Throwable $exception) {
            return $this->blocked('Private kaynak dosya okunamadı: '.$exception->getMessage());
        }

        if ($sourceFingerprint['size'] !== (int) $mediaAsset->size_bytes) {
            return $this->blocked(sprintf(
                'Kaynak boyutu kayıtlı boyutla uyuşmuyor (%d / %d bayt); atlandı.',
                $sourceFingerprint['size'],
                (int) $mediaAsset->size_bytes,
            ));
        }

        $checksum = trim((string) $mediaAsset->checksum);

        if ($checksum !== '' && ! hash_equals(strtolower($checksum), $sourceFingerprint['sha256'])) {
            return $this->blocked('Kaynak SHA-256 değeri kayıtlı checksum ile uyuşmuyor; atlandı.');
        }

        if ($this->hasPublicMediaAssetAtPath($path, (int) $mediaAsset->getKey())) {
            return $this->blocked('Aynı public disk/yol için başka bir MediaAsset kaydı var; güvenli birleştirme yapılmadı.');
        }

        try {
            $targetDisk = Storage::disk(self::TARGET_DISK);
            $targetExists = $targetDisk->exists($path);
        } catch (Throwable $exception) {
            return $this->blocked('Public disk denetlenemedi: '.$exception->getMessage());
        }

        if ($targetExists) {
            try {
                $targetFingerprint = $this->fingerprint(self::TARGET_DISK, $path);
            } catch (Throwable $exception) {
                return $this->blocked('Mevcut public hedef dosya okunamadı: '.$exception->getMessage());
            }

            if (! $this->fingerprintsMatch($sourceFingerprint, $targetFingerprint)) {
                return $this->blocked('Aynı yolda farklı bir public dosya var; üzerine yazılmadı.');
            }

            if ($dryRun) {
                return $this->wouldMigrate('Doğrulanmış public kopya mevcut; yalnız MediaAsset kaydı public yapılacak.');
            }

            return $this->markAsPublic($mediaAsset, $path, $sourceFingerprint)
                ? $this->migrated('Doğrulanmış mevcut public kopya kullanıldı; MediaAsset kaydı public yapıldı.')
                : $this->blocked('MediaAsset kaydı public yapılamadı.');
        }

        if ($dryRun) {
            return $this->wouldMigrate('Kaynak doğrulandı; public diske kopyalanıp MediaAsset kaydı public yapılacak.');
        }

        $createdTarget = false;

        try {
            $createdTarget = $this->copyAndVerify($path, $sourceFingerprint);

            if (! $this->markAsPublic($mediaAsset, $path, $sourceFingerprint)) {
                throw new RuntimeException('MediaAsset kaydı public yapılamadı.');
            }
        } catch (Throwable $exception) {
            if ($createdTarget) {
                $this->removeUnreferencedTargetCreatedByThisCommand($path, $sourceFingerprint);
            }

            return $this->blocked('Public geçiş tamamlanamadı: '.$exception->getMessage());
        }

        return $this->migrated('Dosya public diske doğrulanarak kopyalandı; MediaAsset kaydı public yapıldı.');
    }

    /**
     * Copy through a temporary path so an interrupted upload cannot leave a
     * partial file at the public URL.
     *
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     */
    private function copyAndVerify(string $path, array $sourceFingerprint): bool
    {
        $temporaryPath = self::TEMPORARY_PATH_PREFIX.bin2hex(random_bytes(16)).'.part';
        $sourceStream = null;

        try {
            $sourceStream = Storage::disk(self::SOURCE_DISK)->readStream($path);

            if (! is_resource($sourceStream)) {
                throw new RuntimeException('Private kaynak için okunabilir bir stream açılamadı.');
            }

            $written = Storage::disk(self::TARGET_DISK)->writeStream($temporaryPath, $sourceStream, [
                'visibility' => 'public',
            ]);

            if ($written !== true) {
                throw new RuntimeException('Geçici public dosya yazılamadı.');
            }

            $temporaryFingerprint = $this->fingerprint(self::TARGET_DISK, $temporaryPath);

            if (! $this->fingerprintsMatch($sourceFingerprint, $temporaryFingerprint)) {
                throw new RuntimeException('Geçici public dosyanın boyutu veya SHA-256 değeri kaynakla uyuşmuyor.');
            }

            $targetDisk = Storage::disk(self::TARGET_DISK);

            if ($targetDisk->exists($path)) {
                $targetFingerprint = $this->fingerprint(self::TARGET_DISK, $path);

                if (! $this->fingerprintsMatch($sourceFingerprint, $targetFingerprint)) {
                    throw new RuntimeException('Kopyalama sırasında aynı public yolda farklı bir dosya oluştu.');
                }

                return false;
            }

            if (! $targetDisk->move($temporaryPath, $path)) {
                throw new RuntimeException('Doğrulanmış geçici dosya public hedefe taşınamadı.');
            }

            $temporaryPath = null;
            $targetFingerprint = $this->fingerprint(self::TARGET_DISK, $path);

            if (! $this->fingerprintsMatch($sourceFingerprint, $targetFingerprint)) {
                throw new RuntimeException('Public hedef dosyanın boyutu veya SHA-256 değeri kaynakla uyuşmuyor.');
            }

            return true;
        } finally {
            if (is_resource($sourceStream)) {
                fclose($sourceStream);
            }

            if ($temporaryPath !== null) {
                try {
                    Storage::disk(self::TARGET_DISK)->delete($temporaryPath);
                } catch (Throwable) {
                    // The random, command-owned temporary path can be cleaned up later.
                }
            }
        }
    }

    /**
     * Update only after the target file is fully verified. Re-checking the
     * candidate under a row lock avoids publishing a record that changed while
     * a larger file was being copied.
     *
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     */
    private function markAsPublic(MediaAsset $mediaAsset, string $path, array $sourceFingerprint): bool
    {
        return DB::transaction(function () use ($mediaAsset, $path, $sourceFingerprint): bool {
            $lockedMediaAsset = MediaAsset::query()
                ->lockForUpdate()
                ->find($mediaAsset->getKey());

            if (! $lockedMediaAsset instanceof MediaAsset) {
                throw new RuntimeException('MediaAsset kaydı kopyalama sırasında silindi.');
            }

            if (! $this->eligibleMediaAssets()->whereKey($lockedMediaAsset->getKey())->exists()) {
                throw new RuntimeException('MediaAsset kaydı veya CMS bağlantısı kopyalama sırasında değişti.');
            }

            $lockedPath = trim((string) $lockedMediaAsset->path);
            $lockedChecksum = trim((string) $lockedMediaAsset->checksum);

            if (
                $lockedMediaAsset->disk !== self::SOURCE_DISK
                || $lockedMediaAsset->visibility !== MediaAsset::VISIBILITY_PRIVATE
                || $lockedPath !== $path
                || $lockedMediaAsset->path_hash !== $this->pathHash(self::SOURCE_DISK, $path)
                || (int) $lockedMediaAsset->size_bytes !== $sourceFingerprint['size']
                || ($lockedChecksum !== '' && ! hash_equals(strtolower($lockedChecksum), $sourceFingerprint['sha256']))
            ) {
                throw new RuntimeException('MediaAsset kaynağı kopyalama sırasında değişti.');
            }

            if (! $this->fingerprintsMatch($sourceFingerprint, $this->fingerprint(self::SOURCE_DISK, $path))) {
                throw new RuntimeException('Private kaynak dosya kopyalama sırasında değişti.');
            }

            if ($this->hasPublicMediaAssetAtPath($path, (int) $lockedMediaAsset->getKey())) {
                throw new RuntimeException('Aynı public disk/yol için başka bir MediaAsset kaydı oluştu.');
            }

            $lockedMediaAsset->forceFill([
                'disk' => self::TARGET_DISK,
                'visibility' => MediaAsset::VISIBILITY_PUBLIC,
            ])->save();

            return true;
        });
    }

    /**
     * Remove only a final target that this command created and that is still
     * unreferenced in the database. Private source files are never deleted.
     *
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     */
    private function removeUnreferencedTargetCreatedByThisCommand(string $path, array $sourceFingerprint): void
    {
        try {
            if ($this->hasPublicMediaAssetAtPath($path)) {
                return;
            }

            $targetDisk = Storage::disk(self::TARGET_DISK);

            if (! $targetDisk->exists($path)) {
                return;
            }

            $targetFingerprint = $this->fingerprint(self::TARGET_DISK, $path);

            if ($this->fingerprintsMatch($sourceFingerprint, $targetFingerprint)) {
                $targetDisk->delete($path);
            }
        } catch (Throwable) {
            // Keep an auditable final failure instead of risking deletion of an unknown file.
        }
    }

    /**
     * @return Builder<MediaAsset>
     */
    private function eligibleMediaAssets(): Builder
    {
        return MediaAsset::query()
            ->where('disk', self::SOURCE_DISK)
            ->where('visibility', MediaAsset::VISIBILITY_PRIVATE)
            ->where(function (Builder $query): void {
                $query
                    ->whereIn('id', News::query()
                        ->select('cover_media_asset_id')
                        ->whereNotNull('cover_media_asset_id'))
                    ->orWhereIn('id', NewsContentBlock::query()
                        ->select('media_asset_id')
                        ->whereNotNull('media_asset_id'))
                    ->orWhereIn('id', CampaignPageSetting::query()
                        ->select('hero_media_asset_id')
                        ->whereNotNull('hero_media_asset_id'))
                    ->orWhereIn('id', AchievementPageSetting::query()
                        ->select('hero_media_asset_id')
                        ->whereNotNull('hero_media_asset_id'));
            });
    }

    private function hasPublicMediaAssetAtPath(string $path, ?int $exceptMediaAssetId = null): bool
    {
        return MediaAsset::query()
            ->where('disk', self::TARGET_DISK)
            ->where('path_hash', $this->pathHash(self::TARGET_DISK, $path))
            ->when(
                $exceptMediaAssetId !== null,
                fn (Builder $query): Builder => $query->whereKeyNot($exceptMediaAssetId),
            )
            ->exists();
    }

    /**
     * @return array{size: int, sha256: string}
     */
    private function fingerprint(string $disk, string $path): array
    {
        $stream = Storage::disk($disk)->readStream($path);

        if (! is_resource($stream)) {
            throw new RuntimeException('Dosya stream olarak açılamadı.');
        }

        $context = hash_init('sha256');
        $size = 0;

        try {
            while (! feof($stream)) {
                $chunk = fread($stream, 1024 * 1024);

                if ($chunk === false || ($chunk === '' && ! feof($stream))) {
                    throw new RuntimeException('Dosya okunurken beklenmeyen bir hata oluştu.');
                }

                $size += strlen($chunk);
                hash_update($context, $chunk);
            }
        } finally {
            fclose($stream);
        }

        return [
            'size' => $size,
            'sha256' => hash_final($context),
        ];
    }

    /**
     * @param  array{size: int, sha256: string}  $left
     * @param  array{size: int, sha256: string}  $right
     */
    private function fingerprintsMatch(array $left, array $right): bool
    {
        return $left['size'] === $right['size']
            && hash_equals($left['sha256'], $right['sha256']);
    }

    private function pathHash(string $disk, string $path): string
    {
        return hash('sha256', $disk."\0".$path);
    }

    private function isSafeRelativePath(string $path): bool
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

    private function chunkSize(): ?int
    {
        $value = filter_var($this->option('chunk'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        return $value === false ? null : $value;
    }

    /**
     * @return array{outcome: 'migrated', message: string}
     */
    private function migrated(string $message): array
    {
        return ['outcome' => 'migrated', 'message' => $message];
    }

    /**
     * @return array{outcome: 'would_migrate', message: string}
     */
    private function wouldMigrate(string $message): array
    {
        return ['outcome' => 'would_migrate', 'message' => $message];
    }

    /**
     * @return array{outcome: 'blocked', message: string}
     */
    private function blocked(string $message): array
    {
        return ['outcome' => 'blocked', 'message' => $message];
    }
}
