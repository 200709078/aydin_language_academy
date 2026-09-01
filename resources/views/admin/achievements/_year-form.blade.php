@php
    $currentYear = $achievementYear ?? null;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.achievement_back') }}
                    </a>
                    <button type="submit" form="achievement-year-form" class="btn btn-success btn-sm">
                        {{ $submitLabel }}
                    </button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="achievement-year-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="year" class="form-label">{{ __('dictt.achievement_year') }}</label>
                    <input id="year" type="number" name="year" min="1900" max="9999"
                        value="{{ old('year', $currentYear?->year ?? now()->year) }}"
                        class="form-control @error('year') is-invalid @enderror" required>
                    @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-9 mb-3">
                    <label for="title" class="form-label">{{ __('dictt.achievement_year_title') }}</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $currentYear?->title) }}"
                        class="form-control @error('title') is-invalid @enderror" maxlength="255" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">{{ __('dictt.description') }}</label>
                <textarea id="description" name="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $currentYear?->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </form>
    </div>
</div>
