<x-frontend-profile-layout :header="__('scholarship.application_detail')">
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h2 mb-2">{{ __('scholarship.application_detail') }}</h1>
            <p class="text-break mb-3"><strong>{{ __('scholarship.application_number') }}:</strong> {{ $application['application_number'] }}</p>
            <a href="{{ route('frontend.scholarship.applications.index') }}" class="btn btn-outline-primary">{{ __('scholarship.member_applications_title') }}</a>
        </div>

        <section class="bg-light rounded p-3 p-md-4 mb-4" aria-labelledby="application-period">
            <h2 class="h4 mb-3 text-break" id="application-period">{{ $application['period']['title'] }}</h2>
            @include('frontend.scholarship.applications.partials.status')
            @if ($application['application']['arrival_at'])
                <p class="mt-3 mb-0">
                    <strong>{{ __('scholarship.member_arrival_deadline') }}:</strong>
                    {{ \Carbon\CarbonImmutable::parse($application['application']['arrival_at'])->format('d.m.Y H:i') }}
                </p>
            @endif
        </section>

        @if ($application['restriction'])
            <div class="alert {{ $application['restriction'] === 'suspended' ? 'alert-warning' : 'alert-info' }}" role="status">
                <p class="mb-2">{{ __('scholarship.member_restriction_'.$application['restriction']) }}</p>
                <a href="{{ route('frontend.contact') }}" class="alert-link">{{ __('scholarship.member_contact_admin') }}</a>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <section class="bg-light rounded h-100 p-3 p-md-4" aria-labelledby="student-details">
                    <h2 class="h4 mb-3" id="student-details">{{ __('scholarship.member_student_details') }}</h2>
                    <dl class="mb-0">
                        <dt>{{ __('scholarship.student_name') }}</dt>
                        <dd class="text-break">{{ $application['student']['name'] }}</dd>
                        <dt>{{ __('scholarship.current_school') }}</dt>
                        <dd class="text-break">{{ $application['student']['school'] }}</dd>
                        <dt>{{ __('scholarship.current_level') }}</dt>
                        <dd class="text-break mb-0">{{ $application['student']['level'] }}</dd>
                    </dl>
                </section>
            </div>
            <div class="col-12 col-lg-6">
                <section class="bg-light rounded h-100 p-3 p-md-4" aria-labelledby="exam-details">
                    <h2 class="h4 mb-3" id="exam-details">{{ __('scholarship.member_exam_selection') }}</h2>
                    <dl class="mb-0">
                        <dt>{{ __('scholarship.branch') }}</dt>
                        <dd class="text-break">{{ $application['session']['branch'] }}</dd>
                        @if ($application['session']['address'])
                            <dt>{{ __('scholarship.definition_address') }}</dt>
                            <dd class="text-break" style="white-space: pre-line;">{{ $application['session']['address'] }}</dd>
                        @endif
                        <dt>{{ __('scholarship.exam_group') }}</dt>
                        <dd class="text-break">{{ $application['session']['exam_group'] }}</dd>
                        <dt>{{ __('scholarship.exam_title') }}</dt>
                        <dd class="text-break">{{ $application['session']['exam_title'] }}</dd>
                        <dt>{{ __('scholarship.session_date_time') }}</dt>
                        <dd class="mb-0">{{ \Carbon\CarbonImmutable::parse($application['session']['date'])->format('d.m.Y') }} · {{ substr($application['session']['starts_at'], 0, 5) }}–{{ substr($application['session']['ends_at'], 0, 5) }}</dd>
                    </dl>
                </section>
            </div>
        </div>
    </div>
</x-frontend-profile-layout>
