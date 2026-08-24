<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.placement_test') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('frontend/vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Placement Test Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 wow fadeIn" data-wow-delay="0.1s">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('dictt.placement_test_close') }}"></button>
                        </div>
                    @endif

                    <div class="bg-light rounded p-5 text-center">
                        <h1 class="mb-4">{{ __('dictt.placement_test') }}</h1>
                        @auth
                            <p class="mb-0">{{ __('dictt.placement_test_welcome', ['name' => auth()->user()->name]) }}</p>
                        @else
                            <p class="mb-0">{!! __('dictt.placement_test_login_prompt', ['link' => '<a class="text-primary" href="' . route('frontend.login', ['return' => request()->route()?->getName()]) . '">' . __('dictt.login') . '</a>']) !!}</p>
                        @endauth
                    </div>

                    @auth
                        @if (($openAttempt ?? null)?->status === 'pending_approval')
                            <div class="bg-light rounded p-4 mt-4">
                                <h4 class="mb-3">{{ __('dictt.placement_test_exam_info') }}</h4>
                                @include('frontend.placement-test.attempt-summary', ['placementTest' => $openAttempt])
                            </div>
                        @else
                            <div class="bg-light rounded p-4 mt-4">
                                <h4 class="mb-3">{{ __('dictt.placement_test_rules_title') }}</h4>
                                <ul class="mb-0 ps-3">
                                    <li class="mb-2">{{ __('dictt.placement_test_rule_1') }}</li>
                                    <li class="mb-2">{{ __('dictt.placement_test_rule_2') }}</li>
                                    <li class="mb-2">{{ __('dictt.placement_test_rule_3') }}</li>
                                    <li class="mb-2">{{ __('dictt.placement_test_rule_4') }}</li>
                                    <li class="mb-0">{{ __('dictt.placement_test_rule_5') }}</li>
                                </ul>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            @if (($openAttempt ?? null)?->status === 'in_progress')
                                <p class="text-muted mb-3">{{ __('dictt.placement_test_in_progress_note') }}</p>
                                <form method="POST" action="{{ route('frontend.placement-test.start') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary py-3 px-5">{{ __('dictt.placement_test_continue') }}</button>
                                </form>
                            @elseif (($openAttempt ?? null)?->status === 'pending_approval')
                                <a href="{{ route('frontend.placement-test.completed', $openAttempt) }}" class="btn btn-primary py-3 px-5">
                                    {{ __('dictt.placement_test_view_status') }}
                                </a>
                            @else
                                <form method="POST" action="{{ route('frontend.placement-test.start') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary py-3 px-5">{{ __('dictt.placement_test_start') }}</button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    <!-- Placement Test End -->

    @include('frontend.partials.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
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

    <!-- Template Javascript -->
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
