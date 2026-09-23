<p class="mb-2">
    <strong>{{ __('scholarship.member_application_status') }}:</strong>
    <span class="badge {{ $application['application']['state'] === 'approved' ? 'bg-success text-white' : 'bg-warning text-dark' }} text-wrap">
        {{ $application['application']['state'] === 'approved' ? __('scholarship.delivery_accepted') : __('scholarship.member_application_under_review') }}
    </span>
</p>
@if (! $application['application']['published'])
    <p class="text-muted mb-2">{{ __('scholarship.member_application_unpublished') }}</p>
@endif
@if ($application['application']['state'] === 'approved')
    <p class="mb-2">{{ __('scholarship.delivery_arrive_early') }}</p>
@endif
<p class="mb-0">
    <strong>{{ __('scholarship.member_result_status') }}:</strong>
    <span class="badge {{ $application['result']['published'] ? 'bg-success' : 'bg-secondary' }} text-white text-wrap">
        {{ __('scholarship.member_result_'.($application['result']['published'] ? 'published' : 'unpublished')) }}
    </span>
</p>
@if ($application['result']['published'])
    @include('frontend.scholarship.applications.partials.result', ['result' => $application['result']])
@endif
