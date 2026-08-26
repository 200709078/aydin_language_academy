<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.placement_test_my_attempts') }} | {{ __('dictt.ala') }}</title>
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
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 wow fadeIn" data-wow-delay="0.1s">
                    <div class="text-center mx-auto mb-5" style="max-width: 680px;">
                        <h1 class="mb-3">{{ __('dictt.placement_test_my_attempts') }}</h1>
                        <p class="mb-4">{{ __('dictt.placement_test_my_attempts_note') }}</p>
                        <a href="{{ route('frontend.placement-test') }}" class="btn btn-outline-primary">
                            <i class="fa fa-clipboard-check me-2" aria-hidden="true"></i>{{ __('dictt.placement_test') }}
                        </a>
                    </div>

                    <div class="bg-light rounded p-3 p-md-4">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ __('dictt.placement_test_history_date') }}</th>
                                        <th scope="col">{{ __('dictt.placement_test_result_level') }}</th>
                                        <th scope="col">{{ __('dictt.status') }}</th>
                                        <th scope="col">{{ __('dictt.operations') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attempts as $placementTest)
                                        <tr>
                                            <th scope="row">{{ $placementTest->id }}</th>
                                            <td class="text-nowrap">
                                                {{ ($placementTest->status === 'in_progress' ? $placementTest->started_at : $placementTest->submitted_at)?->format('d.m.Y H:i') ?? '—' }}
                                            </td>
                                            <td>
                                                {{ $placementTest->status === 'approved' ? ($placementTest->resultLevel?->code ?? '—') : '—' }}
                                            </td>
                                            <td>
                                                @if ($placementTest->status === 'in_progress')
                                                    <span class="badge text-bg-primary">{{ __('dictt.placement_test_status_in_progress') }}</span>
                                                @elseif ($placementTest->status === 'approved')
                                                    <span class="badge text-bg-success">{{ __('dictt.status_approved') }}</span>
                                                @else
                                                    <span class="badge text-bg-warning">{{ __('dictt.status_pending') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($placementTest->status === 'in_progress')
                                                    <a href="{{ route('frontend.placement-test.exam', $placementTest) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('dictt.placement_test_continue') }}">
                                                        <i class="fa fa-play" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.placement_test_continue') }}</span>
                                                    </a>
                                                @elseif ($placementTest->status === 'approved')
                                                    <a href="{{ route('frontend.placement-test.attempts.show', $placementTest) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('dictt.placement_test_review') }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.placement_test_review') }}</span>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-secondary" disabled
                                                        aria-disabled="true" title="{{ __('dictt.placement_test_my_attempts_pending_note') }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.placement_test_my_attempts_pending_note') }}</span>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">{{ __('dictt.placement_test_my_attempts_empty') }}</td>
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
                    </div>
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
    <script src="{{ asset('frontend/vendor/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
