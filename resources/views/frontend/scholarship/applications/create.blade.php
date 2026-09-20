<x-frontend-profile-layout :header="__('scholarship.member_form_title')">
    @php
        $sessions = collect($period['sessions']);
        $branchId = $selectedSession['branch_id'] ?? null;
        $groupId = $selectedSession['exam_group_id'] ?? null;
        $examTitle = $selectedSession['exam_title'] ?? null;
    @endphp
    <div class="container py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <h1 class="h2 mb-0">{{ __('scholarship.member_form_title') }}</h1>
            <a href="{{ route('frontend.scholarship.exams.index') }}" class="btn btn-outline-secondary">{{ __('scholarship.member_exams_title') }}</a>
        </div>
        <div class="bg-light rounded p-3 p-md-4">
            <h2 class="h4 text-break">{{ $period['title'] }}</h2>
            <p>{{ __('scholarship.member_application_dates') }}: {{ $period['applications_open_at'] }} – {{ $period['applications_close_at'] }}</p>
            @if ($selectionUnavailable)
                <div class="alert alert-warning" role="status">{{ __('scholarship.member_selection_unavailable') }}</div>
            @endif
            @if ($schools->isEmpty() || $levels->isEmpty())
                <div class="alert alert-warning">
                    {{ __('scholarship.member_definitions_missing') }}
                    <a href="{{ route('frontend.contact') }}" class="alert-link">{{ __('scholarship.member_contact_admin') }}</a>
                </div>
            @endif

            <form id="scholarship-application-form" data-scholarship-form method="POST"
                action="{{ route('frontend.scholarship.applications.create', $period['id']) }}" onsubmit="return false;">
                @csrf
                <div class="mb-3">
                    <label for="student_name" class="form-label">{{ __('scholarship.student_name') }}</label>
                    <input id="student_name" class="form-control" value="{{ $student['name'] }}" readonly aria-describedby="student-help">
                    <div id="student-help" class="form-text">{{ __('scholarship.member_student_help') }}</div>
                </div>
                <div class="row">
                    @foreach (['school_id' => [$schools, 'current_school'], 'student_level_id' => [$levels, 'current_level']] as $field => [$options, $label])
                        <div class="col-md-6 mb-3">
                            <label for="{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                            <select id="{{ $field }}" name="{{ $field }}" required class="form-select @error($field) is-invalid @enderror">
                                <option value="">{{ __('scholarship.member_choose') }}</option>
                                @foreach ($options as $option)
                                    <option value="{{ $option->id }}" @selected(old($field) == $option->id)>{{ $option->name }}</option>
                                @endforeach
                            </select>
                            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>

                <fieldset class="border-top pt-3 mt-2">
                    <legend class="h5">{{ __('scholarship.member_exam_selection') }}</legend>
                    <p id="group-help" class="form-text mb-3">{{ __('scholarship.member_group_help') }}</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">{{ __('scholarship.branch') }}</label>
                            <select id="branch_id" name="branch_id" required class="form-select">
                                <option value="">{{ __('scholarship.choose_branch') }}</option>
                                @foreach ($sessions->unique('branch_id') as $session)
                                    <option value="{{ $session['branch_id'] }}" @selected($branchId === $session['branch_id'])>{{ $session['branch'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="exam_group_id" class="form-label">{{ __('scholarship.exam_group') }}</label>
                            <select id="exam_group_id" name="exam_group_id" required class="form-select" aria-describedby="group-help" @disabled($branchId === null)>
                                <option value="">{{ __('scholarship.choose_exam_group') }}</option>
                                @foreach ($sessions->unique(fn ($session) => $session['branch_id'].':'.$session['exam_group_id']) as $session)
                                    <option value="{{ $session['exam_group_id'] }}" data-branch-id="{{ $session['branch_id'] }}"
                                        @selected($branchId === $session['branch_id'] && $groupId === $session['exam_group_id'])
                                        @disabled($branchId !== $session['branch_id']) @if ($branchId !== $session['branch_id']) hidden @endif>{{ $session['exam_group'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="exam_title" class="form-label">{{ __('scholarship.exam_title') }}</label>
                            <select id="exam_title" name="exam_title" required class="form-select" @disabled($groupId === null)>
                                <option value="">{{ __('scholarship.member_choose_exam') }}</option>
                                @foreach ($sessions->unique(fn ($session) => json_encode([$session['branch_id'], $session['exam_group_id'], $session['exam_title']])) as $session)
                                    @php($matchesGroup = $branchId === $session['branch_id'] && $groupId === $session['exam_group_id'])
                                    <option value="{{ $session['exam_title'] }}" data-branch-id="{{ $session['branch_id'] }}" data-group-id="{{ $session['exam_group_id'] }}"
                                        @selected($matchesGroup && $examTitle === $session['exam_title'])
                                        @disabled(! $matchesGroup) @if (! $matchesGroup) hidden @endif>{{ $session['exam_title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="session_id" class="form-label">{{ __('scholarship.sessions') }}</label>
                            <select id="session_id" name="session_id" required class="form-select @error('session_id') is-invalid @enderror" @disabled($examTitle === null)>
                                <option value="">{{ __('scholarship.member_choose_session') }}</option>
                                @foreach ($sessions as $session)
                                    @php($matchesExam = $branchId === $session['branch_id'] && $groupId === $session['exam_group_id'] && $examTitle === $session['exam_title'])
                                    <option value="{{ $session['id'] }}" data-branch-id="{{ $session['branch_id'] }}" data-group-id="{{ $session['exam_group_id'] }}" data-exam-title="{{ $session['exam_title'] }}"
                                        data-state="{{ $session['state'] }}" data-exam-date="{{ $session['exam_date'] }}" data-starts-at="{{ $session['starts_at'] }}" data-ends-at="{{ $session['ends_at'] }}"
                                        data-capacity="{{ $session['capacity'] }}" data-remaining="{{ $session['remaining'] }}"
                                        @selected(($selectedSession['id'] ?? null) === $session['id'])
                                        @disabled(! $matchesExam || $session['state'] !== 'available') @if (! $matchesExam) hidden @endif>
                                        {{ $session['exam_date'] }} · {{ $session['starts_at'] }}–{{ $session['ends_at'] }} · {{ __('scholarship.member_remaining_seats', ['count' => $session['remaining']]) }}
                                        @if ($session['state'] === 'full') · {{ __('scholarship.session_full') }} @elseif ($session['state'] === 'suspended') · {{ __('scholarship.session_suspended') }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="alert alert-info" data-no-sessions hidden role="status">{{ __('scholarship.member_no_available_sessions') }}</div>
                    <div class="alert alert-warning" data-suspended-sessions hidden>
                        {{ __('scholarship.member_suspended_help') }}
                        <a href="{{ route('frontend.contact') }}" class="alert-link">{{ __('scholarship.member_contact_admin') }}</a>
                    </div>
                    <div class="row" aria-live="polite">
                        @foreach (['exam_date' => 'examDate', 'starts_at' => 'startsAt', 'ends_at' => 'endsAt', 'capacity' => 'capacity', 'remaining' => 'remaining'] as $field => $dataKey)
                            <div class="col-sm-6 col-lg-4 mb-3">
                                <label for="chosen_{{ $field }}" class="form-label">{{ __('scholarship.'.($field === 'remaining' ? 'member_remaining_label' : $field)) }}</label>
                                <input id="chosen_{{ $field }}" class="form-control" data-session-detail="{{ $dataKey }}" value="{{ $selectedSession[$field] ?? '—' }}" readonly>
                            </div>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="border-top pt-3 mt-2">
                    <legend class="h5">{{ __('scholarship.account_contact') }}</legend>
                    <div class="row">
                        @foreach (['email', 'phone'] as $field)
                            <div class="col-md-6 mb-3">
                                <label for="account_{{ $field }}" class="form-label">{{ __('scholarship.account_'.$field) }}</label>
                                <input id="account_{{ $field }}" class="form-control" value="{{ $student[$field] ?: __('scholarship.member_contact_missing') }}" readonly>
                            </div>
                        @endforeach
                    </div>
                    <div class="alert alert-info">
                        <p class="mb-2">{{ __('scholarship.member_contact_help') }}</p>
                        <a href="{{ route('profile.show') }}" class="alert-link" target="_blank" rel="noopener">{{ __('scholarship.member_update_profile') }}</a>
                    </div>
                </fieldset>
                <p id="submission-help" class="text-muted">{{ __('scholarship.member_submission_pending') }}</p>
                <button type="submit" class="btn btn-primary" disabled aria-describedby="submission-help">{{ __('scholarship.member_submit') }}</button>
            </form>
        </div>
    </div>
    <x-slot name="js">
        <script src="{{ asset('frontend/js/scholarship-application-form.js') }}" defer></script>
    </x-slot>
</x-frontend-profile-layout>
