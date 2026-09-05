<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.seo_branch_title', ['branch' => 'Ortaca']) }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="{{ __('dictt.seo_branch_keywords') }}, Muğla Ortaca Dil Kursu" name="keywords">
    <meta content="{{ __('dictt.seo_branch_description', ['branch' => 'Ortaca']) }}" name="description">

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
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Branches Start -->
    <div class="container-xxl pt-5 pb-0">
        <div class="container">
            <div class="text-center mb-2">
                <h1 class="mb-2">{{ app()->getLocale() === 'tr' ? 'ŞUBELERİMİZ' : mb_strtoupper(__('dictt.branches'), 'UTF-8') }}</h1>
                <h2 class="mb-0 text-center text-lg-start">Muğla Ortaca Dil Kursu</h2>
            </div>
            <div class="row g-5">
                <!-- Mobil: 1. ortaca-1.png | PC: sol alt -->
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/branches/ortaca-2.png') }}" alt="Ortaca">
                </div>
                <!-- Mobil: 2. Kartlar | PC: sağ alt -->
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div>
                        <a class="text-decoration-none" href="https://maps.google.com/?cid=8402083866868113807&g_mp=CiVnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLkdldFBsYWNlEAMYASAF&hl=tr&gl=TR&source=embed" target="_blank" rel="noopener noreferrer">
                            <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                    <i class="fa fa-map-marker-alt text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1">{{ __('dictt.branch_our_address') }}</p>
                                    <h5 class="mb-0"><span class="text-dark text-break">Merkez Mahallesi Muhammed Kundakçı Caddesi Eski PTT Karşısı No:10 Ortaca/Muğla</span></h5>
                                </div>
                            </div>
                        </a>
                        <a class="text-decoration-none" href="tel:+905468284884">
                            <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                    <i class="fa fa-phone-alt text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1">{{ __('dictt.branch_call_us') }}</p>
                                    <h5 class="mb-0"><span class="text-dark text-break">+905468284884</span></h5>
                                </div>
                            </div>
                        </a>
                        <a class="text-decoration-none" href="{{ route('frontend.contact', ['branch' => 'ortaca']) }}">
                            <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                    <i class="fa fa-envelope-open text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1">{{ __('dictt.branch_send_email') }}</p>
                                    <h5 class="mb-0"><span class="text-dark text-break">ortaca@learnenglishwithala.com</span></h5>
                                </div>
                            </div>
                        </a>
                        <a class="text-decoration-none" href="https://web.whatsapp.com/send?phone=905468284884&amp;text=Selamlar%2C%20ALA%20web%20sitesi%20%C3%BCzerinden%20yaz%C4%B1yorum." target="_blank" rel="noopener noreferrer" data-whatsapp-app-url="whatsapp://send?phone=905468284884&amp;text=Selamlar%2C%20ALA%20web%20sitesi%20%C3%BCzerinden%20yaz%C4%B1yorum.">
                            <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                    <i class="fab fa-whatsapp text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1">{{ __('dictt.branch_whatsapp_contact') }}</p>
                                    <h5 class="mb-0"><span class="text-dark text-break">WhatsApp</span></h5>
                                </div>
                            </div>
                        </a>
                        <a class="text-decoration-none" href="https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy" target="_blank" rel="noopener noreferrer">
                            <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                    <i class="fab fa-youtube text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1">{{ __('dictt.branch_youtube_channel') }}</p>
                                    <h5 class="mb-0"><span class="text-dark text-break">YouTube</span></h5>
                                </div>
                            </div>
                        </a>
                        <a class="text-decoration-none" href="https://www.instagram.com/aydindilakademisiortaca" target="_blank" rel="noopener noreferrer">
                            <div class="bg-light rounded d-flex align-items-center p-3">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                    <i class="fab fa-instagram text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-1">{{ __('dictt.branch_instagram_account') }}</p>
                                    <h5 class="mb-0"><span class="text-dark text-break">Instagram</span></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-0 pt-5 wow fadeIn" data-wow-delay="0.1s">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d51115.311219381656!2d28.748261982330863!3d36.80157602214068!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14c07b701dfcdc7b%3A0x749a32c2c1b6e18f!2sAYDIN%20D%C4%B0L%20AKADEM%C4%B0S%C4%B0%20YABANCI%20D%C4%B0L%20KURSU%20(ORTACA)!5e0!3m2!1str!2str!4v1788550638495!5m2!1str!2str" width="100%" height="450" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="AYDIN DİL AKADEMİSİ YABANCI DİL KURSU (ORTACA) - Google Haritalar"></iframe>
            </div>

            <section class="pt-5">
                <h2 class="mb-2 text-center text-lg-start">Muğla Dalaman Dil Kursu</h2>
                <div class="row g-5">
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/branches/dalaman-1.jpg') }}" alt="Dalaman">
                    </div>
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <a class="text-decoration-none" href="https://maps.google.com/?cid=13451178032151641036&g_mp=CiVnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLkdldFBsYWNlEAMYASAF&hl=tr&gl=TR&source=embed" target="_blank" rel="noopener noreferrer">
                            <div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fa fa-map-marker-alt text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_our_address') }}</p><h5 class="mb-0"><span class="text-dark text-break">Karaçalı Mahallesi, Şehit Hamza Atakul Caddesi No:39/A Dalaman/Muğla</span></h5></div></div>
                        </a>
                        <a class="text-decoration-none" href="tel:+905308284884"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fa fa-phone-alt text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_call_us') }}</p><h5 class="mb-0"><span class="text-dark text-break">+905308284884</span></h5></div></div></a>
                        <a class="text-decoration-none" href="{{ route('frontend.contact', ['branch' => 'dalaman']) }}"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fa fa-envelope-open text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_send_email') }}</p><h5 class="mb-0"><span class="text-dark text-break">dalaman@learnenglishwithala.com</span></h5></div></div></a>
                        <a class="text-decoration-none" href="https://web.whatsapp.com/send?phone=905308284884&amp;text=Selamlar%2C%20ALA%20web%20sitesi%20%C3%BCzerinden%20yaz%C4%B1yorum." target="_blank" rel="noopener noreferrer" data-whatsapp-app-url="whatsapp://send?phone=905308284884&amp;text=Selamlar%2C%20ALA%20web%20sitesi%20%C3%BCzerinden%20yaz%C4%B1yorum."><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fab fa-whatsapp text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_whatsapp_contact') }}</p><h5 class="mb-0"><span class="text-dark text-break">WhatsApp</span></h5></div></div></a>
                        <a class="text-decoration-none" href="https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy" target="_blank" rel="noopener noreferrer"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fab fa-youtube text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_youtube_channel') }}</p><h5 class="mb-0"><span class="text-dark text-break">YouTube</span></h5></div></div></a>
                        <a class="text-decoration-none" href="https://www.instagram.com/aydindilakademisi.tr" target="_blank" rel="noopener noreferrer"><div class="bg-light rounded d-flex align-items-center p-3"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fab fa-instagram text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_instagram_account') }}</p><h5 class="mb-0"><span class="text-dark text-break">Instagram</span></h5></div></div></a>
                    </div>
                </div>
            </section>

            <div class="container-fluid px-0 pt-5 wow fadeIn" data-wow-delay="0.1s">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3196.2412788987353!2d28.807965776268485!3d36.76477886969464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14c0712838e60f13%3A0xbaac2f2720efbbcc!2sAYDIN%20D%C4%B0L%20AKADEM%C4%B0S%C4%B0%20YABANCI%20D%C4%B0L%20KURSU%20(DALAMAN)!5e0!3m2!1str!2str!4v1787575566648!5m2!1str!2str" width="100%" height="450" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="AYDIN DİL AKADEMİSİ YABANCI DİL KURSU (DALAMAN) - Google Haritalar"></iframe>
            </div>

            <section class="pt-5">
                <h2 class="mb-2 text-center text-lg-start">Muğla Köyceğiz Dil Kursu</h2>
                <div class="row g-5">
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/branches/koycegiz-1.jpg') }}" alt="Köyceğiz">
                    </div>
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <a class="text-decoration-none" href="https://maps.google.com/?cid=17191200493032107110&g_mp=CiVnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLkdldFBsYWNlEAMYASAF&hl=tr&gl=TR&source=embed" target="_blank" rel="noopener noreferrer"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fa fa-map-marker-alt text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_our_address') }}</p><h5 class="mb-0"><span class="text-dark text-break">Ulucamii İbrahim Koç Sokak Köyceğiz Lokantası Yanı Köyceğiz/Muğla</span></h5></div></div></a>
                        <a class="text-decoration-none" href="tel:+905408284884"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fa fa-phone-alt text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_call_us') }}</p><h5 class="mb-0"><span class="text-dark text-break">+905408284884</span></h5></div></div></a>
                        <a class="text-decoration-none" href="{{ route('frontend.contact', ['branch' => 'koycegiz']) }}"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fa fa-envelope-open text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_send_email') }}</p><h5 class="mb-0"><span class="text-dark text-break">koycegiz@learnenglishwithala.com</span></h5></div></div></a>
                        <a class="text-decoration-none" href="https://web.whatsapp.com/send?phone=905408284884&amp;text=Selamlar%2C%20ALA%20web%20sitesi%20%C3%BCzerinden%20yaz%C4%B1yorum." target="_blank" rel="noopener noreferrer" data-whatsapp-app-url="whatsapp://send?phone=905408284884&amp;text=Selamlar%2C%20ALA%20web%20sitesi%20%C3%BCzerinden%20yaz%C4%B1yorum."><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fab fa-whatsapp text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_whatsapp_contact') }}</p><h5 class="mb-0"><span class="text-dark text-break">WhatsApp</span></h5></div></div></a>
                        <a class="text-decoration-none" href="https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy" target="_blank" rel="noopener noreferrer"><div class="bg-light rounded d-flex align-items-center p-3 mb-4"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fab fa-youtube text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_youtube_channel') }}</p><h5 class="mb-0"><span class="text-dark text-break">YouTube</span></h5></div></div></a>
                        <a class="text-decoration-none" href="https://www.instagram.com/aydindilakademisikoycegiz" target="_blank" rel="noopener noreferrer"><div class="bg-light rounded d-flex align-items-center p-3"><div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;"><i class="fab fa-instagram text-primary"></i></div><div class="ms-3"><p class="mb-1">{{ __('dictt.branch_instagram_account') }}</p><h5 class="mb-0"><span class="text-dark text-break">Instagram</span></h5></div></div></a>
                    </div>
                </div>
            </section>

            <div class="container-fluid px-0 pt-5 wow fadeIn" data-wow-delay="0.1s">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3188.1415253123455!2d28.680515976275284!3d36.95867665874949!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14c07f2732eeadd3%3A0xee9369c3f9b2ec66!2zQVlESU4gRMSwTCBBS0FERU3EsFPEsCBZQUJBTkNJIETEsEwgS1VSU1UgKEvDlllDRcSexLBaKQ!5e0!3m2!1str!2str!4v1787575794008!5m2!1str!2str" width="100%" height="450" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="AYDIN DİL AKADEMİSİ YABANCI DİL KURSU (KÖYCEĞİZ) - Google Haritalar"></iframe>
            </div>
        </div>
    </div>
    <!-- Branches End -->

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
