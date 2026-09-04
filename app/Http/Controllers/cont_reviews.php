<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class cont_reviews extends Controller
{
    public function index(): View
    {
        return view('admin.reviews.list');
    }

    public function edit(string $review_id): View
    {
        $review = Review::with(['user', 'approver'])
            ->where('status', '!=', Review::STATUS_ARCHIVED)
            ->findOrFail($review_id);

        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, string $review_id): RedirectResponse
    {
        $review = Review::with(['user', 'approver'])
            ->where('status', '!=', Review::STATUS_ARCHIVED)
            ->findOrFail($review_id);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'branch' => ['nullable', 'string', 'in:' . implode(',', Review::BRANCHES)],
            'status' => ['required', 'string', 'in:' . implode(',', [Review::STATUS_PENDING, Review::STATUS_APPROVED, Review::STATUS_REJECTED])],
        ], [
            'content.required' => __('dictt.required_item', ['name' => __('dictt.content')]),
            'content.min' => __('dictt.mincharacter_item', ['name' => __('dictt.content'), 'number' => 3]),
            'content.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.content'), 'number' => 2000]),
            'rating.required' => __('dictt.required_item', ['name' => __('dictt.rating')]),
            'rating.integer' => __('dictt.invalidvalue_item', ['name' => __('dictt.rating')]),
            'rating.min' => __('dictt.invalidvalue_item', ['name' => __('dictt.rating')]),
            'rating.max' => __('dictt.invalidvalue_item', ['name' => __('dictt.rating')]),
            'branch.in' => __('dictt.invalidvalue_item', ['name' => __('dictt.branch')]),
            'status.required' => __('dictt.required_item', ['name' => __('dictt.status')]),
            'status.in' => __('dictt.invalidvalue_item', ['name' => __('dictt.status')]),
        ]);

        $wasApproved = $review->status === Review::STATUS_APPROVED;
        $displayName = $review->user?->name ?? ('#' . $review->id);

        $review->fill([
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'branch' => $validated['branch'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($review->status === Review::STATUS_APPROVED) {
            if (! $wasApproved) {
                $review->approved_by = $request->user()->id;
                $review->approved_at = now();
            }
        } else {
            $review->approved_by = null;
            $review->approved_at = null;
        }

        $review->save();

        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.review')]);
        $modalSuccessContent = __('dictt.admin_review_update_success', ['name' => $displayName]);

        return redirect()->route('reviews_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }
}
