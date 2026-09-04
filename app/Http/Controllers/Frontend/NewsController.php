<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
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

}
