<?php

namespace App\Livewire;

use App\Models\Review;
use Livewire\Component;

class ReviewList extends Component
{
    public string $statusFilter = 'default';

    public $reviews;

    public $confirmingDelete = false;

    public $reviewToDelete = null;

    public $modalConfirmTitle;

    public $modalConfirmContent;

    public $modalSuccessTitle;

    public $modalSuccessContent;

    public function mount()
    {
        $this->loadReviews();
    }

    public function updatedStatusFilter()
    {
        $this->loadReviews();
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);

        if ($review->status !== Review::STATUS_APPROVED) {
            $review->status = Review::STATUS_APPROVED;
            $review->approved_by = auth()->id();
            $review->approved_at = now();
            $review->save();

            $this->modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.review')]);
            $this->modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.review'), 'name' => $this->displayName($review)]);
        }

        $this->loadReviews();
    }

    public function reject($id)
    {
        $review = Review::findOrFail($id);

        if ($review->status !== Review::STATUS_REJECTED) {
            $review->status = Review::STATUS_REJECTED;
            $review->approved_by = null;
            $review->approved_at = null;
            $review->save();

            $this->modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.review')]);
            $this->modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.review'), 'name' => $this->displayName($review)]);
        }

        $this->loadReviews();
    }

    public function confirmDelete($id)
    {
        $review = Review::findOrFail($id);
        $this->reviewToDelete = $review;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.review')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => __('dictt.review'), 'name' => $this->displayName($review)]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if ($this->reviewToDelete) {
            $review = $this->reviewToDelete;
            $name = $this->displayName($review);
            $review->delete();
            $this->reviewToDelete = null;
            $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.review')]);
            $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => __('dictt.review'), 'name' => $name]);
            $this->confirmingDelete = false;
        }

        $this->loadReviews();
    }

    private function displayName(Review $review): string
    {
        return $review->user?->name ?? ('#' . $review->id);
    }

    private function loadReviews()
    {
        if ($this->statusFilter === Review::STATUS_PENDING) {
            $this->reviews = Review::with('user')
                ->where('status', Review::STATUS_PENDING)
                ->orderBy('created_at')
                ->get();

            return;
        }

        if ($this->statusFilter === Review::STATUS_APPROVED) {
            $this->reviews = Review::with('user')
                ->where('status', Review::STATUS_APPROVED)
                ->orderByDesc('created_at')
                ->get();

            return;
        }

        if ($this->statusFilter === Review::STATUS_REJECTED) {
            $this->reviews = Review::with('user')
                ->where('status', Review::STATUS_REJECTED)
                ->orderByDesc('created_at')
                ->get();

            return;
        }

        $pending = Review::with('user')
            ->where('status', Review::STATUS_PENDING)
            ->orderBy('created_at')
            ->get();

        $approved = Review::with('user')
            ->where('status', Review::STATUS_APPROVED)
            ->orderByDesc('created_at')
            ->get();

        $this->reviews = $pending->concat($approved);
    }

    public function render()
    {
        return view('livewire.review-list');
    }
}
