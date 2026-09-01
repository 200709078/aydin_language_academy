<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPageSetting;
use App\Models\MediaAsset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    private const MEDIA_DISK = 'local';

    private const MEDIA_PATH_PREFIX = 'campaigns/media-assets/';

    /**
     * Display the campaign page with only published campaign cards.
     */
    public function index(): View
    {
        $pageSettings = CampaignPageSetting::query()
            ->with('heroMediaAsset')
            ->orderBy('id')
            ->first();

        $campaigns = Campaign::query()
            ->publiclyAvailable()
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('frontend.campaigns', compact('pageSettings', 'campaigns'));
    }

    /**
     * Serve only the private hero image assigned to the singleton campaign page
     * setting. Other private media files remain inaccessible from this route.
     */
    public function media(MediaAsset $mediaAsset)
    {
        $pageSettings = CampaignPageSetting::query()
            ->orderBy('id')
            ->firstOrFail();

        $path = trim((string) $mediaAsset->path);

        if (
            (int) $pageSettings->hero_media_asset_id !== (int) $mediaAsset->getKey()
            || $mediaAsset->kind !== MediaAsset::KIND_IMAGE
            || $mediaAsset->visibility !== MediaAsset::VISIBILITY_PRIVATE
            || $mediaAsset->disk !== self::MEDIA_DISK
            || ! $this->isSafeCampaignMediaPath($path)
        ) {
            abort(404);
        }

        $disk = Storage::disk(self::MEDIA_DISK);

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, $mediaAsset->original_filename, [
            'Content-Type' => $mediaAsset->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function isSafeCampaignMediaPath(string $path): bool
    {
        if (
            $path === ''
            || ! str_starts_with($path, self::MEDIA_PATH_PREFIX)
            || str_starts_with($path, '/')
            || str_contains($path, '\\')
            || str_contains(strtolower($path), '://')
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }
}
