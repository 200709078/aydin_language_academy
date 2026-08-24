<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.themes') }} | {{ __('dictt.ala') }}</title>
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
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/themes.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Themes Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <nav aria-label="breadcrumb animated slideInDown" class="mb-5 wow fadeIn" data-wow-delay="0.1s">
                <ol class="breadcrumb text-uppercase themes-crumb mb-0 py-3 px-4">
                    <li class="breadcrumb-item">{{ $level->name }}</li>
                    <li class="breadcrumb-item">{{ $subLevel->name }}</li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">{{ __('dictt.themes') }}</li>
                </ol>
            </nav>

            @if ($themes->isNotEmpty())
                <div class="row g-4">
                    @foreach ($themes as $theme)
                        <div class="col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-delay="{{ $loop->iteration * 0.2 <= 0.5 ? $loop->iteration * 0.2 : 0.5 }}s">
                            <div class="service-item bg-light rounded h-100 theme-card d-flex flex-column">
                                <div class="theme-thumb mb-3">
                                    @if ($theme->image)
                                        <img src="{{ asset('photos/' . $theme->image) }}" alt="{{ $theme->name }}">
                                    @else
                                        <i class="fa fa-book text-primary fs-2"></i>
                                    @endif
                                </div>
                                <h5 class="theme-title">{{ $theme->name }}</h5>
                                <a class="btn btn-primary btn-sm theme-btn mt-auto align-self-start"
                                    href="{{ route('frontend.themes.detail', $theme->id) }}">
                                    {{ __('dictt.details') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 wow fadeIn" data-wow-delay="0.2s">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="fa fa-search text-primary fs-3"></i>
                    </div>
                    <h5 class="text-muted mb-0">{{ __('dictt.themenotfound') }}</h5>
                </div>
            @endif
        </div>
    </div>
    <!-- Themes End -->

    @include('frontend.partials.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/easing/easing.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
