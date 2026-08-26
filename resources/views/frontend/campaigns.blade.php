<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.campaigns') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">

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
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Campaigns Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-7 wow fadeIn" data-wow-delay="0.1s">
                    <h1 class="mb-0">{{ __('dictt.campaigns_page_heading') }}</h1>
                </div>
                <div class="col-lg-5 wow fadeIn" data-wow-delay="0.5s">
                    <div class="text-center">
                        <img class="img-fluid" src="{{ asset('frontend/images/campaigns/campaign-1.png') }}" alt="{{ __('dictt.campaigns_image_alt') }}" style="max-height: 360px;">
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <img src="{{ asset('frontend/images/campaigns/teachings.png') }}" alt="" style="width: 42px; height: 42px; object-fit: contain;">
                        </div>
                        <h4 class="mb-3">{{ __('dictt.campaigns_summer_winter_title') }}</h4>
                        <p class="mb-0">{{ __('dictt.campaigns_summer_winter_text') }}</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <img src="{{ asset('frontend/images/campaigns/exam.png') }}" alt="" style="width: 42px; height: 42px; object-fit: contain;">
                        </div>
                        <h4 class="mb-3">{{ __('dictt.campaigns_placement_discount_title') }}</h4>
                        <p class="mb-0">{{ __('dictt.campaigns_placement_discount_text') }}</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <img src="{{ asset('frontend/images/campaigns/linguistics.png') }}" alt="" style="width: 42px; height: 42px; object-fit: contain;">
                        </div>
                        <h4 class="mb-3">{{ __('dictt.campaigns_scholarship_title') }}</h4>
                        <p class="mb-0">{{ __('dictt.campaigns_scholarship_text') }}</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item bg-light rounded h-100 p-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-4" style="width: 65px; height: 65px;">
                            <img src="{{ asset('frontend/images/campaigns/communicate.png') }}" alt="" style="width: 42px; height: 42px; object-fit: contain;">
                        </div>
                        <h4 class="mb-3">{{ __('dictt.campaigns_instagram_title') }}</h4>
                        <p class="mb-0">{{ __('dictt.campaigns_instagram_text') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Campaigns End -->

    @include('frontend.partials.footer')


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
