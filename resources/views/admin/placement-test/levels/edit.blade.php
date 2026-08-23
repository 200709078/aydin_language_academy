<x-app-layout>
    <x-slot name="header">{{ __('dictt.levels') }} / {{ $placementTestLevel->code }} {{ __('dictt.edit') }}</x-slot>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('placement_test_levels_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.back') }}
                </a>
            </h5>

            <form method="POST" action="{{ route('placement_test_levels_update', $placementTestLevel) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label">{{ __('dictt.level') }}</label>
                        <input id="code" type="text" class="form-control" value="{{ $placementTestLevel->code }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sequence" class="form-label">{{ __('dictt.order') }}</label>
                        <input id="sequence" type="text" class="form-control" value="{{ $placementTestLevel->sequence }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="has_exam" class="form-label">{{ __('dictt.exam_status') }}</label>
                        <input id="has_exam" type="text" class="form-control"
                            value="{{ $placementTestLevel->has_exam ? __('dictt.pt_has_exam') : __('dictt.pt_no_exam') }}" readonly>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1"
                        @checked(old('is_active', $placementTestLevel->is_active))>
                    <label for="is_active" class="form-check-label">{{ __('dictt.pt_level_active_label') }}</label>
                </div>

                @if ($placementTestLevel->has_exam)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="question_count" class="form-label">{{ __('dictt.target_question_count') }}</label>
                            <input id="question_count" name="question_count" type="number" min="0" max="65535"
                                class="form-control @error('question_count') is-invalid @enderror"
                                value="{{ old('question_count', $placementTestLevel->question_count) }}">
                            @error('question_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('dictt.pt_question_count_help') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
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
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        {{ __('dictt.pt_c2_alert') }}
                    </div>
                @endif

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('dictt.save_settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
