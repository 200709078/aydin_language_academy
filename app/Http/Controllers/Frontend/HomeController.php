<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Review;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $homepageNews = News::query()
            ->publiclyAvailable()
            ->where('display_location', News::DISPLAY_HOMEPAGE)
            ->with('coverMediaAsset')
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get();

        $heroNews = News::query()
            ->publiclyAvailable()
            ->where('display_location', News::DISPLAY_HERO)
            ->with('coverMediaAsset')
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(2)
            ->get();

        $approved = Review::query()
            ->where('status', Review::STATUS_APPROVED)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $carousel = collect();
        if ($approved->isNotEmpty()) {
            $carousel = collect([$approved->first()])
                ->merge($approved->slice(1)->sortByDesc('created_at')->values());
        }

        return view('frontend.home', [
            'heroNews' => $heroNews,
            'homepageNews' => $homepageNews,
            'latestReview' => $approved->last(),
            'previousReview' => $approved->count() >= 2 ? $approved->slice(-2, 1)->first() : null,
            'firstReview' => $approved->count() >= 3 ? $approved->first() : null,
            'reviewCarousel' => $carousel,
        ]);
    }
}
