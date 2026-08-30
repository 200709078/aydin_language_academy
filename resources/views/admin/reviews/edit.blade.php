<x-app-layout>
    <x-slot name="header">{{ __('dictt.review') }} {{ __('dictt.edit') }} - {{ $review->user?->name ?? ('#' . $review->id) }}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0 d-flex gap-2">
                    <a href="{{ route('reviews_list') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
                    <button type="submit" form="review-form" class="btn btn-success btn-sm">
                        {{ __('dictt.update') }}</button>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.review') }} {{ __('dictt.edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>

            <dl class="row mb-3 small">
                <dt class="col-sm-2">{{ __('dictt.member') }}</dt>
                <dd class="col-sm-10">{{ $review->user?->name ?? ('#' . $review->id) }}</dd>
                <dt class="col-sm-2">{{ __('dictt.date') }}</dt>
                <dd class="col-sm-10">{{ $review->created_at?->format('d.m.Y H:i') }}</dd>
                @if ($review->status === \App\Models\Review::STATUS_APPROVED && $review->approver)
                    <dt class="col-sm-2">{{ __('dictt.approve') }}</dt>
                    <dd class="col-sm-10">{{ $review->approver->name }} — {{ $review->approved_at?->format('d.m.Y H:i') }}</dd>
                @endif
            </dl>

            <form id="review-form" method="POST" action="{{ route('review_update', $review->id) }}">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-md-8 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.content') }}</label>
                            <textarea name="content" class="form-control" rows="4"
                                required>{{ old('content', $review->content) }}</textarea>
                            @error('content')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.rating') }}</label>
                            <select name="rating" class="form-select" required>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected((string) old('rating', $review->rating) === (string) $i)>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('rating')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('dictt.branch') }}</label>
                            <select name="branch" class="form-select">
                                <option value="" @selected(old('branch', $review->branch) === null)>{{ __('dictt.none') }}</option>
                                @foreach (\App\Models\Review::BRANCHES as $branch)
                                    <option value="{{ $branch }}" @selected(old('branch', $review->branch) === $branch)>{{ __('dictt.branch_' . $branch) }}</option>
                                @endforeach
                            </select>
                            @error('branch')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('dictt.status') }}</label>
                            <select name="status" class="form-select" required>
                                @foreach ([\App\Models\Review::STATUS_PENDING, \App\Models\Review::STATUS_APPROVED, \App\Models\Review::STATUS_REJECTED] as $statusOption)
                                    <option value="{{ $statusOption }}" @selected(old('status', $review->status) === $statusOption)>{{ __('dictt.status_' . $statusOption) }}</option>
                                @endforeach
                            </select>
                            @error('status')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('dictt.display_order') }}</label>
                            <input type="number" name="display_order" min="0" class="form-control" value="{{ old('display_order', $review->display_order) }}">
                            @error('display_order')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
