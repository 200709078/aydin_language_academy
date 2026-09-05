@php
    $currentSlogan = $slogan ?? null;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.slogans.index') }}" class="btn btn-sm btn-secondary"
                        title="{{ __('dictt.back_short') }}">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="slogan-form" class="btn btn-success btn-sm"
                        title="{{ $submitLabel }}">{{ $submitLabel }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="slogan-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title_tr" class="form-label">{{ __('dictt.slogan_title_tr') }}</label>
                    <textarea id="title_tr" name="title_tr" rows="4" maxlength="255" required autofocus
                        class="form-control @error('title_tr') is-invalid @enderror">{{ old('title_tr', $currentSlogan?->title_tr) }}</textarea>
                    @error('title_tr')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="title_en" class="form-label">{{ __('dictt.slogan_title_en') }}</label>
                    <textarea id="title_en" name="title_en" rows="4" maxlength="255" required
                        class="form-control @error('title_en') is-invalid @enderror">{{ old('title_en', $currentSlogan?->title_en) }}</textarea>
                    @error('title_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </form>
    </div>
</div>
