<x-frontend-profile-layout :header="__('scholarship.member_applications_title')">
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h2 mb-2">{{ __('scholarship.member_applications_title') }}</h1>
            <p class="text-muted mb-3">{{ __('scholarship.member_applications_help') }}</p>
            <a href="{{ route('frontend.scholarship.exams.index') }}" class="btn btn-outline-primary">{{ __('scholarship.member_exams_title') }}</a>
        </div>

        @if (session('scholarship_notice'))
            <div class="alert alert-info" role="status">{{ session('scholarship_notice') }}</div>
        @endif

        @forelse ($applications as $application)
            <article class="bg-light rounded p-3 p-md-4 mb-4" aria-labelledby="application-{{ $application['id'] }}">
                <h2 class="h4 mb-3 text-break" id="application-{{ $application['id'] }}">{{ $application['period']['title'] }}</h2>
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <dl class="mb-0">
                            <dt>{{ __('scholarship.application_number') }}</dt>
                            <dd class="text-break">{{ $application['application_number'] }}</dd>
                            <dt>{{ __('scholarship.student_name') }}</dt>
                            <dd class="text-break">{{ $application['student']['name'] }}</dd>
                            <dt>{{ __('scholarship.exam_title') }}</dt>
                            <dd class="text-break">{{ $application['session']['exam_title'] }}</dd>
                        </dl>
                    </div>
                    <div class="col-12 col-lg-6">
                        <dl class="mb-0">
                            <dt>{{ __('scholarship.branch') }}</dt>
                            <dd class="text-break">{{ $application['session']['branch'] }}</dd>
                            <dt>{{ __('scholarship.exam_group') }}</dt>
                            <dd class="text-break">{{ $application['session']['exam_group'] }}</dd>
                            <dt>{{ __('scholarship.session_date_time') }}</dt>
                            <dd>{{ \Carbon\CarbonImmutable::parse($application['session']['date'])->format('d.m.Y') }} · {{ substr($application['session']['starts_at'], 0, 5) }}–{{ substr($application['session']['ends_at'], 0, 5) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="border-top pt-3 mt-2">
                    @include('frontend.scholarship.applications.partials.status')
                </div>
                <a href="{{ route('frontend.scholarship.applications.show', $application['id']) }}" class="btn btn-outline-primary mt-3" aria-describedby="application-{{ $application['id'] }}">{{ __('scholarship.member_view_application') }}</a>
            </article>
        @empty
            <div class="bg-light rounded p-4 text-center text-muted">{{ __('scholarship.member_applications_empty') }}</div>
        @endforelse

        @if ($applications->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $applications->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</x-frontend-profile-layout>
