<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index(): View
    {
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
            'latestReview' => $approved->last(),
            'previousReview' => $approved->count() >= 2 ? $approved->slice(-2, 1)->first() : null,
            'firstReview' => $approved->count() >= 3 ? $approved->first() : null,
            'reviewCarousel' => $carousel,
        ]);
    }
}
