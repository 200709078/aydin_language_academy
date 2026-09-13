<x-app-layout>
    <x-slot name="header">{{ __('scholarship.approval_preview') }}</x-slot>
    <div class="card"><div class="card-body">
        <h5 class="card-title">{{ __('scholarship.approval_preview') }}</h5>
        <p>{{ __('scholarship.scope_'.$scope) }}: <strong>{{ $count }}</strong></p>
        <p class="text-muted">{{ __('scholarship.approval_preview_help', ['count' => $count]) }}</p>
        <p class="small">{{ __('scholarship.approval_help') }}</p>
        <div class="table-responsive position-relative mb-3">
            <table class="table table-striped table-sm align-middle">
                <thead><tr><th>{{ __('scholarship.application_number') }}</th><th>{{ __('scholarship.student_name') }}</th><th>{{ __('scholarship.period') }}</th><th>{{ __('scholarship.sessions') }}</th></tr></thead>
                <tbody>@foreach ($applications as $application)<tr><td>{{ $application->application_number }}</td><td>{{ $application->student_name_snapshot }}</td><td>{{ $application->period->title }}</td><td>@include('admin.scholarship.applications._session-label', ['session' => $application->session])</td></tr>@endforeach</tbody>
            </table>
        </div>
        @if ($count > $applications->count())<p class="small text-muted">{{ __('scholarship.preview_first_records', ['count' => $applications->count(), 'total' => $count]) }}</p>@endif
        <form id="confirm-bulk-approval" method="POST" action="{{ route('admin.scholarship.applications.approve.bulk') }}" class="d-flex flex-wrap gap-2">
            @csrf <input type="hidden" name="token" value="{{ $token }}">
            <a href="{{ route('admin.scholarship.applications.index', $filters) }}" class="btn btn-sm btn-secondary">{{ __('dictt.back_short') }}</a>
            <button type="button" class="btn btn-sm btn-success" data-action-confirmation data-confirm-form="confirm-bulk-approval"
                data-confirm-title="{{ __('scholarship.application_approve') }}" data-confirm-content="{{ __('scholarship.bulk_approve_confirm', ['count' => $count]) }}"
                data-confirm-action="{{ __('scholarship.application_approve') }}" data-confirm-icon="fa-check" data-confirm-tone="success">{{ __('scholarship.application_approve') }} ({{ $count }})</button>
        </form>
    </div></div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
