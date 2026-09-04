<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    private const DISK = 'local';

    /**
     * Stream a profile photo only to its owner or an administrator.
     */
    public function show(Request $request, User $user): StreamedResponse
    {
        $viewer = $request->user();

        abort_unless(
            $viewer !== null && ($viewer->is($user) || $viewer->type === 'admin'),
            403,
        );

        $path = $user->profile_photo_path;

        abort_unless($this->isSafeProfilePhotoPath($path), 404);

        $disk = Storage::disk(self::DISK);

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, basename($path), [
            'Cache-Control' => 'private, max-age=3600, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function isSafeProfilePhotoPath(?string $path): bool
    {
        if (
            $path === null
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
}
