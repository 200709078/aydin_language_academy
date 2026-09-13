<x-app-layout>
    <x-slot name="header">{{ __('scholarship.attendance_management') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('scholarship.attendance_management') }}</h5>
            <p class="text-muted small">{{ __('scholarship.attendance_help') }}</p>
            <form method="GET" action="{{ route('admin.scholarship.attendance.index') }}" class="mb-4">
                <div class="row g-3">
                    @foreach (['period_id' => [$periods, 'period', 'title'], 'branch_id' => [$branches, 'branch', 'name'], 'exam_group_id' => [$groups, 'exam_group', 'name'], 'student_level_id' => [$levels, 'current_level', 'name']] as $field => [$options, $label, $textField])
                        <div class="col-md-6 col-xl-3">
                            <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                            <select id="filter-{{ $field }}" name="{{ $field }}" class="form-select">
                                <option value="">{{ __('scholarship.filter_all') }}</option>
                                @foreach ($options as $option)<option value="{{ $option->id }}" @selected(($filters[$field] ?? '') == $option->id)>{{ $option->{$textField} }}</option>@endforeach
                            </select>
                        </div>
                    @endforeach
                    @foreach (['exam_date' => 'date', 'starts_at' => 'time', 'ends_at' => 'time'] as $field => $type)
                        <div class="col-md-6 col-xl-3">
                            <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$field) }}</label>
                            <input id="filter-{{ $field }}" name="{{ $field }}" value="{{ $filters[$field] ?? '' }}" type="{{ $type }}" @if ($type === 'time') step="1" @endif class="form-control">
                        </div>
                    @endforeach
                    <div class="col-md-6 col-xl-3">
                        <label for="filter-attendance_status" class="form-label">{{ __('scholarship.attendance') }}</label>
                        <select id="filter-attendance_status" name="attendance_status" class="form-select">
                            <option value="">{{ __('scholarship.filter_all') }}</option>
                            @foreach (['unmarked', 'attended', 'absent'] as $status)<option value="{{ $status }}" @selected(($filters['attendance_status'] ?? '') === $status)>{{ __('scholarship.attendance_'.$status) }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="filter-q" class="form-label">{{ __('scholarship.application_search') }}</label>
                        <input id="filter-q" name="q" value="{{ $filters['q'] ?? '' }}" type="search" maxlength="255" class="form-control">
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">{{ __('scholarship.filter_apply') }}</button>
                    <a href="{{ route('admin.scholarship.attendance.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('scholarship.filter_clear') }}</a>
                </div>
            </form>
            <p class="text-muted small">{{ __('scholarship.attendance_total', ['total' => $applications->total()]) }}</p>
            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead><tr>
                        <th scope="col">{{ __('scholarship.student_name') }} / {{ __('scholarship.application_number') }}</th>
                        <th scope="col">{{ __('scholarship.current_school') }} / {{ __('scholarship.current_level') }}</th>
                        <th scope="col">{{ __('scholarship.sessions') }}</th>
                        <th scope="col">{{ __('scholarship.approval_status') }}</th>
                        <th scope="col">{{ __('scholarship.attendance') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr id="attendance-row-{{ $application->id }}">
                                <td class="text-break"><a href="{{ route('admin.scholarship.applications.show', $application) }}">{{ $application->student_name_snapshot }}</a><div class="small">{{ $application->application_number }}</div></td>
                                <td class="text-break">{{ $application->school_name_snapshot }}<div class="small">{{ $application->student_level_name_snapshot }}</div></td>
                                <td class="text-break"><div>{{ $application->period->title }}</div><div>{{ $application->session->branch->name }} / {{ $application->session->examGroup->name }}</div><div class="small">{{ $application->session->exam_title }}</div><div class="small">{{ $application->session->exam_date->format('d.m.Y') }} {{ $application->session->starts_at }}–{{ $application->session->ends_at }}</div></td>
                                <td><span class="badge {{ $application->status === 'approved' ? 'text-bg-success' : 'text-bg-warning' }}">{{ __('scholarship.approval_'.$application->status) }}</span></td>
                                <td>
                                    <form id="attendance-{{ $application->id }}" method="POST"
                                        action="{{ route('admin.scholarship.attendance.update', ['application' => $application->id, ...$filters]) }}"
                                        x-data="{ attendance: @js($application->attendance_status) }"
                                        x-on:submit="if (attendance === 'absent' && @js($application->attendance_status !== 'absent')) {
                                            $event.preventDefault();
                                            $dispatch('ala-action-confirmation', @js([
                                                'formId' => 'attendance-'.$application->id,
                                                'title' => __('scholarship.attendance_absent'),
                                                'content' => __('scholarship.attendance_absent_confirm', ['number' => $application->application_number]),
                                                'actionLabel' => __('dictt.save'),
                                                'icon' => 'fa-user-xmark',
                                                'tone' => 'danger',
                                            ]));
                                        }">
                                        @csrf @method('PATCH')
                                        <label for="attendance-status-{{ $application->id }}" class="visually-hidden">{{ __('scholarship.attendance_for', ['number' => $application->application_number]) }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <select id="attendance-status-{{ $application->id }}" name="attendance_status" x-model="attendance" class="form-select form-select-sm w-auto" required>
                                                @foreach (['unmarked', 'attended', 'absent'] as $status)<option value="{{ $status }}" @selected($application->attendance_status === $status)>{{ __('scholarship.attendance_'.$status) }}</option>@endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('dictt.save') }}</button>
                                        </div>
                                        @if ($application->attendance_status === 'absent' && $application->result_published)
                                            <p class="small text-muted mt-2 mb-0">{{ __('scholarship.attendance_published_help') }}</p>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ __('scholarship.applications_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($applications->hasPages())<div class="mt-3">{{ $applications->links() }}</div>@endif
        </div>
    </div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
