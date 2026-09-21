<x-frontend-profile-layout :header="__('scholarship.member_exams_title')">
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h2 mb-2">{{ __('scholarship.member_exams_title') }}</h1>
            <p class="text-muted mb-0">{{ __('scholarship.member_exams_help') }}</p>
            <a href="{{ route('frontend.scholarship.applications.index') }}" class="btn btn-outline-primary mt-3">{{ __('scholarship.member_applications_title') }}</a>
        </div>

        @if (session('scholarship_application_created.user_id') === auth()->id())
            <div class="alert alert-success text-break" role="status">
                {{ __('scholarship.member_application_created', ['number' => session('scholarship_application_created.number')]) }}
            </div>
        @endif

        @if (session('scholarship_notice'))
            <div class="alert alert-info" role="status">{{ session('scholarship_notice') }}</div>
        @endif

        @forelse ($periods as $period)
            <section class="bg-light rounded p-3 p-md-4 mb-4" aria-labelledby="period-{{ $period['id'] }}">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="h4 mb-0 text-break" id="period-{{ $period['id'] }}">{{ $period['title'] }}</h2>
                    <span class="badge {{ $period['state'] === 'open' ? 'bg-success' : 'bg-secondary' }} text-white text-wrap">
                        {{ __('scholarship.member_period_'.$period['state']) }}
                    </span>
                </div>
                @if ($period['description'])
                    <p class="text-break" style="white-space: pre-line;">{{ $period['description'] }}</p>
                @endif
                <p class="mb-3">
                    <strong>{{ __('scholarship.member_application_dates') }}:</strong>
                    {{ $period['applications_open_at'] }} – {{ $period['applications_close_at'] }}
                </p>
                @if ($period['has_application'])
                    <div class="alert alert-info">{{ __('scholarship.member_existing_application') }}</div>
                @endif

                <div class="row g-3">
                    @foreach ($period['sessions'] as $session)
                        @php
                            $badgeClass = match ($session['state']) {
                                'available' => 'bg-success text-white',
                                'suspended' => 'bg-warning text-dark',
                                default => 'bg-secondary text-white',
                            };
                            $stateLabel = match ($session['state']) {
                                'suspended' => __('scholarship.session_suspended'),
                                'full' => __('scholarship.session_full'),
                                default => __('scholarship.member_session_'.$session['state']),
                            };
                        @endphp
                        <div class="col-12 col-lg-6">
                            <article class="bg-white border rounded h-100 p-3" aria-labelledby="session-{{ $session['id'] }}">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <h3 class="h5 mb-0 text-break" id="session-{{ $session['id'] }}">{{ $session['exam_title'] }}</h3>
                                    <span class="badge {{ $badgeClass }} text-wrap">{{ $stateLabel }}</span>
                                </div>
                                <dl class="mb-3">
                                    <dt>{{ __('scholarship.branch') }}</dt>
                                    <dd class="text-break">{{ $session['branch'] }}</dd>
                                    <dt>{{ __('scholarship.exam_group') }}</dt>
                                    <dd class="text-break">{{ $session['exam_group'] }}</dd>
                                    <dt>{{ __('scholarship.session_date_time') }}</dt>
                                    <dd>{{ $session['exam_date'] }} · {{ $session['starts_at'] }}–{{ $session['ends_at'] }}</dd>
                                    <dt>{{ __('scholarship.capacity') }}</dt>
                                    <dd>{{ $session['capacity'] }}</dd>
                                </dl>
                                <p class="mb-0">{{ __('scholarship.member_remaining_seats', ['count' => $session['remaining']]) }}</p>
                                @if ($session['state'] === 'available')
                                    <a href="{{ route('frontend.scholarship.applications.create', ['period' => $period['id'], 'session' => $session['id']]) }}" class="btn btn-outline-primary mt-3">
                                        {{ __('scholarship.member_open_form') }}
                                    </a>
                                @endif
                                @if ($session['state'] === 'suspended')
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <p class="mb-2">{{ __('scholarship.member_suspended_help') }}</p>
                                        <a href="{{ route('frontend.contact') }}" class="alert-link">{{ __('scholarship.member_contact_admin') }}</a>
                                    </div>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="bg-light rounded p-4 text-center text-muted">{{ __('scholarship.member_exams_empty') }}</div>
        @endforelse

        @if ($periods->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $periods->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</x-frontend-profile-layout>
