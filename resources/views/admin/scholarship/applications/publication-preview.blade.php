<x-app-layout>
    <x-slot name="header">{{ __('scholarship.publication_preview') }}</x-slot>
    <div class="card"><div class="card-body">
        <h5 class="card-title">{{ __('scholarship.publication_preview') }}</h5>
        <p>{{ __('scholarship.'.($scope === 'selected' ? 'scope_selected' : 'scope_all_filtered')) }}: <strong>{{ $count }}</strong></p>
        <p><strong>{{ __('scholarship.'.$phase.'_publication') }} — {{ __('scholarship.'.($published ? 'publication_enable' : 'publication_disable')) }}</strong></p>
        <p class="text-muted">{{ __('scholarship.publication_preview_help', ['count' => $count, 'eligible' => $eligibleCount]) }}</p>
        <p class="small">{{ __('scholarship.publication_help') }}</p>
        <div class="table-responsive position-relative mb-3">
            <table class="table table-striped table-sm align-middle">
                <thead><tr><th>{{ __('scholarship.application_number') }}</th><th>{{ __('scholarship.student_name') }}</th><th>{{ __('scholarship.period') }}</th><th>{{ __('scholarship.approval_status') }}</th><th>{{ __('scholarship.score') }}</th><th>{{ __('scholarship.scholarship_percentage') }}</th><th>{{ __('scholarship.publication_action') }}</th></tr></thead>
                <tbody>@foreach ($applications as $application)<tr>
                    <td>{{ $application->application_number }}</td><td>{{ $application->student_name_snapshot }}</td><td>{{ $application->period->title }}</td>
                    <td>{{ __('scholarship.approval_'.$application->status) }}</td><td>{{ $application->score ?? __('scholarship.not_entered') }}</td>
                    <td>{{ $application->scholarship_percentage === null ? __('scholarship.award_unset') : ($application->scholarship_percentage === 0 ? __('scholarship.award_none') : '%'.$application->scholarship_percentage) }}</td>
                    <td>{{ $phase === 'result' && $published && $application->scholarship_percentage === null ? __('scholarship.publication_incomplete') : __('scholarship.publication_ready') }}</td>
                </tr>@endforeach</tbody>
            </table>
        </div>
        @if ($count > $applications->count())<p class="small text-muted">{{ __('scholarship.preview_first_records', ['count' => $applications->count(), 'total' => $count]) }}</p>@endif
        <form id="confirm-bulk-publication" method="POST" action="{{ route('admin.scholarship.applications.publication.bulk') }}" class="d-flex flex-wrap gap-2">
            @csrf <input type="hidden" name="token" value="{{ $token }}">
            <a href="{{ route('admin.scholarship.applications.index', $filters) }}" class="btn btn-sm btn-secondary">{{ __('dictt.back_short') }}</a>
            <button type="button" class="btn btn-sm btn-primary" @disabled($eligibleCount === 0) data-action-confirmation data-confirm-form="confirm-bulk-publication"
                data-confirm-title="{{ __('scholarship.publication_management') }}"
                data-confirm-content="{{ __('scholarship.publication_confirm', ['count' => $count, 'phase' => __('scholarship.'.$phase.'_publication'), 'state' => __('scholarship.'.($published ? 'publication_enable' : 'publication_disable'))]) }}"
                data-confirm-action="{{ __('scholarship.publication_apply') }}" data-confirm-icon="fa-eye" data-confirm-tone="success">{{ __('scholarship.publication_apply') }}</button>
        </form>
    </div></div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
