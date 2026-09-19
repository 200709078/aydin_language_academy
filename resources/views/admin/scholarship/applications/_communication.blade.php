<div class="border rounded p-3 mb-3">
    <h6>{{ __('scholarship.communication') }}</h6>
    @include('admin.scholarship.applications._contact-switches')
    <p class="small text-muted">{{ __('scholarship.contact_help') }}</p>
    <div class="d-flex flex-wrap gap-2">
        @foreach (['application', 'result'] as $sendPhase)
            <form method="POST" action="{{ route('admin.scholarship.applications.notification.preview') }}">
                @csrf
                <input type="hidden" name="scope" value="selected"><input type="hidden" name="ids[]" value="{{ $application->id }}">
                <input type="hidden" name="phase" value="{{ $sendPhase }}"><input type="hidden" name="channel" value="email">
                <button type="submit" class="btn btn-sm btn-outline-primary" @disabled($application->getAttribute($sendPhase.'_contact_status') === 'reached')>{{ __('scholarship.delivery_email_'.$sendPhase) }}</button>
            </form>
        @endforeach
    </div>
    <p class="small text-muted mt-2">{{ __('scholarship.delivery_whatsapp_pending') }}</p>
    <h6 class="mt-3">{{ __('scholarship.delivery_records') }}</h6>
    <p class="small text-muted">{{ __('scholarship.delivery_status_help') }}</p>
    <div class="table-responsive position-relative">
        <table class="table table-striped table-sm align-middle">
            <thead><tr><th>{{ __('scholarship.communication') }}</th><th>{{ __('scholarship.delivery_recipient') }}</th><th>{{ __('scholarship.definition_status') }}</th><th>{{ __('scholarship.delivery_time') }}</th></tr></thead>
            <tbody>
                @forelse ($deliveries as $delivery)
                    <tr>
                        <td>{{ __('scholarship.'.$delivery->phase.'_contact') }}<div class="small">{{ __('scholarship.channel_'.$delivery->channel) }}</div></td>
                        <td class="text-break">{{ $delivery->recipient }}</td>
                        <td>{{ __('scholarship.delivery_status_'.$delivery->status) }}
                            @if ($delivery->failure_reason)<div class="small text-muted">{{ __('scholarship.'.$delivery->failure_reason) }}</div>@endif
                            @if ($delivery->status === 'queued' && $delivery->attempts > 0)<div class="small text-muted">{{ __('scholarship.delivery_processing') }}</div>@endif
                        </td>
                        <td>{{ ($delivery->sent_at ?? $delivery->failed_at ?? $delivery->queued_at)?->format('d.m.Y H:i') ?? '—' }}</td>
                    </tr>
                @empty<tr><td colspan="4" class="text-muted">{{ __('scholarship.delivery_empty') }}</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if ($deliveries->hasPages()){{ $deliveries->links() }}@endif
</div>
