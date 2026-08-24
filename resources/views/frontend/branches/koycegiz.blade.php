<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.branch_koycegiz') }} | {{ __('dictt.ala') }}</title>
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

    <!-- Köyceğiz Branch Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/branches/ala_koycegiz.jpg') }}" alt="{{ __('dictt.branch_koycegiz') }}">
                    <div class="mt-4">
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fa fa-map-marker-alt text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">{{ __('dictt.branch_our_address') }}</p>
                                <h5 class="mb-0"><span class="text-dark text-break">Ulucamii İbrahim Koç Sokak Köyceğiz Lokantası Yanı Köyceğiz/Muğla</span></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fa fa-phone-alt text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">{{ __('dictt.branch_call_us') }}</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="tel:+905408284884">(540) 828 4884</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fa fa-envelope-open text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">{{ __('dictt.branch_send_email') }}</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="mailto:koycegiz@learnenglishwithala.com">koycegiz@learnenglishwithala.com</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fab fa-whatsapp text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">{{ __('dictt.branch_whatsapp_contact') }}</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="https://wa.me/905408284884" target="_blank" rel="noopener noreferrer">WhatsApp</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fab fa-youtube text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">{{ __('dictt.branch_youtube_channel') }}</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy" target="_blank" rel="noopener noreferrer">YouTube</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fab fa-instagram text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">{{ __('dictt.branch_instagram_account') }}</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==" target="_blank" rel="noopener noreferrer">Instagram</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">{{ __('dictt.branch_koycegiz') }}</h1>
                    <p>{{ __('dictt.koycegiz_intro') }}</p>
                    <p>{{ __('dictt.koycegiz_section_1_title') }}<br>{{ __('dictt.koycegiz_section_1_text') }}</p>
                    <p>{{ __('dictt.koycegiz_section_2_title') }}<br>{{ __('dictt.koycegiz_section_2_text') }}</p>
                    <p>{{ __('dictt.koycegiz_section_3_title') }}<br>{{ __('dictt.koycegiz_section_3_text') }}</p>
                    <p>{{ __('dictt.koycegiz_section_4_title') }}<br>{{ __('dictt.koycegiz_section_4_text') }}</p>
                    <p>{{ __('dictt.koycegiz_section_5_title') }}<br>{{ __('dictt.koycegiz_section_5_text') }}</p>
                    <p>{{ __('dictt.koycegiz_outro') }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Köyceğiz Branch End -->

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
