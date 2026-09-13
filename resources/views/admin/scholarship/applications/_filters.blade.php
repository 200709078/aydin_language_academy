<form method="GET" action="{{ route('admin.scholarship.applications.index') }}" class="mb-4">
    <div class="row g-3">
        @foreach (['period_id' => [$periods, 'period', 'title'], 'branch_id' => [$branches, 'branch', 'name'], 'exam_group_id' => [$groups, 'exam_group', 'name']] as $field => [$options, $label, $textField])
            <div class="col-md-4">
                <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                <select id="filter-{{ $field }}" name="{{ $field }}" class="form-select">
                    <option value="">{{ __('scholarship.filter_all') }}</option>
                    @foreach ($options as $option)<option value="{{ $option->id }}" @selected(($filters[$field] ?? '') == $option->id)>{{ $option->{$textField} }}</option>@endforeach
                </select>
            </div>
        @endforeach
        <div class="col-md-8">
            <label for="filter-q" class="form-label">{{ __('scholarship.application_search') }}</label>
            <input id="filter-q" name="q" value="{{ $filters['q'] ?? '' }}" type="search" maxlength="255" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="filter-status" class="form-label">{{ __('scholarship.approval_status') }}</label>
            <select id="filter-status" name="status" class="form-select">
                <option value="">{{ __('scholarship.filter_all') }}</option>
                @foreach (['pending', 'approved'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ __('scholarship.approval_'.$status) }}</option>@endforeach
            </select>
        </div>
    </div>
    <details class="mt-3" @if (count(array_diff(array_keys($filters), ['period_id', 'branch_id', 'exam_group_id', 'q', 'status'])) > 0) open @endif>
        <summary class="text-primary">{{ __('scholarship.more_filters') }}</summary>
        <div class="row g-3 mt-1">
            <div class="col-12">
                <label for="filter-session_id" class="form-label">{{ __('scholarship.sessions') }}</label>
                <select id="filter-session_id" name="session_id" class="form-select">
                    <option value="">{{ __('scholarship.filter_all') }}</option>
                    @foreach ($sessions as $session)<option value="{{ $session->id }}" @selected(($filters['session_id'] ?? '') == $session->id)>@include('admin.scholarship.applications._session-label')</option>@endforeach
                </select>
            </div>
            @foreach (['school_id' => [$schools, 'current_school'], 'student_level_id' => [$levels, 'current_level']] as $field => [$options, $label])
                <div class="col-md-6">
                    <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                    <select id="filter-{{ $field }}" name="{{ $field }}" class="form-select">
                        <option value="">{{ __('scholarship.filter_all') }}</option>
                        @foreach ($options as $option)<option value="{{ $option->id }}" @selected(($filters[$field] ?? '') == $option->id)>{{ $option->name }}</option>@endforeach
                    </select>
                </div>
            @endforeach
            @foreach (['exam_date' => 'date', 'starts_at' => 'time', 'ends_at' => 'time'] as $field => $type)
                <div class="col-md-4">
                    <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$field) }}</label>
                    <input id="filter-{{ $field }}" name="{{ $field }}" value="{{ $filters[$field] ?? '' }}" type="{{ $type }}" @if ($type === 'time') step="1" @endif class="form-control">
                </div>
            @endforeach
            @foreach ([
                'session_state' => ['session_state', ['active' => 'session_active', 'suspended' => 'session_suspended', 'archived' => 'session_archived']],
                'attendance_status' => ['attendance', ['unmarked' => 'attendance_unmarked', 'attended' => 'attendance_attended', 'absent' => 'attendance_absent']],
                'application_contact_status' => ['application_contact', ['unreached' => 'contact_unreached', 'reached' => 'contact_reached']],
                'result_contact_status' => ['result_contact', ['unreached' => 'contact_unreached', 'reached' => 'contact_reached']],
                'application_published' => ['application_publication', [0 => 'unpublished', 1 => 'published']],
                'result_published' => ['result_publication', [0 => 'unpublished', 1 => 'published']],
            ] as $field => [$label, $options])
                <div class="col-md-4">
                    <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                    <select id="filter-{{ $field }}" name="{{ $field }}" class="form-select">
                        <option value="">{{ __('scholarship.filter_all') }}</option>
                        @foreach ($options as $value => $text)<option value="{{ $value }}" @selected(isset($filters[$field]) && (string) $filters[$field] === (string) $value)>{{ __('scholarship.'.$text) }}</option>@endforeach
                    </select>
                </div>
            @endforeach
            <div class="col-md-4">
                <label for="filter-scholarship_percentage" class="form-label">{{ __('scholarship.scholarship_percentage') }}</label>
                <select id="filter-scholarship_percentage" name="scholarship_percentage" class="form-select">
                    <option value="">{{ __('scholarship.filter_all') }}</option>
                    <option value="unset" @selected(($filters['scholarship_percentage'] ?? '') === 'unset')>{{ __('scholarship.not_entered') }}</option>
                    @foreach (range(100, 0, -10) as $percentage)<option value="{{ $percentage }}" @selected(isset($filters['scholarship_percentage']) && (string) $filters['scholarship_percentage'] === (string) $percentage)>%{{ $percentage }}</option>@endforeach
                </select>
            </div>
        </div>
    </details>
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-sm btn-primary">{{ __('scholarship.filter_apply') }}</button>
        <a href="{{ route('admin.scholarship.applications.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('scholarship.filter_clear') }}</a>
    </div>
</form>
