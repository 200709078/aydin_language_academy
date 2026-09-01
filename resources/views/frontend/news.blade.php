<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.news') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="{{ __('dictt.news') }}" name="keywords">
    <meta content="{{ __('dictt.news') }} | {{ __('dictt.ala') }}" name="description">

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
    <link href="{{ asset('frontend/css/style.css') }}?v=20260830-news-5" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <main class="container-xxl py-5 ala-news-listing">
        <div class="container">
            <div class="news-listing__heading mb-5 wow fadeInUp" data-wow-delay="0.1s">
                <a class="news-listing__back btn btn-sm btn-outline-primary" href="{{ route('home') }}" title="{{ __('dictt.news_back_to_home') }}">
                    <i class="fa fa-arrow-left me-2" aria-hidden="true"></i>{{ __('dictt.news_back_to_home') }}
                </a>
                <h1 class="mb-0">{{ __('dictt.news') }}</h1>
            </div>

            <div class="row g-4">
                @forelse ($news as $newsItem)
                    <div class="col-xl-4 col-md-6 d-flex wow fadeInUp" data-wow-delay="0.1s">
                        @include('frontend.partials.news-card', ['news' => $newsItem])
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">{{ __('dictt.news_empty') }}</div>
                @endforelse
            </div>

            @if ($news->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $news->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </main>

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
    <script src="{{ asset('frontend/js/main.js') }}?v=20260830-news-2"></script>
</body>

</html>
