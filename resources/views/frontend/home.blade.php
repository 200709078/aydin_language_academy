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
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')
    @include('frontend.partials.about')


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
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1>{{ __('dictt.ourcourses') }}</h1>
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
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="{{ asset('frontend/images/testimonial-1.jpg') }}" style="width: 100px; height: 100px;">
                    <div class="testimonial-text rounded text-center p-4">
                        <p>Dersler hem eğlenceli hem de çok düzenli ilerledi. Kısa sürede İngilizce konuşurken kendime daha çok güvenmeye başladım.</p>
                        <h5 class="mb-1">Duru Kaya</h5>
                        <span class="fst-italic">Grafik Tasarımcı</span>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="{{ asset('frontend/images/testimonial-2.jpg') }}" style="width: 100px; height: 100px;">
                    <div class="testimonial-text rounded text-center p-4">
                        <p>Öğretmenlerim ihtiyaçlarıma göre yönlendirme yaptı. Özellikle konuşma pratiği sayesinde yabancı misafirlerle rahatça iletişim kurabiliyorum.</p>
                        <h5 class="mb-1">Efe Yılmaz</h5>
                        <span class="fst-italic">Turizm Uzmanı</span>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="{{ asset('frontend/images/testimonial-3.jpg') }}" style="width: 100px; height: 100px;">
                    <div class="testimonial-text rounded text-center p-4">
                        <p>Online ders seçeneği yoğun çalışma temposunda benim için çok faydalı oldu. Her dersten sonra ilerlediğimi net biçimde hissediyorum.</p>
                        <h5 class="mb-1">Selim Arslan</h5>
                        <span class="fst-italic">Yazılım Geliştirici</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


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
