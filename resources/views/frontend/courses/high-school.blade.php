<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>Lise | {{ __('dictt.ala') }}</title>
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

    <!-- High School Course Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/whoweare.png') }}" alt="Lise">
                    <p class="mt-4 mb-0 clearfix"><i class="fa fa-book-open text-primary fs-4 float-start me-3" aria-hidden="true"></i>{{ __('dictt.shared_course_image_text') }}</p>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">Lise</h1>
                    <p>Lise öğrencilerimize yönelik İngilizce programlarımız, onların gelecekteki akademik ve profesyonel hayatlarına güçlü bir başlangıç yapmalarını sağlamak üzere tasarlanmıştır. İngilizceyi etkili bir şekilde öğrenen lise öğrencileri, hem sınav başarılarında hem de uluslararası platformlarda avantaj kazanır.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Seviye Tespit ile Doğru Başlangıç</p>
                    <p class="mb-4">Her öğrencimiz, tarafımızca uygulanan seviye tespit sınavıyla değerlendirilir. Bu sınav sonuçlarına göre, öğrencilerimiz seviyelerine uygun sınıflara yerleştirilerek etkili bir dil eğitimi alır.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>İleri Düzey Dil Becerileri Eğitimi</p>
                    <p class="mb-4">Lise öğrencilerine yönelik derslerimiz, akademik ve profesyonel İngilizce kullanımını geliştirmeye odaklanır. Derslerde okuma, yazma, dinleme ve konuşma becerileri üzerinde çalışılırken, özellikle sınav teknikleri, sunum yapma ve eleştirel düşünce gibi ileri düzey becerilere de yer verilir.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Kaliteli Kaynaklar ve Uzman Eğitmenler</p>
                    <p class="mb-4">Lise seviyesine uygun olarak seçilmiş akademik ve güncel kaynaklar, uzman eğitmenlerimizin rehberliğinde kullanılır. Öğrencilerimizin hedeflerine ulaşmalarını sağlayacak materyallerle öğrenim sürecini destekliyoruz.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Grup Dersleri</p>
                    <p class="mb-4">Akademik başarı ve grup etkileşimiyle öğrenmeyi teşvik eder.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Özel Dersler</p>
                    <p class="mb-4">Kişiselleştirilmiş bir programla öğrencinin hedeflerine odaklanır.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- High School Course End -->

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
