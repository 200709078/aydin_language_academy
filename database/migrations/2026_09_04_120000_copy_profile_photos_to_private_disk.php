<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Move legacy public profile photos into the private disk.
     *
     * A public source is removed only after its private copy has matching size
     * and checksum. Interrupted runs are safe to retry: a verified private
     * copy is enough to remove the still-public source on the next run.
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

                    if (! $privateExists) {
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
        $stream = $publicDisk->readStream($path);

        if (! is_resource($stream)) {
            throw new RuntimeException('Profil fotoğrafı public diskten okunamadı.');
        }

        try {
            $copied = $privateDisk->writeStream($path, $stream, [
                'visibility' => 'private',
            ]);
        } finally {
            fclose($stream);
        }

        if (! $copied || ! $privateDisk->exists($path)) {
            throw new RuntimeException('Profil fotoğrafı private diske kopyalanamadı.');
        }
    }

    private function filesMatch($publicDisk, $privateDisk, string $path): bool
    {
        if ($publicDisk->size($path) !== $privateDisk->size($path)) {
            return false;
        }

        $publicChecksum = $publicDisk->checksum($path);
        $privateChecksum = $privateDisk->checksum($path);

        return is_string($publicChecksum)
            && is_string($privateChecksum)
            && hash_equals($publicChecksum, $privateChecksum);
    }
};
