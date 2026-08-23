<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>Ortaca | {{ __('dictt.ala') }}</title>
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
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Ortaca Branch Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/branches/ala_ortaca.jpg') }}" alt="Ortaca">
                    <div class="mt-4">
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fa fa-map-marker-alt text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">Adresimiz</p>
                                <h5 class="mb-0"><span class="text-dark text-break">Merkez Mahallesi Muhammed Kundakçı Caddesi Eski PTT Karşısı No:10 Ortaca/Muğla</span></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fa fa-phone-alt text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">Bizi Arayın</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="tel:+905468284884">(546) 828 4884</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fa fa-envelope-open text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">E-posta Gönderin</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="mailto:ortaca@learnenglishwithala.com">ortaca@learnenglishwithala.com</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fab fa-whatsapp text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">WhatsApp ile İletişime Geçin</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="https://wa.me/905468284884" target="_blank" rel="noopener noreferrer">WhatsApp</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3 mb-4">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fab fa-youtube text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">YouTube Kanalımız</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy" target="_blank" rel="noopener noreferrer">YouTube</a></h5>
                            </div>
                        </div>
                        <div class="bg-light rounded d-flex align-items-center p-3">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                                <i class="fab fa-instagram text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-1">Instagram Hesabımız</p>
                                <h5 class="mb-0"><a class="text-dark text-break" href="https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==" target="_blank" rel="noopener noreferrer">Instagram</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">Ortaca</h1>
                    <p>Aydın Language Academy, Ortaca'da İngilizce öğrenmek isteyenler için mükemmel bir seçenektir. Kaliteli eğitim ve uzman öğretmenlerimizle, dil becerilerinizi geliştirmek için size yardımcı oluyoruz.</p>
                    <p>Kaliteli Eğitim:<br>Aydın Language Academy olarak, öğrencilere en iyi eğitimi sunmayı taahhüt ediyoruz. Nitelikli ve deneyimli öğretmenlerimiz, interaktif dersler ve modern öğretim materyalleriyle öğrencilerin İngilizce becerilerini hızla geliştirmelerini sağlıyor.</p>
                    <p>Geniş Kurs Seçenekleri:<br>Ortaca'daki Aydın Language Academy, farklı seviyelerde ve ihtiyaçlara uygun çeşitli kurs seçenekleri sunar. Genel İngilizce, İş İngilizcesi, Akademik İngilizce ve daha fazlası için bize katılın ve İngilizce becerilerinizi geliştirin!</p>
                    <p>Esnek Programlar:<br>Yoğun bir programınız varsa endişelenmeyin! Aydın Language Academy, esnek programlar sunarak öğrencilerin ihtiyaçlarına uyum sağlar. Sabah, öğle veya akşam dersleri arasından seçim yapabilir ve kendi hızınızda ilerleyebilirsiniz.</p>
                    <p>Mükemmel Konum:<br>Aydın Language Academy, Ortaca'nın merkezinde yer almaktadır. Ulaşım açısından oldukça elverişli olan merkezimiz, öğrencilere kolaylık sağlar. Ortaca'nın güzel doğası ve tarihi mirasını keşfederken İngbecerilerinizi geliştirin!</p>
                    <p>Ücretsiz Deneme Dersleri:<br>Hala karar veremediniz mi? Hiç sorun değil! Aydın Language Academy, tüm potansiyel öğrencilere ücretsiz deneme dersleri sunmaktadır. Kurslarımızı deneyin ve sizin için en uygun olanı seçin.</p>
                    <p>Ortaca'da İngilizce öğrenmek isteyen herkes için Aydın Language Academy mükemmel bir seçenektir. Bizimle iletişime geçin ve dil becerilerinizi geliştirmeye hemen başlayın!</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Ortaca Branch End -->

    @include('frontend.partials.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
