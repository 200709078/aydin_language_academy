<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    private const TEMPORARY_PATH_PREFIX = '.profile-photo-migration/';

    /**
     * Move legacy public profile photos into the private disk.
     *
     * A public source is removed only after its private copy has a matching
     * fingerprint. An incomplete private copy is replaced only after a fresh
     * temporary copy and the public source have both been verified.
     */
    public function up(): void
    {
        $publicDisk = Storage::disk('public');
        $privateDisk = Storage::disk('local');

        DB::table('users')
            ->select(['id', 'profile_photo_path'])
            ->whereNotNull('profile_photo_path')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($publicDisk, $privateDisk): void {
                foreach ($users as $user) {
                    $path = $user->profile_photo_path;

                    if (! $this->isSafeProfilePhotoPath($path)) {
                        continue;
                    }

                    $publicExists = $publicDisk->exists($path);
                    $privateExists = $privateDisk->exists($path);

                    if (! $publicExists) {
                        continue;
                    }

                    if (! $privateExists || ! $this->filesMatch($publicDisk, $privateDisk, $path)) {
                        $this->copyToPrivateDisk($publicDisk, $privateDisk, $path);
                    }

                    if (! $this->filesMatch($publicDisk, $privateDisk, $path)) {
                        throw new RuntimeException('Profil fotoğrafı private diskte doğrulanamadı; public kaynak silinmedi.');
                    }

                    if (! $publicDisk->delete($path)) {
                        throw new RuntimeException('Doğrulanmış eski profil fotoğrafı public diskten kaldırılamadı.');
                    }
                }
            });
    }

    /**
     * Private files are intentionally retained on rollback.
     */
    public function down(): void
    {
        // No destructive file operation.
    }

    private function isSafeProfilePhotoPath(mixed $path): bool
    {
        if (
            ! is_string($path)
            || $path === ''
            || ! str_starts_with($path, 'profile-photos/')
            || str_starts_with($path, '/')
            || str_contains($path, "\\")
            || str_contains($path, "\0")
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }

    private function copyToPrivateDisk($publicDisk, $privateDisk, string $path): void
    {
        $sourceFingerprint = $this->fingerprint($publicDisk, $path);
        $temporaryPath = self::TEMPORARY_PATH_PREFIX.bin2hex(random_bytes(16)).'.part';
        $stream = null;

        try {
            $stream = $publicDisk->readStream($path);

            if (! is_resource($stream)) {
                throw new RuntimeException('Profil fotoğrafı public diskten okunamadı.');
            }

            $copied = $privateDisk->writeStream($temporaryPath, $stream, [
                'visibility' => 'private',
            ]);

            if (! $copied || ! $privateDisk->exists($temporaryPath)) {
                throw new RuntimeException('Profil fotoğrafı private diske kopyalanamadı.');
            }

            $temporaryFingerprint = $this->fingerprint($privateDisk, $temporaryPath);
            $currentSourceFingerprint = $this->fingerprint($publicDisk, $path);

            if (
                ! $this->fingerprintsMatch($sourceFingerprint, $temporaryFingerprint)
                || ! $this->fingerprintsMatch($sourceFingerprint, $currentSourceFingerprint)
            ) {
                throw new RuntimeException('Profil fotoğrafı kopyalama sırasında değişti; public kaynak silinmedi.');
            }

            if ($privateDisk->exists($path) && ! $privateDisk->delete($path)) {
                throw new RuntimeException('Geçersiz private profil fotoğrafı kaldırılamadı.');
            }

            if (! $privateDisk->move($temporaryPath, $path)) {
                throw new RuntimeException('Doğrulanmış profil fotoğrafı private hedefe taşınamadı.');
            }

            $temporaryPath = null;

            if (! $this->fingerprintsMatch($sourceFingerprint, $this->fingerprint($privateDisk, $path))) {
                throw new RuntimeException('Profil fotoğrafı private hedefte doğrulanamadı.');
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($temporaryPath !== null) {
                try {
                    $privateDisk->delete($temporaryPath);
                } catch (\Throwable) {
                    // The random command-owned temporary path can be cleaned up later.
                }
            }
        }
    }

    private function filesMatch($publicDisk, $privateDisk, string $path): bool
    {
        return $this->fingerprintsMatch(
            $this->fingerprint($publicDisk, $path),
            $this->fingerprint($privateDisk, $path),
        );
    }

    /**
     * @return array{size: int, sha256: string}
     */
    private function fingerprint($disk, string $path): array
    {
        $stream = $disk->readStream($path);

        if (! is_resource($stream)) {
            throw new RuntimeException('Profil fotoğrafı stream olarak okunamadı.');
        }

        $context = hash_init('sha256');
        $size = 0;

        try {
            while (! feof($stream)) {
                $chunk = fread($stream, 1024 * 1024);

                if ($chunk === false || ($chunk === '' && ! feof($stream))) {
                    throw new RuntimeException('Profil fotoğrafı okunurken beklenmeyen bir hata oluştu.');
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
};
