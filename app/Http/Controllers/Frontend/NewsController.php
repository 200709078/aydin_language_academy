<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    private const MEDIA_DISK = 'local';

    private const MEDIA_PATH_PREFIX = 'news/media-assets/';

    /**
     * List only news that is currently available to the public.
     */
    public function index(): View
    {
        $news = News::query()
            ->publiclyAvailable()
            ->with('coverMediaAsset')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('frontend.news', compact('news'));
    }

    /**
     * Show one currently public news item and its active content blocks.
     *
     * Explicitly resolving through publiclyAvailable() keeps drafts, scheduled,
     * expired, archived, and soft-deleted records out of the public route.
     */
    public function show(News $news): View
    {
        $news = News::query()
            ->publiclyAvailable()
            ->whereKey($news->getKey())
            ->with([
                'coverMediaAsset',
                'activeContentBlocks.mediaAsset',
            ])
            ->firstOrFail();

        return view('frontend.news-detail', compact('news'));
    }

    /**
     * Stream a private uploaded media file only when it belongs to a currently
     * public news item and is used by its cover or an active content block.
     */
    public function media(News $news, MediaAsset $mediaAsset)
    {
        $publicNews = News::query()
            ->publiclyAvailable()
            ->whereKey($news->getKey())
            ->firstOrFail();

        $path = trim((string) $mediaAsset->path);

        if (! $this->isReferencedByPublicNews($publicNews, $mediaAsset)
            || $mediaAsset->disk !== self::MEDIA_DISK
            || ! $this->isSafeNewsMediaPath($path)) {
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

    private function isReferencedByPublicNews(News $news, MediaAsset $mediaAsset): bool
    {
        if ((int) $news->cover_media_asset_id === (int) $mediaAsset->getKey()) {
            return true;
        }

        return $news->activeContentBlocks()
            ->where('media_asset_id', $mediaAsset->getKey())
            ->exists();
    }

    private function isSafeNewsMediaPath(string $path): bool
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
