<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\AchievementPageSetting;
use App\Models\MediaAsset;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    private const MEDIA_DISK = 'local';

    private const MEDIA_PATH_PREFIX = 'achievements/media-assets/';

    private const SHARED_CAMPAIGN_MEDIA_PATH_PREFIX = 'campaigns/media-assets/';

    /**
     * Show only currently public annual achievement records.
     */
    public function index(): View
    {
        $pageSettings = AchievementPageSetting::query()
            ->with('heroMediaAsset')
            ->orderBy('id')
            ->first();

        $achievementYears = Achievement::query()
            ->publiclyAvailable()
            ->whereHas('publicEntries')
            ->with([
                'publicEntries' => static function (HasMany $query): void {
                    $query->select([
                        'id',
                        'achievements_id',
                        'full_name',
                        'name_permission_status',
                        'university_name',
                        'department_name',
                        'description',
                        'branch',
                        'card_sub_title',
                        'sort_order',
                    ]);
                },
            ])
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        $initialOpenYearId = $achievementYears->first()?->getKey();

        return view('frontend.achievements', compact(
            'achievementYears',
            'initialOpenYearId',
            'pageSettings',
        ));
    }

    /**
     * Serve only the private hero image selected for the singleton settings.
     * The temporarily shared campaign image is explicitly allowed as well.
     */
    public function media(MediaAsset $mediaAsset)
    {
        $pageSettings = AchievementPageSetting::query()
            ->orderBy('id')
            ->firstOrFail();
        $path = trim((string) $mediaAsset->path);

        if (
            (int) $pageSettings->hero_media_asset_id !== (int) $mediaAsset->getKey()
            || $mediaAsset->kind !== MediaAsset::KIND_IMAGE
            || $mediaAsset->visibility !== MediaAsset::VISIBILITY_PRIVATE
            || $mediaAsset->disk !== self::MEDIA_DISK
            || ! $this->isSafeAchievementMediaPath($path)
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

    private function isSafeAchievementMediaPath(string $path): bool
    {
        if (
            $path === ''
            || (! str_starts_with($path, self::MEDIA_PATH_PREFIX)
                && ! str_starts_with($path, self::SHARED_CAMPAIGN_MEDIA_PATH_PREFIX))
            || str_starts_with($path, '/')
            || str_contains($path, '\\')
            || str_contains(strtolower($path), '://')
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }
}
