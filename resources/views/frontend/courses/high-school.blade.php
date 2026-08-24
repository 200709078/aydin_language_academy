<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.high_school') }} | {{ __('dictt.ala') }}</title>
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

    <!-- High School Course Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/whoweare.png') }}" alt="{{ __('dictt.high_school') }}">
                    <p class="mt-4 mb-0 clearfix"><i class="fa fa-book-open text-primary fs-4 float-start me-3" aria-hidden="true"></i>{{ __('dictt.shared_course_image_text') }}</p>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">{{ __('dictt.high_school') }}</h1>
                    <p>{{ __('dictt.high_school_intro') }}</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>{{ __('dictt.high_school_section_1_title') }}</p>
                    <p class="mb-4">{{ __('dictt.high_school_section_1_text') }}</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>{{ __('dictt.high_school_section_2_title') }}</p>
                    <p class="mb-4">{{ __('dictt.high_school_section_2_text') }}</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>{{ __('dictt.high_school_section_3_title') }}</p>
                    <p class="mb-4">{{ __('dictt.high_school_section_3_text') }}</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>{{ __('dictt.high_school_section_4_title') }}</p>
                    <p class="mb-4">{{ __('dictt.high_school_section_4_text') }}</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>{{ __('dictt.high_school_section_5_title') }}</p>
                    <p class="mb-4">{{ __('dictt.high_school_section_5_text') }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- High School Course End -->

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
