<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.placement_test') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-5 text-center">
                        @if ($placementTest->status === 'approved')
                            <i class="fa fa-check-circle text-success display-5 mb-4"></i>
                            <h1 class="mb-3">{{ __('dictt.placement_test_result_approved_title') }}</h1>
                            <p class="mb-4">{{ __('dictt.placement_test_result_approved_text') }}</p>
                            @if ($placementTest->resultLevel)
                                <div class="h4 text-primary mb-4">{{ __('dictt.placement_test_final_level', ['level' => $placementTest->resultLevel->code]) }}</div>
                            @endif
                        @else
                            <i class="fa fa-clock text-primary display-5 mb-4"></i>
                            <h1 class="mb-3">{{ __('dictt.placement_test_completed_title') }}</h1>
                            <p class="mb-4">{{ __('dictt.placement_test_completed_text') }}</p>
                        @endif
                    </div>

                    <div class="bg-light rounded p-4 mt-4">
                        <h4 class="mb-3">{{ __('dictt.placement_test_exam_info') }}</h4>
                        @include('frontend.placement-test.attempt-summary', ['placementTest' => $placementTest])
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-4">
                        @if ($placementTest->status === 'approved')
                            <a href="{{ route('frontend.placement-test.attempts.show', $placementTest) }}" class="btn btn-outline-primary py-3 px-4">
                                <i class="fa fa-eye me-2" aria-hidden="true"></i>{{ __('dictt.placement_test_review') }}
                            </a>
                            <form method="POST" action="{{ route('frontend.placement-test.start') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary py-3 px-4">
                                    <i class="fa fa-redo me-2" aria-hidden="true"></i>{{ __('dictt.placement_test_start_new') }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('frontend.placement-test.attempts') }}" class="btn btn-outline-secondary py-3 px-4">
                            <i class="fa fa-history me-2" aria-hidden="true"></i>{{ __('dictt.placement_test_my_attempts') }}
                        </a>
                        <a href="{{ route('frontend.placement-test') }}" class="btn btn-outline-primary py-3 px-4">
                            {{ __('dictt.placement_test_back_to_page') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.footer')

    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
