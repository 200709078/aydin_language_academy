@php
    $currentPeriod = $period ?? null;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.scholarship.periods.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="scholarship-period-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="scholarship-period-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="mb-3">
                <label for="title" class="form-label">{{ __('dictt.title') }}</label>
                <input id="title" name="title" type="text" maxlength="255" required autofocus
                    value="{{ old('title', $currentPeriod?->title) }}" class="form-control @error('title') is-invalid @enderror">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('dictt.description') }}</label>
                <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $currentPeriod?->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <p class="text-muted small">{{ __('dictt.scholarship_timezone', ['timezone' => config('app.timezone')]) }}</p>
            <div class="row">
                @foreach (['applications_open_at', 'applications_close_at'] as $field)
                    <div class="col-md-6 mb-3">
                        <label for="{{ $field }}" class="form-label">{{ __('dictt.scholarship_'.$field) }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="datetime-local" step="1" required
                            value="{{ old($field, $currentPeriod?->{$field}?->format('Y-m-d\TH:i:s')) }}"
                            class="form-control @error($field) is-invalid @enderror">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                @foreach (['exam_starts_on', 'exam_ends_on'] as $field)
                    <div class="col-md-6 mb-3">
                        <label for="{{ $field }}" class="form-label">{{ __('dictt.scholarship_'.$field) }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="date" required
                            value="{{ old($field, $currentPeriod?->{$field}?->format('Y-m-d')) }}"
                            class="form-control @error($field) is-invalid @enderror">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>

            <div class="row">
                @foreach (['is_active' => 'scholarship_period_active', 'applications_open' => 'scholarship_applications_open'] as $field => $label)
                    <div class="col-md-6 mb-3">
                        <input type="hidden" name="{{ $field }}" value="0">
                        <div class="form-check form-switch">
                            <input id="{{ $field }}" name="{{ $field }}" type="checkbox" value="1" role="switch"
                                class="form-check-input @error($field) is-invalid @enderror"
                                @checked(old($field, $currentPeriod?->{$field} ?? false)) aria-describedby="period-access-help">
                            <label for="{{ $field }}" class="form-check-label">{{ __('dictt.'.$label) }}</label>
                            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                @endforeach
            </div>
            <p id="period-access-help" class="text-muted small mb-0">{{ __('dictt.scholarship_period_access_help') }}</p>
        </form>
    </div>
</div>
