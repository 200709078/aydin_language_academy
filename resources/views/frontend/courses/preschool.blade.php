<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>Okul Öncesi | {{ __('dictt.ala') }}</title>
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

    <!-- Preschool Course Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontend/images/whoweare.png') }}" alt="Okul Öncesi">
                    <p class="mt-4 mb-0 clearfix"><i class="fa fa-book-open text-primary fs-4 float-start me-3" aria-hidden="true"></i>{{ __('dictt.shared_course_image_text') }}</p>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="mb-4">Okul Öncesi</h1>
                    <p>Okul öncesi öğrencilerimiz için hazırlanan İngilizce eğitim programlarımız, dil öğrenme sürecini eğlenceli ve etkileşimli bir hale getirir. Bu yaş grubunun doğal öğrenme yetenekleri göz önünde bulundurularak, dil becerilerini oyunlar, şarkılar ve çeşitli yaratıcı etkinliklerle geliştirmeyi hedefliyoruz.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Seviye Tespit ile Kişiye Özel Eğitim</p>
                    <p class="mb-4">Okul öncesi öğrencilerimiz için seviye tespit sınavı uygulanmaz, çünkü bu yaş grubunda dil öğrenimi doğal bir süreç olarak ele alınır. Öğrenciler, yaşlarına uygun materyallerle eğlenceli bir şekilde dil becerilerini geliştirir. Ancak, öğrencinin dil gelişimi ve ihtiyaçları, öğretmenlerimiz tarafından sürekli izlenir ve her çocuğa özel eğitim yaklaşımı benimsenir.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Dil Becerilerini Eğlenceli Yöntemlerle Geliştirme</p>
                    <p class="mb-4">Okul öncesi programımız, İngilizceyi öğrenmeye yönelik temel becerileri geliştirmeye odaklanır. Çocuklar, şarkılar, hikayeler ve oyunlar aracılığıyla dilin temellerini öğrenirken, özellikle dinleme, konuşma ve kelime dağarcığını geliştirme üzerine yoğunlaşılır. Bu sayede, çocuklar doğal bir şekilde İngilizceyi günlük yaşamlarında kullanmaya başlarlar.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Uzman Eğitmenler ve Oyun Bazlı Kaynaklar</p>
                    <p class="mb-4">Eğitim materyallerimiz, uzman eğitmenlerimiz tarafından seçilen renkli ve eğlenceli içeriklerden oluşur. Hikayeler, şarkılar ve resimli kitaplar, çocukların dikkatini çekmek ve onların öğrenmesini desteklemek için kullanılmaktadır. Öğrenme süreci oyun tabanlı olduğundan, çocuklar doğal bir şekilde İngilizceyi keşfeder ve eğlenerek öğrenir.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Grup Dersleri</p>
                    <p class="mb-4">Çocukların sosyal becerilerini geliştirirken, dil öğrenme süreçlerine aktif katılımlarını sağlar.</p>
                    <p class="mb-2 fw-bold"><i class="far fa-check-circle text-primary me-3"></i>Özel Dersler</p>
                    <p class="mb-4">Çocuğun bireysel hızına ve ihtiyaçlarına göre uyarlanmış kişisel bir öğrenme deneyimi sunar.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Preschool Course End -->

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
