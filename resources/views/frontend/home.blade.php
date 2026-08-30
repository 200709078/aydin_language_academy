<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.home') }} | {{ __('dictt.ala') }}</title>
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
    <link href="{{ asset('frontend/css/style.css') }}?v=20260830-program-finder" rel="stylesheet">
</head>

<body class="frontend-home">
    @include('frontend.partials.header')

    <!-- Header Start -->
    <div class="container-fluid header ala-slider-shell p-0 mb-5">
        <div class="row g-0 align-items-center flex-column-reverse flex-lg-row">
            <div class="col-lg-6 p-5 wow fadeIn ala-slider-summary" data-wow-delay="0.1s">
                <h1 class="display-4 text-white mb-5">İngilizceyi öğrenme.<br>Onu yaşamaya başla.</h1>
                <div class="row g-4">
                    <div class="col-4 col-sm-4">
                        <div class="border-start border-light ps-4">
                            <h2 class="text-white mb-1" data-toggle="counter-up">3</h2>
                            <p class="text-light mb-0">Şube</p>
                        </div>
                    </div>
                    <div class="col-4 col-sm-4">
                        <div class="border-start border-light ps-4">
                            <h2 class="text-white mb-1"><span data-toggle="counter-up">200</span>+</h2>
                            <p class="text-light mb-0">Aktif Öğrenci</p>
                        </div>
                    </div>
                    <div class="col-4 col-sm-4">
                        <div class="border-start border-light ps-4">
                            <h2 class="text-white mb-1">%<span data-toggle="counter-up">95</span></h2>
                            <p class="text-light mb-0">Yerleştirme Başarısı</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                <div class="owl-carousel header-carousel">
                    <div class="owl-carousel-item position-relative">
                        <img class="img-fluid" src="{{ asset('frontend/images/slider-1.jpg') }}" alt="">
                        <div class="owl-carousel-text">
                            <h1 class="display-1 text-white mb-0">
                                <a class="header-carousel-branch-link" href="{{ route('frontend.branches.ortaca') }}">Ortaca</a>
                            </h1>
                        </div>
                    </div>
                    <div class="owl-carousel-item position-relative">
                        <img class="img-fluid" src="{{ asset('frontend/images/slider-2.jpg') }}" alt="">
                        <div class="owl-carousel-text">
                            <h1 class="display-1 text-white mb-0">
                                <a class="header-carousel-branch-link" href="{{ route('frontend.branches.dalaman') }}">Dalaman</a>
                            </h1>
                        </div>
                    </div>
                    <div class="owl-carousel-item position-relative">
                        <img class="img-fluid" src="{{ asset('frontend/images/slider-3.jpg') }}" alt="">
                        <div class="owl-carousel-text">
                            <h1 class="display-1 text-white mb-0">
                                <a class="header-carousel-branch-link" href="{{ route('frontend.branches.koycegiz') }}">Köyceğiz</a>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    @include('frontend.partials.about')

    <section class="ala-brand-band" aria-label="Aydın Language Academy">
        <div class="container">
            <img src="{{ asset('frontend/images/logo/logo-1.png') }}" alt="Aydın Language Academy">
        </div>
    </section>

    <!-- Why Us Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">{{ __('dictt.why_us') }}</h1>
                    <p>{{ __('dictt.why_us_text_1') }}</p>
                    <p>{{ __('dictt.why_us_text_2') }}</p>
                    <p>{{ __('dictt.why_us_text_3') }}</p>
                    <p class="mb-4">{{ __('dictt.why_us_text_4') }}</p>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/whyus.jpg') }}" alt="{{ __('dictt.why_us_image_alt') }}">
                </div>
            </div>
        </div>
    </div>
    <!-- Why Us End -->


    <!-- Service Start -->
    <div class="container-xxl py-5 ala-courses">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1>{{ __('dictt.ourcourses') }}</h1>
            </div>
            <div class="ala-program-finder-home wow fadeInUp" data-wow-delay="0.15s">
                <div class="ala-program-finder-home__content">
                    <span class="ala-program-finder-home__icon"><i class="fa fa-compass" aria-hidden="true"></i></span>
                    <div>
                        <h2 class="h4">{{ __('dictt.program_finder_title') }}</h2>
                        <p>{{ __('dictt.program_finder_home_intro') }}</p>
                    </div>
                </div>
                <a href="{{ route('frontend.program-finder') }}" class="btn btn-primary flex-shrink-0">
                    {{ __('dictt.program_finder_home_cta') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
                </a>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="{{ route('frontend.courses.ielts') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-plane-departure text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.ielts_prep') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <a href="{{ route('frontend.courses.yks-dil') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-university text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.yks_dil_prep') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <a href="{{ route('frontend.courses.yds-yokdil') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-book text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.yds_yokdil') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="{{ route('frontend.courses.toefl') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-globe text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.toefl') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <a href="{{ route('frontend.courses.pte-academic') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-laptop text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.pte_academic') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <a href="{{ route('frontend.courses.test-of-english') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-check-circle text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.test_of_english') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="{{ route('frontend.courses.sat') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-calculator text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.sat') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <a href="{{ route('frontend.courses.preschool') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-child text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.preschool') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <a href="{{ route('frontend.courses.primary-school') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-pencil-alt text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.primary_school') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="{{ route('frontend.courses.middle-school') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-book-open text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.middle_school') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <a href="{{ route('frontend.courses.high-school') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.high_school') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <a href="{{ route('frontend.courses.general-english') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-comments text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.general_english') }}</h4>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="{{ route('frontend.courses.speaking-clubs') }}" class="service-item bg-light rounded h-100 p-4 d-flex align-items-center text-decoration-none">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-users text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">{{ __('dictt.speaking_clubs') }}</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


    <!-- Testimonial Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1>{{ __('dictt.testimonials') }}</h1>
            </div>

            @if (($reviewCarousel ?? collect())->count() > 3)
                <div class="owl-carousel review-carousel wow fadeInUp" data-wow-delay="0.1s">
                    @foreach ($reviewCarousel as $review)
                        <div class="h-100 d-flex">
                            @include('frontend.partials.review-card', ['review' => $review])
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center align-items-center mt-4 wow fadeInUp" data-wow-delay="0.15s">
                    <button type="button" class="btn btn-outline-primary btn-lg-square rounded-circle me-2 d-inline-flex align-items-center justify-content-center review-prev" aria-label="{{ __('dictt.previous') }}"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-outline-primary btn-lg-square rounded-circle d-inline-flex align-items-center justify-content-center review-next" aria-label="{{ __('dictt.next') }}"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
                </div>
            @elseif (($latestReview ?? null) || ($previousReview ?? null) || ($firstReview ?? null))
                <div class="row g-4 justify-content-center wow fadeInUp" data-wow-delay="0.1s">
                    @if ($firstReview ?? null)
                        <div class="col-lg-4 col-md-6 d-flex">
                            @include('frontend.partials.review-card', ['review' => $firstReview])
                        </div>
                    @endif
                    @if ($latestReview ?? null)
                        <div class="col-lg-4 col-md-6 d-flex">
                            @include('frontend.partials.review-card', ['review' => $latestReview])
                        </div>
                    @endif
                    @if ($previousReview ?? null)
                        <div class="col-lg-4 col-md-6 d-flex">
                            @include('frontend.partials.review-card', ['review' => $previousReview])
                        </div>
                    @endif
                </div>
            @endif

            <div class="text-center mt-5 wow fadeInUp" data-wow-delay="0.2s">
                <a class="btn btn-outline-primary py-3 px-5 mb-2 mb-lg-0" href="{{ route('frontend.reviews') }}">{{ __('dictt.view_all') }}</a>
                @auth
                    <a class="btn btn-outline-primary py-3 px-5" href="{{ route('frontend.my-reviews') }}">{{ __('dictt.write_review') }}</a>
                @endauth
                @guest
                    <a class="btn btn-outline-primary py-3 px-5" href="{{ route('frontend.login', ['return' => 'frontend.my-reviews']) }}">{{ __('dictt.write_review') }}</a>
                @endguest
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


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
    <script src="{{ asset('frontend/js/main.js') }}?v=20260829-slider"></script>
</body>

</html>
