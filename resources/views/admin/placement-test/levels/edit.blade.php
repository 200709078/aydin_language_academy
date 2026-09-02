<x-app-layout>
    <x-slot name="header">{{ __('dictt.levels') }} / {{ $placementTestLevel->code }} {{ __('dictt.edit') }}</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('placement_test_levels_list') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}
                        </a>
                        <button type="submit" form="placement-test-level-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">
                    {{ __('dictt.placement_test') }} — {{ __('dictt.level') }} {{ __('dictt.edit') }}
                </h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>

            <form id="placement-test-level-form" method="POST" action="{{ route('placement_test_levels_update', $placementTestLevel) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="code" class="form-label">{{ __('dictt.level') }}</label>
                    <input id="code" type="text" class="form-control" value="{{ $placementTestLevel->code }}" readonly>
                </div>

                <input type="hidden" name="is_active"
                    value="{{ old('is_active', $placementTestLevel->is_active) ? '1' : '0' }}">

                @if ($placementTestLevel->has_exam)
                    <div class="mb-3">
                        <label for="pass_percentage" class="form-label">{{ __('dictt.pass_percentage') }}</label>
                        @php
                            $passPercentage = (int) old('pass_percentage', $placementTestLevel->pass_percentage);
                        @endphp
                        <div x-data="{ passPercentage: {{ $passPercentage }} }">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">0% – 100%</span>
                                <span class="badge text-bg-primary" x-text="passPercentage + '%'">{{ $passPercentage }}%</span>
                            </div>
                            <input id="pass_percentage" name="pass_percentage" type="range" min="0" max="100" step="5"
                                class="form-range @error('pass_percentage') is-invalid @enderror"
                                x-model.number="passPercentage" value="{{ $passPercentage }}" required>
                            @error('pass_percentage')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">{{ __('dictt.pt_slider_help') }}</div>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        {{ __('dictt.pt_c2_alert') }}
                    </div>
                @endif

            </form>
        </div>
    </div>
</x-app-layout>
