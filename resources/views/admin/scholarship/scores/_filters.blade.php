<form method="GET" action="{{ route($filterRoute ?? 'admin.scholarship.scores.index') }}" class="mb-4">
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
        <div class="col-md-6 col-xl-3">
            <label for="filter-score_state" class="form-label">{{ __('scholarship.score_state') }}</label>
            <select id="filter-score_state" name="score_state" class="form-select">
                <option value="">{{ __('scholarship.filter_all') }}</option>
                @foreach (['entered', 'missing'] as $state)<option value="{{ $state }}" @selected(($filters['score_state'] ?? '') === $state)>{{ __('scholarship.score_'.$state) }}</option>@endforeach
            </select>
        </div>
        @foreach (['score_min', 'score_max'] as $field)
            <div class="col-md-6 col-xl-3">
                <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$field) }}</label>
                <input id="filter-{{ $field }}" name="{{ $field }}" value="{{ $filters[$field] ?? '' }}" type="number" min="0" max="100" step="1" class="form-control">
            </div>
        @endforeach
        <div class="col-md-6 col-xl-3">
            <label for="filter-sort" class="form-label">{{ __('scholarship.score_sort') }}</label>
            <select id="filter-sort" name="sort" class="form-select">
                @foreach (['score_desc' => 'score_desc', 'score_asc' => 'score_asc', 'name' => 'score_sort_name'] as $sort => $label)<option value="{{ $sort }}" @selected(($filters['sort'] ?? 'score_desc') === $sort)>{{ __('scholarship.'.$label) }}</option>@endforeach
            </select>
        </div>
        @if ($showAwardFilter ?? false)
            <div class="col-md-4">
                <label for="filter-scholarship_percentage" class="form-label">{{ __('scholarship.scholarship_percentage') }}</label>
                <select id="filter-scholarship_percentage" name="scholarship_percentage" class="form-select">
                    <option value="">{{ __('scholarship.filter_all') }}</option>
                    <option value="unset" @selected(($filters['scholarship_percentage'] ?? '') === 'unset')>{{ __('scholarship.award_unset') }}</option>
                    @foreach (range(100, 0, -10) as $percentage)
                        <option value="{{ $percentage }}" @selected(isset($filters['scholarship_percentage']) && (string) $filters['scholarship_percentage'] === (string) $percentage)>{{ $percentage === 0 ? __('scholarship.award_none') : '%'.$percentage }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12">
            <label for="filter-q" class="form-label">{{ __('scholarship.application_search') }}</label>
            <input id="filter-q" name="q" value="{{ $filters['q'] ?? '' }}" type="search" maxlength="255" class="form-control">
        </div>
    </div>
    <details class="mt-3" @if (collect($filters)->keys()->intersect(['session_id', 'school_id', 'student_level_id', 'exam_date', 'starts_at', 'ends_at', 'attendance_status'])->isNotEmpty()) open @endif>
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
                <div class="col-md-4">
                    <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                    <select id="filter-{{ $field }}" name="{{ $field }}" class="form-select">
                        <option value="">{{ __('scholarship.filter_all') }}</option>
                        @foreach ($options as $option)<option value="{{ $option->id }}" @selected(($filters[$field] ?? '') == $option->id)>{{ $option->name }}</option>@endforeach
                    </select>
                </div>
            @endforeach
            <div class="col-md-4">
                <label for="filter-attendance_status" class="form-label">{{ __('scholarship.attendance') }}</label>
                <select id="filter-attendance_status" name="attendance_status" class="form-select">
                    <option value="">{{ __('scholarship.filter_all') }}</option>
                    @foreach (['unmarked', 'attended', 'absent'] as $status)<option value="{{ $status }}" @selected(($filters['attendance_status'] ?? '') === $status)>{{ __('scholarship.attendance_'.$status) }}</option>@endforeach
                </select>
            </div>
            @foreach (['exam_date' => 'date', 'starts_at' => 'time', 'ends_at' => 'time'] as $field => $type)
                <div class="col-md-4">
                    <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$field) }}</label>
                    <input id="filter-{{ $field }}" name="{{ $field }}" value="{{ $filters[$field] ?? '' }}" type="{{ $type }}" @if ($type === 'time') step="1" @endif class="form-control">
                </div>
            @endforeach
        </div>
    </details>
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-sm btn-primary">{{ __('scholarship.filter_apply') }}</button>
        <a href="{{ route($filterRoute ?? 'admin.scholarship.scores.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('scholarship.filter_clear') }}</a>
    </div>
</form>
