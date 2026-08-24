<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $branch = $request->query('sube');

        $reviews = Review::query()
            ->where('status', Review::STATUS_APPROVED)
            ->with('user')
            ->when(in_array($branch, Review::BRANCHES, true), fn ($query) => $query->where('branch', $branch))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.reviews', [
            'reviews' => $reviews,
            'branch' => in_array($branch, Review::BRANCHES, true) ? $branch : null,
        ]);
    }
}
