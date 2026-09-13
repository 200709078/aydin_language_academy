<x-app-layout>
    <x-slot name="header">{{ __('scholarship.application_detail') }}</x-slot>
    <div class="card"><div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
            <div><a href="{{ route('admin.scholarship.applications.index') }}" class="btn btn-sm btn-secondary mb-2">{{ __('dictt.back_short') }}</a><h5 class="card-title">{{ __('scholarship.application_detail') }}</h5><div class="text-break">{{ $application->application_number }}</div></div>
            @include('admin.scholarship.applications._actions')
        </div>
        <dl class="row">
            @foreach ([
                'student_name' => $application->student_name_snapshot,
                'current_school' => $application->school_name_snapshot,
                'current_level' => $application->student_level_name_snapshot,
                'account_name' => $application->user?->name ?? __('scholarship.account_deleted'),
                'account_email' => $application->user?->email ?? '—',
                'account_phone' => $application->user?->phone ?? '—',
                'period' => $application->period->title,
                'approval_status' => __('scholarship.approval_'.$application->status),
                'application_publication' => __('scholarship.'.($application->application_published ? 'published' : 'unpublished')),
                'application_contact' => __('scholarship.contact_'.$application->application_contact_status),
                'attendance' => __('scholarship.attendance_'.$application->attendance_status),
                'score' => $application->score ?? __('scholarship.not_entered'),
                'scholarship_percentage' => $application->scholarship_percentage === null ? __('scholarship.not_entered') : '%'.$application->scholarship_percentage,
                'result_publication' => __('scholarship.'.($application->result_published ? 'published' : 'unpublished')),
                'result_contact' => __('scholarship.contact_'.$application->result_contact_status),
            ] as $label => $value)
                <dt class="col-sm-4 mb-1">{{ __('scholarship.'.$label) }}</dt><dd class="col-sm-8 text-break">{{ $value }}</dd>
            @endforeach
            <dt class="col-sm-4">{{ __('scholarship.sessions') }}</dt><dd class="col-sm-8 text-break">@include('admin.scholarship.applications._session-label', ['session' => $application->session])</dd>
            <dt class="col-sm-4">{{ __('scholarship.session_state') }}</dt><dd class="col-sm-8">{{ __('scholarship.session_'.($application->session->archived_at ? 'archived' : ($application->session->is_active ? 'active' : 'suspended'))) }}</dd>
        </dl>
        <p class="text-muted small mb-0">{{ __('scholarship.approval_help') }}</p>
    </div></div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
