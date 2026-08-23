<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.themes') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <base href="{{ asset('ALA-FRONTEND/TEMPLATE') }}/">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Themes Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.1s">
                <h1>{{ strtoupper($level->name) }} <i class="bi bi-arrow-right"></i>
                    {{ strtoupper($subLevel->name) }} <i class="bi bi-arrow-right"></i> {{ strtoupper(__('dictt.themes')) }}
                </h1>
            </div>

            @if ($themes->isNotEmpty())
                <div class="row g-4">
                    @foreach ($themes as $theme)
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $loop->iteration * 0.2 <= 0.5 ? $loop->iteration * 0.2 : 0.5 }}s">
                            <div class="service-item bg-light rounded h-100 p-5">
                                <h4 class="mb-3">{{ $theme->name }}</h4>
                                <p class="mb-0 text-center">
                                    @if ($theme->image)
                                        <img class="img-fluid rounded align-self-center" src="{{ asset('photos/' . $theme->image) }}"
                                            style="height:120px" alt="{{ $theme->name }}" />
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center bg-white rounded"
                                            style="width:120px; height:120px;">
                                            <i class="fa fa-book text-primary fs-2"></i>
                                        </span>
                                    @endif
                                </p>
                                <button type="button" class="btn" disabled aria-disabled="true">
                                    <i class="fa fa-plus text-primary me-3"></i>{{ __('dictt.details') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center mx-auto">
                    <h4>{{ __('dictt.themenotfound') }}</h4>
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
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
