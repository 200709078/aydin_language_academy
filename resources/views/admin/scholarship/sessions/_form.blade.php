@php
    $currentSession = $session ?? null;
    $periodId = $currentSession?->period_id ?? old('period_id', $selectedPeriodId ?? null);
    $selectedPeriod = $periods->firstWhere('id', $periodId);
    $hasChoices = $periods->isNotEmpty() && $branches->isNotEmpty() && $groups->isNotEmpty();
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.scholarship.sessions.index', ['period_id' => $currentSession?->period_id ?? $selectedPeriodId ?? null]) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="scholarship-session-form" class="btn btn-success btn-sm" @disabled(!$hasChoices)>{{ __('dictt.save') }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        @if (!$hasChoices)
            <div class="alert alert-warning" role="alert">
                <p>{{ __('scholarship.session_prerequisites') }}</p>
                <div class="d-flex flex-wrap gap-2">
                    @if ($periods->isEmpty())<a href="{{ route('admin.scholarship.periods.create') }}" class="btn btn-sm btn-outline-primary">{{ __('dictt.scholarship_period_add') }}</a>@endif
                    @if ($branches->isEmpty())<a href="{{ route('admin.scholarship.definitions.create', ['type' => 'branch']) }}" class="btn btn-sm btn-outline-primary">{{ __('scholarship.branches') }}</a>@endif
                    @if ($groups->isEmpty())<a href="{{ route('admin.scholarship.definitions.create', ['type' => 'exam_group']) }}" class="btn btn-sm btn-outline-primary">{{ __('scholarship.exam_groups') }}</a>@endif
                </div>
            </div>
        @endif
        @if ($currentSession?->archived_at !== null)
            <div class="alert alert-secondary" role="status">{{ __('scholarship.session_archived_help') }}</div>
        @endif

        <form id="scholarship-session-form" method="POST" action="{{ $action }}" x-data="{}">
            @csrf
            @if ($method !== 'POST') @method($method) @endif

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="period_id" class="form-label">{{ __('scholarship.period') }}</label>
                    @if ($currentSession)
                        <input type="hidden" name="period_id" value="{{ $currentSession->period_id }}">
                        <input id="period_id" type="text" readonly value="{{ $selectedPeriod?->title }}" class="form-control @error('period_id') is-invalid @enderror" aria-describedby="period-help">
                        <div id="period-help" class="form-text">{{ __('scholarship.session_period_fixed') }}</div>
                    @else
                        <select id="period_id" name="period_id" required class="form-select @error('period_id') is-invalid @enderror"
                            x-on:change="$refs.examDate.min = $event.target.selectedOptions[0].dataset.start || ''; $refs.examDate.max = $event.target.selectedOptions[0].dataset.end || ''">
                            <option value="">{{ __('scholarship.choose_period') }}</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" @selected($periodId == $period->id)
                                    data-start="{{ $period->exam_starts_on->format('Y-m-d') }}" data-end="{{ $period->exam_ends_on->format('Y-m-d') }}">
                                    {{ $period->title }} ({{ $period->exam_starts_on->format('d.m.Y') }}–{{ $period->exam_ends_on->format('d.m.Y') }}){{ $period->is_active ? '' : ' ('.__('dictt.passive').')' }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('period_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @foreach (['branch_id' => [$branches, 'branch'], 'exam_group_id' => [$groups, 'exam_group']] as $field => [$options, $label])
                    <div class="col-md-6 mb-3">
                        <label for="{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" required class="form-select @error($field) is-invalid @enderror">
                            <option value="">{{ __('scholarship.choose_'.$label) }}</option>
                            @foreach ($options as $option)
                                <option value="{{ $option->id }}" @selected(old($field, $currentSession?->{$field}) == $option->id)>{{ $option->name }}{{ $option->is_active ? '' : ' ('.__('dictt.passive').')' }}</option>
                            @endforeach
                        </select>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                <div class="col-md-6 mb-3">
                    <label for="exam_title" class="form-label">{{ __('scholarship.exam_title') }}</label>
                    <input id="exam_title" name="exam_title" type="text" maxlength="150" required value="{{ old('exam_title', $currentSession?->exam_title) }}" class="form-control @error('exam_title') is-invalid @enderror">
                    @error('exam_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="exam_date" class="form-label">{{ __('scholarship.exam_date') }}</label>
                    <input id="exam_date" name="exam_date" type="date" required x-ref="examDate" value="{{ old('exam_date', $currentSession?->exam_date?->format('Y-m-d')) }}"
                        min="{{ $selectedPeriod?->exam_starts_on?->format('Y-m-d') }}" max="{{ $selectedPeriod?->exam_ends_on?->format('Y-m-d') }}"
                        class="form-control @error('exam_date') is-invalid @enderror" aria-describedby="exam-date-help">
                    <div id="exam-date-help" class="form-text">{{ __('scholarship.session_date_help') }}</div>
                    @error('exam_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @foreach (['starts_at', 'ends_at'] as $field)
                    <div class="col-md-6 mb-3">
                        <label for="{{ $field }}" class="form-label">{{ __('scholarship.'.$field) }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="time" step="1" required value="{{ old($field, $currentSession?->{$field}) }}" class="form-control @error($field) is-invalid @enderror">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                <div class="col-md-6 mb-3">
                    <label for="capacity" class="form-label">{{ __('scholarship.capacity') }}</label>
                    <input id="capacity" name="capacity" type="number" min="{{ max(1, $currentSession?->applications_count ?? 0) }}" max="4294967295" step="1" required
                        value="{{ old('capacity', $currentSession?->capacity) }}" class="form-control @error('capacity') is-invalid @enderror" aria-describedby="capacity-help">
                    <div id="capacity-help" class="form-text">{{ __('scholarship.capacity_help') }} @if ($currentSession) {{ __('scholarship.current_occupancy', ['count' => $currentSession->applications_count]) }} @endif</div>
                    @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check form-switch">
                        <input id="is_active" name="is_active" type="checkbox" value="1" role="switch" @checked(old('is_active', $currentSession?->is_active ?? true))
                            class="form-check-input @error('is_active') is-invalid @enderror" aria-describedby="session-state-help">
                        <label for="is_active" class="form-check-label">{{ __('scholarship.session_active') }}</label>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <p id="session-state-help" class="text-muted small">{{ __('scholarship.session_states_help') }}</p>
            <p class="text-muted small mb-0">{{ __('dictt.scholarship_timezone', ['timezone' => config('app.timezone')]) }}</p>
        </form>
    </div>
</div>
