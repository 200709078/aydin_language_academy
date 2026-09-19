<x-app-layout>
    <x-slot name="header">{{ __('scholarship.delivery_preview') }}</x-slot>
    <div class="card"><div class="card-body">
        <h5 class="card-title">{{ __('scholarship.delivery_preview') }}</h5>
        <p>{{ __('scholarship.'.($scope === 'selected' ? 'scope_selected' : 'scope_all_filtered')) }}: <strong>{{ $count }}</strong></p>
        <p><strong>{{ __('scholarship.'.$phase.'_contact') }} — {{ __('scholarship.channel_'.$channel) }}</strong></p>
        <p class="text-muted">{{ __('scholarship.delivery_preview_help') }}</p>
        @foreach ($rows as $row)
            <div class="border rounded p-3 mb-3">
                <h6>{{ $row['application']->application_number }} — {{ $row['application']->student_name_snapshot }}</h6>
                @if ($row['error'])<p class="text-warning mb-0">{{ $row['error'] }}</p>
                @else
                    <p class="text-break">{{ $row['message']['recipient'] }}<br>{{ $row['message']['subject'] }}</p>
                    <div class="text-break" style="white-space: pre-wrap">{{ $row['message']['body'] }}</div>
                @endif
            </div>
        @endforeach
        @if ($count > count($rows))<p class="small text-muted">{{ __('scholarship.preview_first_records', ['count' => count($rows), 'total' => $count]) }}</p>@endif
        <form id="confirm-scholarship-notifications" method="POST" action="{{ route('admin.scholarship.applications.notification.send') }}" class="d-flex flex-wrap gap-2">
            @csrf <input type="hidden" name="token" value="{{ $token }}">
            <a href="{{ route('admin.scholarship.applications.index', $filters) }}" class="btn btn-sm btn-secondary">{{ __('dictt.back_short') }}</a>
            <button type="button" class="btn btn-sm btn-primary" @disabled($channel === 'whatsapp') data-action-confirmation data-confirm-form="confirm-scholarship-notifications"
                data-confirm-title="{{ __('scholarship.delivery_send') }}" data-confirm-content="{{ __('scholarship.delivery_confirm', ['count' => $count]) }}"
                data-confirm-action="{{ __('scholarship.delivery_send') }}" data-confirm-icon="fa-envelope" data-confirm-tone="success">{{ __('scholarship.delivery_send') }}</button>
        </form>
    </div></div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
