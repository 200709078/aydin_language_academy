<?php

namespace App\Livewire;

use App\Models\Review;
use App\Services\AdminApprovalNotificationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MyReviews extends Component
{
    public $reviews;

    public string $content = '';

    public int $rating = 5;

    public string $branch = '';

    public ?int $editingId = null;

    public string $successMessage = '';

    public bool $confirmingArchive = false;

    public ?int $reviewToArchive = null;

    public string $reviewToArchiveName = '';

    public function mount()
    {
        $this->loadReviews();
    }

    public function loadReviews()
    {
        $this->reviews = Review::query()
            ->where('user_id', auth()->id())
            ->where('status', '!=', Review::STATUS_ARCHIVED)
            ->orderByDesc('created_at')
            ->get();
    }

    public function create()
    {
        $limiterKey = 'review-create:' . auth()->id();

        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            $this->addError('content', __('dictt.try_again_later'));

            return;
        }

        RateLimiter::hit($limiterKey, 60);

        $this->validate($this->rules(), $this->messages());

        $hasPendingReview = Review::query()
            ->where('user_id', auth()->id())
            ->where('status', Review::STATUS_PENDING)
            ->exists();

        if ($hasPendingReview) {
            $this->addError('content', __('dictt.review_pending_exists'));

            return;
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'branch' => $this->branch !== '' ? $this->branch : null,
            'content' => $this->content,
            'rating' => $this->rating,
            'status' => Review::STATUS_PENDING,
        ]);

        app(AdminApprovalNotificationService::class)->reviewCreated($review);

        $this->resetForm();
        $this->successMessage = __('dictt.review_submit_success');
        $this->loadReviews();
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);

        Gate::authorize('update', $review);

        $this->editingId = $review->id;
        $this->content = $review->content;
        $this->rating = $review->rating;
        $this->branch = $review->branch ?? '';
        $this->resetErrorBag();
        $this->successMessage = '';
    }

    public function update()
    {
        $review = Review::findOrFail($this->editingId);

        Gate::authorize('update', $review);

        $this->validate($this->rules(), $this->messages());

        $wasRejected = $review->status === Review::STATUS_REJECTED;

        $review->fill([
            'branch' => $this->branch !== '' ? $this->branch : null,
            'content' => $this->content,
            'rating' => $this->rating,
        ]);

        $review->status = Review::STATUS_PENDING;
        $review->approved_by = null;
        $review->approved_at = null;
        $reviewContentChanged = $review->isDirty(['branch', 'content', 'rating']);
        $review->save();

        if ($reviewContentChanged || $wasRejected) {
            app(AdminApprovalNotificationService::class)->reviewUpdated($review, $wasRejected);
        }

        $this->resetForm();
        $this->successMessage = __('dictt.review_update_success');
        $this->loadReviews();
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function confirmArchive($id): void
    {
        $review = Review::findOrFail($id);

        Gate::authorize('delete', $review);

        $this->reviewToArchive = $review->id;
        $this->reviewToArchiveName = $review->user?->name ?? ('#' . $review->id);
        $this->confirmingArchive = true;
    }

    public function archive(): void
    {
        if (! $this->reviewToArchive) {
            $this->cancelArchive();

            return;
        }

        $review = Review::findOrFail($this->reviewToArchive);

        Gate::authorize('delete', $review);

        $review->update(['status' => Review::STATUS_ARCHIVED]);

        if ($this->editingId === $review->id) {
            $this->resetForm();
        }

        $this->cancelArchive();
        $this->successMessage = __('dictt.review_archive_success');
        $this->loadReviews();
    }

    public function cancelArchive(): void
    {
        $this->confirmingArchive = false;
        $this->reviewToArchive = null;
        $this->reviewToArchiveName = '';
    }

    private function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'branch' => ['nullable', 'string', Rule::in(Review::BRANCHES)],
        ];
    }

    private function messages(): array
    {
        return [
            'content.required' => __('dictt.required_item', ['name' => __('dictt.content')]),
            'content.min' => __('dictt.mincharacter_item', ['name' => __('dictt.content'), 'number' => 3]),
            'content.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.content'), 'number' => 2000]),
            'rating.required' => __('dictt.required_item', ['name' => __('dictt.rating')]),
            'rating.integer' => __('dictt.invalidvalue_item', ['name' => __('dictt.rating')]),
            'rating.min' => __('dictt.invalidvalue_item', ['name' => __('dictt.rating')]),
            'rating.max' => __('dictt.invalidvalue_item', ['name' => __('dictt.rating')]),
            'branch.in' => __('dictt.invalidvalue_item', ['name' => __('dictt.branch')]),
        ];
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->content = '';
        $this->rating = 5;
        $this->branch = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.my-reviews');
    }
}
