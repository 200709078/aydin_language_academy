<div class="d-flex flex-wrap align-items-start gap-2">
    <a href="{{ route('admin.scholarship.applications.edit', $application) }}" class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}"><i class="fa fa-pen" aria-hidden="true"></i><span class="visually-hidden">{{ __('dictt.edit') }}</span></a>
    @if ($application->status === 'pending')
        <form id="application-approve-{{ $application->id }}" method="POST" action="{{ route('admin.scholarship.applications.approve', $application) }}">
            @csrf @method('PATCH')
            <button type="button" class="btn btn-sm btn-outline-success" data-action-confirmation
                data-confirm-form="application-approve-{{ $application->id }}" data-confirm-title="{{ __('scholarship.application_approve') }}"
                data-confirm-content="{{ __('scholarship.application_approve_confirm', ['number' => $application->application_number]) }}"
                data-confirm-action="{{ __('scholarship.application_approve') }}" data-confirm-icon="fa-check" data-confirm-tone="success"
                title="{{ __('scholarship.application_approve') }}"><i class="fa fa-check" aria-hidden="true"></i><span class="visually-hidden">{{ __('scholarship.application_approve') }}</span></button>
        </form>
    @endif
    <form id="application-delete-{{ $application->id }}" method="POST" action="{{ route('admin.scholarship.applications.destroy', $application) }}">
        @csrf @method('DELETE')
        <button type="button" class="btn btn-sm btn-outline-danger admin-danger-action" data-action-confirmation
            data-confirm-form="application-delete-{{ $application->id }}" data-confirm-title="{{ __('scholarship.application_delete') }}"
            data-confirm-content="{{ __('scholarship.application_delete_confirm', ['number' => $application->application_number]) }}"
            data-confirm-action="{{ __('scholarship.application_delete') }}" data-confirm-icon="fa-trash" data-confirm-tone="danger"
            title="{{ __('scholarship.application_delete') }}"><i class="fa fa-trash" aria-hidden="true"></i><span class="visually-hidden">{{ __('scholarship.application_delete') }}</span></button>
    </form>
</div>
