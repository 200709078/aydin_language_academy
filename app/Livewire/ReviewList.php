<?php

namespace App\Livewire;

use App\Models\Review;
use Livewire\Component;

class ReviewList extends Component
{
    public string $statusFilter = 'default';

    public $reviews;

    public bool $confirmingAction = false;

    public ?int $reviewToActOn = null;

    public ?string $pendingAction = null;

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

        $this->ensureReviewIsActive($review);

        if ($review->status !== Review::STATUS_APPROVED) {
            $review->status = Review::STATUS_APPROVED;
            $review->approved_by = auth()->id();
            $review->approved_at = now();
            $review->save();

            $this->modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.review')]);
            $this->modalSuccessContent = __('dictt.admin_review_approve_success', ['name' => $this->displayName($review)]);
        }

        $this->loadReviews();
    }

    public function reject($id)
    {
        $review = Review::findOrFail($id);

        $this->ensureReviewIsActive($review);

        if ($review->status !== Review::STATUS_REJECTED) {
            $review->status = Review::STATUS_REJECTED;
            $review->approved_by = null;
            $review->approved_at = null;
            $review->save();

            $this->modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.review')]);
            $this->modalSuccessContent = __('dictt.admin_review_reject_success', ['name' => $this->displayName($review)]);
        }

        $this->loadReviews();
    }

    public function confirmArchive($id): void
    {
        $review = Review::findOrFail($id);

        $this->ensureReviewIsActive($review);

        $this->reviewToActOn = $review->id;
        $this->pendingAction = 'archive';
        $this->modalConfirmTitle = __('dictt.review_archive_action');
        $this->modalConfirmContent = __('dictt.review_archive_confirm', ['name' => $this->displayName($review)]);
        $this->confirmingAction = true;
    }

    public function confirmForceDelete($id): void
    {
        $review = Review::withTrashed()->findOrFail($id);

        $this->ensureReviewIsArchived($review);

        $this->reviewToActOn = $review->id;
        $this->pendingAction = 'force-delete';
        $this->modalConfirmTitle = __('dictt.review_permanently_delete');
        $this->modalConfirmContent = __('dictt.review_force_delete_confirm', ['name' => $this->displayName($review)]);
        $this->confirmingAction = true;
    }

    public function executePendingAction(): void
    {
        if (! $this->reviewToActOn || ! $this->pendingAction) {
            $this->clearPendingAction();

            return;
        }

        if ($this->pendingAction === 'archive') {
            $review = Review::findOrFail($this->reviewToActOn);
            $this->ensureReviewIsActive($review);

            $review->update(['status' => Review::STATUS_ARCHIVED]);
            $this->modalSuccessTitle = __('dictt.review_archive_action');
            $this->modalSuccessContent = __('dictt.review_archived');
        }

        if ($this->pendingAction === 'force-delete') {
            $review = Review::withTrashed()->findOrFail($this->reviewToActOn);
            $this->ensureReviewIsArchived($review);

            $review->forceDelete();
            $this->modalSuccessTitle = __('dictt.review_permanently_delete');
            $this->modalSuccessContent = __('dictt.review_permanently_deleted');
        }

        $this->clearPendingAction();
        $this->loadReviews();
    }

    public function dismissSuccess(): void
    {
        $this->modalSuccessTitle = null;
        $this->modalSuccessContent = null;

        session()->forget(['modalSuccessTitle', 'modalSuccessContent']);
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

        if ($this->statusFilter === Review::STATUS_ARCHIVED) {
            $this->reviews = Review::withTrashed()
                ->with('user')
                ->where(function ($query): void {
                    $query->where('status', Review::STATUS_ARCHIVED)
                        ->orWhereNotNull('deleted_at');
                })
                ->orderByDesc('updated_at')
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

    private function ensureReviewIsActive(Review $review): void
    {
        if ($review->status === Review::STATUS_ARCHIVED || $review->trashed()) {
            abort(404);
        }
    }

    private function ensureReviewIsArchived(Review $review): void
    {
        if ($review->status !== Review::STATUS_ARCHIVED && ! $review->trashed()) {
            abort(404);
        }
    }

    private function clearPendingAction(): void
    {
        $this->reviewToActOn = null;
        $this->pendingAction = null;
        $this->confirmingAction = false;
    }

    public function render()
    {
        return view('livewire.review-list');
    }
}
