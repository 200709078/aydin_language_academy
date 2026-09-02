<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.exercise_attempt_history_title') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/themes.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-5 wow fadeIn" data-wow-delay="0.1s">
                <ol class="breadcrumb text-uppercase themes-crumb mb-0 py-3 px-4">
                    <li class="breadcrumb-item text-primary">{{ __('dictt.documents') }}</li>
                    <li class="breadcrumb-item text-primary">{{ $theme->levels->name }}</li>
                    <li class="breadcrumb-item text-primary">
                        <a class="text-primary" href="{{ route('frontend.themes.list', [$theme->levels->slug, $theme->sub_levels->slug]) }}">
                            {{ $theme->sub_levels->name }}
                        </a>
                    </li>
                    <li class="breadcrumb-item text-primary">
                        <a class="text-primary" href="{{ route('frontend.themes.detail', ['theme_id' => $theme]) }}">{{ Str::limit($theme->name, 25) }}</a>
                    </li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">{{ __('dictt.exercise_attempt_history') }}</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-lg-11 wow fadeIn" data-wow-delay="0.15s">
                    <div class="text-center mx-auto mb-5" style="max-width: 720px;">
                        <h1 class="mb-3">{{ __('dictt.exercise_attempt_history_title') }}</h1>
                        <p class="mb-4">{{ __('dictt.exercise_attempt_history_note') }}</p>
                        <a href="{{ route('frontend.themes.detail', ['theme_id' => $theme]) }}#exercise-{{ $exercise->id }}"
                            class="btn btn-outline-primary">
                            <i class="fa fa-arrow-left me-2" aria-hidden="true"></i>{{ __('dictt.back_short') }}
                        </a>
                    </div>

                    <section class="theme-exercise-review-summary rounded p-3 p-md-4">
                        <h2 class="h5 text-dark mb-4">{{ Str::limit($exercise->qtext ?: $exercise->title, 120) }}</h2>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('dictt.exercise_attempt_started_at') }}</th>
                                        <th scope="col">{{ __('dictt.exercise_attempt_completed_at') }}</th>
                                        <th scope="col">{{ __('dictt.exercise_attempt_correct') }}</th>
                                        <th scope="col">{{ __('dictt.exercise_attempt_wrong') }}</th>
                                        <th scope="col">{{ __('dictt.exercise_attempt_blank') }}</th>
                                        <th scope="col">{{ __('dictt.status') }}</th>
                                        <th scope="col">{{ __('dictt.operations') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attempts as $exerciseAttempt)
                                        @php
                                            $summary = $exerciseAttempt->summaryFor($exercise->questions);
                                        @endphp
                                        <tr>
                                            <td class="text-nowrap">{{ $exerciseAttempt->started_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td class="text-nowrap">{{ $exerciseAttempt->completed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>{{ $summary['correct'] }}</td>
                                            <td>{{ $summary['wrong'] }}</td>
                                            <td>{{ $summary['blank'] }}</td>
                                            <td>
                                                @if ($exerciseAttempt->status === 'in_progress')
                                                    <span class="badge bg-primary text-white">{{ __('dictt.exercise_attempt_status_in_progress') }}</span>
                                                @else
                                                    <span class="badge bg-success text-white">{{ __('dictt.exercise_attempt_status_completed') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($exerciseAttempt->status === 'in_progress')
                                                    <a href="{{ route('frontend.themes.detail', ['theme_id' => $theme]) }}#exercise-{{ $exercise->id }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('dictt.exercise_attempt_continue') }}">
                                                        <i class="fa fa-play" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.exercise_attempt_continue') }}</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('frontend.exercise-attempts.show', [
                                                        'exercise' => $exercise,
                                                        'exerciseAttempt' => $exerciseAttempt,
                                                    ]) }}" class="btn btn-sm btn-primary" title="{{ __('dictt.exercise_attempt_review') }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.exercise_attempt_review') }}</span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">{{ __('dictt.exercise_attempt_empty') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($attempts->hasPages())
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $attempts->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.footer')

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/easing/easing.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
