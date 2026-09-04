<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

final class LegacyExerciseMedia
{
    public const DISK = 'local';

    public const THEME_IMAGES = 'legacy-exercise/themes/images';

    public const EXERCISE_IMAGES = 'legacy-exercise/exercises/images';

    public const QUESTION_IMAGES = 'legacy-exercise/questions/images';

    public const DECLARATION_IMAGES = 'legacy-exercise/declarations/images';

    public const DECLARATION_DOCUMENTS = 'legacy-exercise/declarations/documents';

    /**
     * `noimage.jpg` is an intentionally public, non-content UI fallback.
     */
    public static function isPublicFallback(?string $path): bool
    {
        return trim((string) $path) === 'noimage.jpg';
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, self::DISK);
    }

    public static function isSafePathForDirectory(?string $path, string $directory): bool
    {
        $path = trim((string) $path);
        $prefix = trim($directory, '/').'/';

        if (
            $path === ''
            || ! str_starts_with($path, $prefix)
            || str_contains(strtolower($path), '://')
            || str_starts_with($path, '/')
            || str_starts_with($path, '//')
            || str_contains($path, '\\')
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }

    public static function isSafeLegacyFilename(?string $filename): bool
    {
        $filename = trim((string) $filename);

        return $filename !== ''
            && $filename === basename($filename)
            && ! str_contains($filename, '\\')
            && ! str_contains($filename, "\0")
            && $filename !== '.'
            && $filename !== '..';
    }

}
