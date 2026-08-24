<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.reviews') }} | {{ __('dictt.ala') }}</title>
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
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('frontend/css/style.css') }}?v=20260824" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Reviews Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1>{{ __('dictt.reviews') }}</h1>
            </div>

            <form method="GET" action="{{ route('frontend.reviews') }}"
                class="row justify-content-center g-2 mb-5 wow fadeInUp" data-wow-delay="0.15s">
                <div class="col-auto">
                    <select name="sube" class="form-select" onchange="this.form.submit()" aria-label="{{ __('dictt.branch') }}">
                        <option value="" @selected(empty($branch))>{{ __('dictt.filter_all') }}</option>
                        @foreach (\App\Models\Review::BRANCHES as $branchOption)
                            <option value="{{ $branchOption }}" @selected($branch === $branchOption)>{{ __("dictt.branch_{$branchOption}") }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">{{ __('dictt.apply_filter') }}</button>
                </div>
            </form>

            <div class="text-center mb-5 wow fadeInUp" data-wow-delay="0.2s">
                @auth
                    <a href="{{ route('frontend.my-reviews') }}" class="btn btn-outline-primary py-3 px-5">{{ __('dictt.write_review') }}</a>
                @endauth
                @guest
                    <a href="{{ route('frontend.login', ['return' => 'frontend.reviews']) }}"
                        class="btn btn-outline-primary py-3 px-5">{{ __('dictt.guest_review_cta') }}</a>
                @endguest
            </div>

            <div class="row g-4 justify-content-center">
                @forelse ($reviews as $review)
                    <div class="col-lg-4 col-md-6 d-flex wow fadeInUp" data-wow-delay="0.1s">
                        @include('frontend.partials.review-card', ['review' => $review])
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">{{ __('dictt.reviews_empty') }}</div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $reviews->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    <!-- Reviews End -->

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
