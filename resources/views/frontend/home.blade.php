<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Klinik - Clinic Website Template</title>
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
    @include('frontend.partials.about')


    <!-- Why Us Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">Neden Biz!</h1>
                    <p>Genel İngilizce kurslarımız ilkokul, ortaokul ve lise seviyelerinde, öğrencilerin dil becerilerini geliştirmeye yönelik kapsamlı bir program sunmaktadır. Ayrıca, YKS ve IELTS gibi önemli sınavlara hazırlık kurslarımız ile öğrencilerimizi bu zorlu süreçte en iyi şekilde destekliyoruz. Amacımız, her öğrencinin potansiyelini keşfetmesine ve en yüksek dereceleri elde etmesine olanak tanımaktır.</p>
                    <p>Kurumumuzda, ücretsiz seviye tespit sınavları hem online hem de yüz yüze olarak gerçekleştirilmektedir. Bu sayede, öğrencilerimizin dil seviyeleri doğru bir şekilde belirlenir ve en uygun eğitim programına yönlendirilirler. Ayrıca, online ders seçeneklerimiz ile öğrencilerimize esnek bir öğrenme imkanı sunuyoruz.</p>
                    <p>Bunun yanı sıra, diğer dünya dillerinde de eğitim vererek, öğrencilerimizin dil dağarcıklarını zenginleştirmeyi hedefliyoruz. Alanında uzman eğitmen kadromuz, etkileşimli ve eğlenceli bir öğrenme ortamı yaratarak, dil öğrenimini keyifli hale getiriyor.</p>
                    <p class="mb-4">Aydın Dil Akademisi olarak, öğrenme sürecinin her aşamasında yanınızdayız. Bize katılarak, dil öğreniminde yeni ufuklar açabilir ve geleceğinizi şekillendirebilirsiniz.</p>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/whyus.jpg') }}" alt="Neden Biz">
                </div>
            </div>
        </div>
    </div>
    <!-- Why Us End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1>Kurslarımız</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item bg-light rounded h-100 p-5 d-flex align-items-center">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">Genel İngilizce</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item bg-light rounded h-100 p-5 d-flex align-items-center">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">Okul Öncesi</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item bg-light rounded h-100 p-5 d-flex align-items-center">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">İlkokul</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item bg-light rounded h-100 p-5 d-flex align-items-center">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">Ortaokul</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item bg-light rounded h-100 p-5 d-flex align-items-center">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">Lise</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item bg-light rounded h-100 p-5 d-flex align-items-center">
                        <div class="d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle me-4" style="width: 65px; height: 65px;">
                            <i class="fa fa-graduation-cap text-primary fs-4"></i>
                        </div>
                        <h4 class="mb-0">Yetişkin</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


    <!-- Testimonial Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1>Öğrencilerimizin Yorumları</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="img/testimonial-1.jpg" style="width: 100px; height: 100px;">
                    <div class="testimonial-text rounded text-center p-4">
                        <p>Dersler hem eğlenceli hem de çok düzenli ilerledi. Kısa sürede İngilizce konuşurken kendime daha çok güvenmeye başladım.</p>
                        <h5 class="mb-1">Duru Kaya</h5>
                        <span class="fst-italic">Grafik Tasarımcı</span>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="img/testimonial-2.jpg" style="width: 100px; height: 100px;">
                    <div class="testimonial-text rounded text-center p-4">
                        <p>Öğretmenlerim ihtiyaçlarıma göre yönlendirme yaptı. Özellikle konuşma pratiği sayesinde yabancı misafirlerle rahatça iletişim kurabiliyorum.</p>
                        <h5 class="mb-1">Efe Yılmaz</h5>
                        <span class="fst-italic">Turizm Uzmanı</span>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="img-fluid bg-light rounded-circle p-2 mx-auto mb-4" src="img/testimonial-3.jpg" style="width: 100px; height: 100px;">
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
