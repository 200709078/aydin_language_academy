<div>
    @if ($successMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $successMessage }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Kapat"></button>
        </div>
    @endif

    <div class="card border-0 bg-light rounded mb-5">
        <div class="card-body p-4 p-lg-5">
            <h4 class="mb-4">
                {{ $editingId ? __('dictt.edit') : __('dictt.write_review') }}
                @if ($editingId)
                    <span class="badge bg-warning text-dark ms-2">{{ __('dictt.editing_review') }}</span>
                @endif
            </h4>
            <form wire:submit="{{ $editingId ? 'update' : 'create' }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ __('dictt.content') }}</label>
                        <textarea wire:model="content" class="form-control @error('content') is-invalid @enderror"
                            rows="4" placeholder="{{ __('dictt.review_placeholder') }}"></textarea>
                        @error('content')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('dictt.rating') }}</label>
                        <select wire:model="rating" class="form-select">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('rating')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('dictt.branch') }}</label>
                        <select wire:model="branch" class="form-select">
                            <option value="">{{ __('dictt.none') }}</option>
                            @foreach (\App\Models\Review::BRANCHES as $branchOption)
                                <option value="{{ $branchOption }}">{{ __("dictt.branch_{$branchOption}") }}</option>
                            @endforeach
                        </select>
                        @error('branch')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        {{ $editingId ? __('dictt.update') : __('dictt.send') }}
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="cancelEdit" class="btn btn-secondary">{{ __('dictt.cancel') }}</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <h4 class="mb-4">{{ __('dictt.my_reviews_list_title') }}</h4>

    <div class="row g-4 justify-content-center">
        @forelse ($reviews as $review)
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="testimonial-item text-center h-100 w-100 d-flex flex-column">
                    <div class="testimonial-text bg-light rounded text-center p-4 flex-grow-1 d-flex flex-column">
                        <p>{{ $review->content }}</p>
                        <div class="mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }} me-1"></i>
                            @endfor
                        </div>
                        <span class="fst-italic d-block mb-3">{{ $review->branchLabel() }} · {{ $review->created_at?->format('d.m.Y H:i') }}</span>

                        <div class="d-flex align-items-center justify-content-between gap-2 mt-auto">
                            <div>
                                @if ($review->status === \App\Models\Review::STATUS_APPROVED)
                                    <span class="badge bg-success text-white">{{ __('dictt.status_approved') }}</span>
                                @elseif ($review->status === \App\Models\Review::STATUS_REJECTED)
                                    <span class="badge bg-danger text-white">{{ __('dictt.status_rejected') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ __('dictt.status_pending') }}</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @can('update', $review)
                                    <button type="button" wire:click="edit({{ $review->id }})"
                                        class="btn btn-sm btn-primary" title="{{ __('dictt.edit') }}">
                                        <i class="fa fa-pen w-4"></i>
                                    </button>
                                @endcan
                                <button type="button" wire:click="confirmDelete({{ $review->id }})"
                                    wire:confirm="{{ __('dictt.review_delete_confirm') }}"
                                    class="btn btn-sm btn-danger" title="{{ __('dictt.delete') }}">
                                    <i class="fa fa-trash w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-4">{{ __('dictt.reviews_empty') }}</div>
        @endforelse
    </div>
</div>
