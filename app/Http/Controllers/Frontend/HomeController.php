<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Review;
use App\Services\SloganService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(SloganService $sloganService): View
    {
        $homepageNews = News::query()
            ->publiclyAvailable()
            ->whereIn('display_location', [
                News::DISPLAY_HOMEPAGE,
                News::DISPLAY_HERO,
            ])
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
            ->get();

        $approved = Review::query()
            ->where('status', Review::STATUS_APPROVED)
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('frontend.home', [
            'heroNews' => $heroNews,
            'homepageNews' => $homepageNews,
            'primarySlogan' => $sloganService->randomText(
                fallback: __('dictt.primary_slogan'),
            ),
            'latestReview' => $approved->get(0),
            'previousReview' => $approved->get(1),
            'firstReview' => $approved->get(2),
            'reviewCarousel' => $approved->values(),
        ]);
    }
}
