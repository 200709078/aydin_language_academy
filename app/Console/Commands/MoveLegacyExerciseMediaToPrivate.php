<?php

namespace App\Console\Commands;

use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Models\model_themes;
use App\Support\LegacyExerciseMedia;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MoveLegacyExerciseMediaToPrivate extends Command
{
    private const TEMPORARY_PATH_PREFIX = 'legacy-exercise/.private-migration-temp/';

    /**
     * @var array<string, array{model: class-string<Model>, field: string, public_directory: string, private_directory: string, label: string}>
     */
    private array $definitions;

    /**
     * The legacy database stores only a bare public filename. This command is
     * deliberately opt-in: it verifies every copy, updates only the matching
     * row, then removes an exact old public file only when no legacy row still
     * refers to it.
     *
     * @var string
     */
    protected $signature = 'legacy-exercise-media:make-private
        {--dry-run : Dosya veya veritabanı değiştirmeden geçişi denetler}
        {--chunk=100 : Bir seferde okunacak kayıt sayısı}';

    /**
     * @var string
     */
    protected $description = 'Legacy tema, alıştırma, soru ve declaration medyasını doğrulayarak private diske taşır.';

    public function handle(): int
    {
        $chunkSize = $this->chunkSize();

        if ($chunkSize === null) {
            $this->error('--chunk en az 1 olan bir tam sayı olmalıdır.');

            return self::FAILURE;
        }

        $this->definitions = $this->mediaDefinitions();
        $dryRun = (bool) $this->option('dry-run');
        $summary = [
            'candidates' => 0,
            'already_private' => 0,
            'migrated' => 0,
            'would_migrate' => 0,
            'deleted_public_source' => 0,
            'preserved_fallback' => 0,
            'blocked' => 0,
        ];

        $this->info($dryRun
            ? 'Dry-run: legacy medya dosyaları yalnız denetleniyor.'
            : 'Legacy medya dosyaları private diske taşınıyor; yalnız doğrulanmış eski public kaynaklar silinecek.');

        $groups = $this->discoverSourceGroups($chunkSize, $summary);

        foreach ($groups as $group) {
            $this->processSourceGroup($group, $dryRun, $summary);
        }

        $this->newLine();
        $this->table(
            ['Sonuç', 'Adet'],
            [
                ['Aday kayıt', $summary['candidates']],
                ['Zaten private', $summary['already_private']],
                [$dryRun ? 'Private yapılacak' : 'Private yapıldı', $dryRun ? $summary['would_migrate'] : $summary['migrated']],
                ['Silinen eski public kaynak', $summary['deleted_public_source']],
                ['Korunan noimage.jpg fallback', $summary['preserved_fallback']],
                ['Engellendi', $summary['blocked']],
            ],
        );

        if ($summary['blocked'] > 0) {
            $this->error('Engellenen kayıtlar düzeltilmeden ilgili public medya silinmedi.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, int>  $summary
     * @return array<string, array{source_path: string, filename: string, public_directory: string, references: list<array<string, mixed>>}>
     */
    private function discoverSourceGroups(int $chunkSize, array &$summary): array
    {
        $groups = [];

        foreach ($this->definitions as $definition) {
            $modelClass = $definition['model'];
            $field = $definition['field'];

            $modelClass::query()
                ->select(['id', $field])
                ->whereNotNull($field)
                ->orderBy('id')
                ->lazyById($chunkSize)
                ->each(function (Model $record) use (&$groups, &$summary, $definition, $field): void {
                    $value = trim((string) $record->getAttribute($field));

                    if ($value === '') {
                        return;
                    }

                    $summary['candidates']++;

                    if (LegacyExerciseMedia::isPublicFallback($value)) {
                        $summary['preserved_fallback']++;
                        $this->line(sprintf('[%s:%d:%s] noimage.jpg genel arayüz fallback olarak public bırakıldı.', $definition['label'], $record->getKey(), $field));

                        return;
                    }

                    $reference = [
                        'definition' => $definition,
                        'model' => $definition['model'],
                        'id' => (int) $record->getKey(),
                        'field' => $field,
                        'value' => $value,
                        'state' => null,
                        'source_filename' => null,
                    ];

                    if (LegacyExerciseMedia::isSafePathForDirectory($value, $definition['private_directory'])) {
                        $reference['state'] = 'private';
                        $sourceFilename = $this->sourceFilenameFromMigratedPath($value, $definition, (int) $record->getKey());

                        if ($sourceFilename === null) {
                            $this->processStandalonePrivateReference($reference, $summary);

                            return;
                        }

                        $reference['source_filename'] = $sourceFilename;
                        $this->addReferenceToGroup($groups, $reference, $sourceFilename);

                        return;
                    }

                    if (! LegacyExerciseMedia::isSafeLegacyFilename($value)) {
                        $summary['blocked']++;
                        $this->warn(sprintf('[%s:%d:%s] Güvenli olmayan medya yolu; atlandı.', $definition['label'], $record->getKey(), $field));

                        return;
                    }

                    $reference['state'] = 'public';
                    $reference['source_filename'] = $value;
                    $this->addReferenceToGroup($groups, $reference, $value);
                });
        }

        return $groups;
    }

    /**
     * @param  array<string, array{source_path: string, filename: string, public_directory: string, references: list<array<string, mixed>>}>  $groups
     * @param  array<string, mixed>  $reference
     */
    private function addReferenceToGroup(array &$groups, array $reference, string $filename): void
    {
        /** @var array{public_directory: string} $definition */
        $definition = $reference['definition'];
        $publicDirectory = $definition['public_directory'];
        $key = $publicDirectory.'/'.$filename;

        if (! isset($groups[$key])) {
            $groups[$key] = [
                'source_path' => public_path($key),
                'filename' => $filename,
                'public_directory' => $publicDirectory,
                'references' => [],
            ];
        }

        $groups[$key]['references'][] = $reference;
    }

    /**
     * @param  array<string, mixed>  $reference
     * @param  array<string, int>  $summary
     */
    private function processStandalonePrivateReference(array $reference, array &$summary): void
    {
        $definition = $reference['definition'];

        try {
            if (! Storage::disk(LegacyExerciseMedia::DISK)->exists($reference['value'])) {
                throw new RuntimeException('Private dosya bulunamadı.');
            }

            $summary['already_private']++;
            $this->line(sprintf('[%s:%d:%s] Zaten private.', $definition['label'], $reference['id'], $reference['field']));
        } catch (Throwable $exception) {
            $summary['blocked']++;
            $this->warn(sprintf('[%s:%d:%s] %s', $definition['label'], $reference['id'], $reference['field'], $exception->getMessage()));
        }
    }

    /**
     * @param  array{source_path: string, filename: string, public_directory: string, references: list<array<string, mixed>>}  $group
     * @param  array<string, int>  $summary
     */
    private function processSourceGroup(array $group, bool $dryRun, array &$summary): void
    {
        $publicReferences = array_values(array_filter(
            $group['references'],
            static fn (array $reference): bool => $reference['state'] === 'public',
        ));
        $privateReferences = array_values(array_filter(
            $group['references'],
            static fn (array $reference): bool => $reference['state'] === 'private',
        ));

        $allReferencesHealthy = true;

        foreach ($privateReferences as $reference) {
            if (! $this->verifyMigratedPrivateReference($reference, $summary)) {
                $allReferencesHealthy = false;
            }
        }

        $sourceFingerprint = null;

        if ($publicReferences !== []) {
            try {
                $sourceFingerprint = $this->publicSourceFingerprint($group['source_path'], $group['public_directory']);
            } catch (Throwable $exception) {
                $allReferencesHealthy = false;

                foreach ($publicReferences as $reference) {
                    $this->blockedReference($reference, $exception->getMessage(), $summary);
                }
            }
        }

        if ($sourceFingerprint !== null) {
            foreach ($publicReferences as $reference) {
                if (! $this->migratePublicReference($reference, $sourceFingerprint, $dryRun, $summary)) {
                    $allReferencesHealthy = false;
                }
            }
        }

        if (! $allReferencesHealthy || $dryRun) {
            return;
        }

        $this->removePublicSourceIfSafe($group, $sourceFingerprint, $summary);
    }

    /**
     * @param  array<string, mixed>  $reference
     * @param  array<string, int>  $summary
     */
    private function verifyMigratedPrivateReference(array $reference, array &$summary): bool
    {
        $definition = $reference['definition'];

        try {
            $fingerprint = $this->privateFingerprint($reference['value']);
            $expectedHash = $this->migratedPathHash($reference['value'], $definition, $reference['id']);

            if ($expectedHash === null || ! hash_equals($expectedHash, $fingerprint['sha256'])) {
                throw new RuntimeException('Private dosya SHA-256 doğrulamasından geçemedi.');
            }

            $summary['already_private']++;
            $this->line(sprintf('[%s:%d:%s] Daha önce private yapılmış kayıt doğrulandı.', $definition['label'], $reference['id'], $reference['field']));

            return true;
        } catch (Throwable $exception) {
            $this->blockedReference($reference, $exception->getMessage(), $summary);

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $reference
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     * @param  array<string, int>  $summary
     */
    private function migratePublicReference(array $reference, array $sourceFingerprint, bool $dryRun, array &$summary): bool
    {
        $definition = $reference['definition'];
        $destination = $this->privateDestination($reference, $sourceFingerprint['sha256']);

        if ($destination === null) {
            $this->blockedReference($reference, 'Eski dosya adı güvenli hedef yola dönüştürülemedi.', $summary);

            return false;
        }

        try {
            if ($dryRun) {
                $this->assertTargetCanReceive($destination, $sourceFingerprint);
                $summary['would_migrate']++;
                $this->line(sprintf('[%s:%d:%s] Private diske kopyalanıp kayıt güncellenecek.', $definition['label'], $reference['id'], $reference['field']));

                return true;
            }

            $createdDestination = $this->copyPublicFileToPrivate($reference['source_filename'], $definition['public_directory'], $destination, $sourceFingerprint);

            try {
                $this->updateReferencePath($reference, $destination);
            } catch (Throwable $exception) {
                if ($createdDestination) {
                    $this->removeUnreferencedPrivateDestination($destination, $sourceFingerprint);
                }

                throw $exception;
            }

            $summary['migrated']++;
            $this->line(sprintf('[%s:%d:%s] Private diske taşındı.', $definition['label'], $reference['id'], $reference['field']));

            return true;
        } catch (Throwable $exception) {
            $this->blockedReference($reference, $exception->getMessage(), $summary);

            return false;
        }
    }

    /**
     * @param  array{source_path: string, filename: string, public_directory: string, references: list<array<string, mixed>>}  $group
     * @param  array{size: int, sha256: string}|null  $sourceFingerprint
     * @param  array<string, int>  $summary
     */
    private function removePublicSourceIfSafe(array $group, ?array $sourceFingerprint, array &$summary): void
    {
        $sourcePath = $group['source_path'];

        if (! is_file($sourcePath)) {
            return;
        }

        if ($this->hasRemainingPublicReference($group['public_directory'], $group['filename'])) {
            $this->warn(sprintf('[%s] Eski public kaynakta kalan veritabanı referansı var; silinmedi.', $group['public_directory'].'/'.$group['filename']));

            return;
        }

        try {
            $currentFingerprint = $this->publicSourceFingerprint($sourcePath, $group['public_directory']);

            if ($sourceFingerprint !== null && ! $this->fingerprintsMatch($sourceFingerprint, $currentFingerprint)) {
                throw new RuntimeException('Kaynak dosya geçiş sırasında değişti.');
            }

            $publicDirectoryPath = realpath(public_path($group['public_directory']));
            $realSourcePath = realpath($sourcePath);

            if ($publicDirectoryPath === false || $realSourcePath === false
                || ! str_starts_with($realSourcePath, rtrim($publicDirectoryPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)) {
                throw new RuntimeException('Kaynak dosya beklenen public klasörün dışında.');
            }

            if (! unlink($sourcePath)) {
                throw new RuntimeException('Eski public kaynak silinemedi.');
            }

            $summary['deleted_public_source']++;
            $this->line(sprintf('[%s] Doğrulanmış eski public kaynak silindi.', $group['public_directory'].'/'.$group['filename']));
        } catch (Throwable $exception) {
            $this->warn(sprintf('[%s] Eski public kaynak silinmedi: %s', $group['public_directory'].'/'.$group['filename'], $exception->getMessage()));
        }
    }

    private function hasRemainingPublicReference(string $publicDirectory, string $filename): bool
    {
        foreach ($this->definitions as $definition) {
            if ($definition['public_directory'] !== $publicDirectory) {
                continue;
            }

            $modelClass = $definition['model'];

            if ($modelClass::query()->where($definition['field'], $filename)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $reference
     */
    private function updateReferencePath(array $reference, string $destination): void
    {
        DB::transaction(function () use ($reference, $destination): void {
            $modelClass = $reference['model'];
            $record = $modelClass::query()->lockForUpdate()->find($reference['id']);

            if (! $record instanceof Model) {
                throw new RuntimeException('Kayıt geçiş sırasında silindi.');
            }

            if ((string) $record->getAttribute($reference['field']) !== $reference['value']) {
                throw new RuntimeException('Kayıt medya yolu geçiş sırasında değişti.');
            }

            $record->forceFill([$reference['field'] => $destination])->save();
        });
    }

    /**
     * @param  array<string, mixed>  $reference
     */
    private function privateDestination(array $reference, string $sourceHash): ?string
    {
        $filename = $reference['source_filename'];

        if (! is_string($filename) || ! LegacyExerciseMedia::isSafeLegacyFilename($filename)) {
            return null;
        }

        $encodedFilename = rawurlencode($filename);

        // Keep the final filesystem segment well below common 255-byte limits.
        if (strlen($encodedFilename) > 150) {
            return null;
        }

        $definition = $reference['definition'];

        return sprintf(
            '%s/%d/%s--%s--%s',
            $definition['private_directory'],
            $reference['id'],
            $reference['field'],
            $sourceHash,
            $encodedFilename,
        );
    }

    /**
     * @param  array{model: class-string<Model>, field: string, public_directory: string, private_directory: string, label: string}  $definition
     */
    private function sourceFilenameFromMigratedPath(string $path, array $definition, int $id): ?string
    {
        $pattern = sprintf(
            '~^%s/%d/%s--[a-f0-9]{64}--(.+)$~D',
            preg_quote($definition['private_directory'], '~'),
            $id,
            preg_quote($definition['field'], '~'),
        );

        if (preg_match($pattern, $path, $matches) !== 1) {
            return null;
        }

        $filename = rawurldecode($matches[1]);

        return LegacyExerciseMedia::isSafeLegacyFilename($filename) ? $filename : null;
    }

    /**
     * @param  array{model: class-string<Model>, field: string, public_directory: string, private_directory: string, label: string}  $definition
     */
    private function migratedPathHash(string $path, array $definition, int $id): ?string
    {
        $pattern = sprintf(
            '~^%s/%d/%s--([a-f0-9]{64})--.+$~D',
            preg_quote($definition['private_directory'], '~'),
            $id,
            preg_quote($definition['field'], '~'),
        );

        return preg_match($pattern, $path, $matches) === 1 ? $matches[1] : null;
    }

    /**
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     */
    private function assertTargetCanReceive(string $destination, array $sourceFingerprint): void
    {
        $disk = Storage::disk(LegacyExerciseMedia::DISK);

        if (! $disk->exists($destination)) {
            return;
        }

        if (! $this->fingerprintsMatch($sourceFingerprint, $this->privateFingerprint($destination))) {
            throw new RuntimeException('Aynı private hedefte farklı bir dosya var.');
        }
    }

    /**
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     */
    private function copyPublicFileToPrivate(string $filename, string $publicDirectory, string $destination, array $sourceFingerprint): bool
    {
        $sourcePath = public_path($publicDirectory.'/'.$filename);
        $disk = Storage::disk(LegacyExerciseMedia::DISK);

        if ($disk->exists($destination)) {
            if (! $this->fingerprintsMatch($sourceFingerprint, $this->privateFingerprint($destination))) {
                throw new RuntimeException('Aynı private hedefte farklı bir dosya var.');
            }

            return false;
        }

        $temporaryPath = self::TEMPORARY_PATH_PREFIX.bin2hex(random_bytes(16)).'.part';
        $sourceStream = null;

        try {
            $sourceStream = fopen($sourcePath, 'rb');

            if (! is_resource($sourceStream)) {
                throw new RuntimeException('Public kaynak dosya stream olarak açılamadı.');
            }

            if ($disk->writeStream($temporaryPath, $sourceStream) !== true) {
                throw new RuntimeException('Geçici private dosya yazılamadı.');
            }

            if (! $this->fingerprintsMatch($sourceFingerprint, $this->privateFingerprint($temporaryPath))) {
                throw new RuntimeException('Geçici private dosya kaynakla uyuşmuyor.');
            }

            if ($disk->exists($destination)) {
                if (! $this->fingerprintsMatch($sourceFingerprint, $this->privateFingerprint($destination))) {
                    throw new RuntimeException('Kopyalama sırasında aynı private hedefte farklı bir dosya oluştu.');
                }

                return false;
            }

            if (! $disk->move($temporaryPath, $destination)) {
                throw new RuntimeException('Doğrulanmış private dosya hedefe taşınamadı.');
            }

            $temporaryPath = null;

            if (! $this->fingerprintsMatch($sourceFingerprint, $this->privateFingerprint($destination))) {
                throw new RuntimeException('Private hedef dosya kaynakla uyuşmuyor.');
            }

            return true;
        } finally {
            if (is_resource($sourceStream)) {
                fclose($sourceStream);
            }

            if ($temporaryPath !== null) {
                try {
                    $disk->delete($temporaryPath);
                } catch (Throwable) {
                    // Random, command-owned temporary files can safely be left for inspection.
                }
            }
        }
    }

    /**
     * @param  array{size: int, sha256: string}  $sourceFingerprint
     */
    private function removeUnreferencedPrivateDestination(string $destination, array $sourceFingerprint): void
    {
        try {
            if ($this->hasPrivateReference($destination)) {
                return;
            }

            $disk = Storage::disk(LegacyExerciseMedia::DISK);

            if ($disk->exists($destination)
                && $this->fingerprintsMatch($sourceFingerprint, $this->privateFingerprint($destination))) {
                $disk->delete($destination);
            }
        } catch (Throwable) {
            // Never risk deleting an unknown private file while handling a failed migration.
        }
    }

    private function hasPrivateReference(string $path): bool
    {
        foreach ($this->definitions as $definition) {
            $modelClass = $definition['model'];

            if ($modelClass::query()->where($definition['field'], $path)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{size: int, sha256: string}
     */
    private function publicSourceFingerprint(string $sourcePath, string $publicDirectory): array
    {
        if (! is_file($sourcePath)) {
            throw new RuntimeException('Eski public kaynak dosya bulunamadı.');
        }

        $publicDirectoryPath = realpath(public_path($publicDirectory));
        $realSourcePath = realpath($sourcePath);

        if ($publicDirectoryPath === false || $realSourcePath === false
            || ! str_starts_with($realSourcePath, rtrim($publicDirectoryPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Public kaynak dosya beklenen klasörün dışında.');
        }

        $stream = fopen($sourcePath, 'rb');

        if (! is_resource($stream)) {
            throw new RuntimeException('Public kaynak dosya okunamadı.');
        }

        return $this->fingerprintStream($stream);
    }

    /**
     * @return array{size: int, sha256: string}
     */
    private function privateFingerprint(string $path): array
    {
        $stream = Storage::disk(LegacyExerciseMedia::DISK)->readStream($path);

        if (! is_resource($stream)) {
            throw new RuntimeException('Private dosya stream olarak açılamadı.');
        }

        return $this->fingerprintStream($stream);
    }

    /**
     * @param  resource  $stream
     * @return array{size: int, sha256: string}
     */
    private function fingerprintStream($stream): array
    {
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

    /**
     * @param  array<string, mixed>  $reference
     * @param  array<string, int>  $summary
     */
    private function blockedReference(array $reference, string $reason, array &$summary): void
    {
        $definition = $reference['definition'];
        $summary['blocked']++;
        $this->warn(sprintf('[%s:%d:%s] %s', $definition['label'], $reference['id'], $reference['field'], $reason));
    }

    /**
     * @return array<string, array{model: class-string<Model>, field: string, public_directory: string, private_directory: string, label: string}>
     */
    private function mediaDefinitions(): array
    {
        return [
            'theme-image' => [
                'model' => model_themes::class,
                'field' => 'image',
                'public_directory' => 'photos',
                'private_directory' => LegacyExerciseMedia::THEME_IMAGES,
                'label' => 'Tema',
            ],
            'exercise-image' => [
                'model' => model_exercises::class,
                'field' => 'image',
                'public_directory' => 'photos',
                'private_directory' => LegacyExerciseMedia::EXERCISE_IMAGES,
                'label' => 'Alıştırma',
            ],
            'question-image' => [
                'model' => model_questions::class,
                'field' => 'image',
                'public_directory' => 'photos',
                'private_directory' => LegacyExerciseMedia::QUESTION_IMAGES,
                'label' => 'Soru',
            ],
            'declaration-image' => [
                'model' => model_declarations::class,
                'field' => 'image',
                'public_directory' => 'photos',
                'private_directory' => LegacyExerciseMedia::DECLARATION_IMAGES,
                'label' => 'Declaration görseli',
            ],
            'declaration-pdf' => [
                'model' => model_declarations::class,
                'field' => 'pdf',
                'public_directory' => 'pdfs',
                'private_directory' => LegacyExerciseMedia::DECLARATION_DOCUMENTS,
                'label' => 'Declaration PDF',
            ],
            'declaration-answerkey' => [
                'model' => model_declarations::class,
                'field' => 'answerkey',
                'public_directory' => 'pdfs',
                'private_directory' => LegacyExerciseMedia::DECLARATION_DOCUMENTS,
                'label' => 'Declaration cevap anahtarı',
            ],
        ];
    }

    private function chunkSize(): ?int
    {
        $value = filter_var($this->option('chunk'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        return $value === false ? null : $value;
    }
}
