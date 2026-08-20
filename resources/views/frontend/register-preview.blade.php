<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>Üye Ol | Aydın Language Academy</title>
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

    <!-- Registration Preview Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 wow fadeIn" data-wow-delay="0.1s">
                    <div class="text-center text-lg-start">
                        <img class="img-fluid mb-4" src="{{ asset('frontend/images/logo-2.png') }}" alt="Aydın Language Academy" style="max-width: 220px;">
                        <h1 class="mb-0">Üye Ol</h1>
                    </div>
                </div>
                <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded h-100 p-5">
                        <h4 class="mb-4">Üyelik Bilgileri</h4>
                        <div role="group" aria-describedby="registration-preview-note">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="name">Ad Soyad</label>
                                    <input id="name" class="form-control border-0" type="text" name="name" autocomplete="name" style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="email">E-posta Adresi</label>
                                    <input id="email" class="form-control border-0" type="email" name="email" autocomplete="email" style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="phone">Telefon Numarası</label>
                                    <input id="phone" class="form-control border-0" type="tel" name="phone" autocomplete="tel" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="password">Şifre</label>
                                    <input id="password" class="form-control border-0" type="password" name="password" autocomplete="new-password" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="password_confirmation">Şifre Tekrarı</label>
                                    <input id="password_confirmation" class="form-control border-0" type="password" name="password_confirmation" autocomplete="new-password" style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="button">Üye Ol</button>
                                </div>
                            </div>
                        </div>
                        <p id="registration-preview-note" class="small text-muted mb-0 mt-3">Bu önizleme aşamasında bilgiler kaydedilmez.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration Preview End -->

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
