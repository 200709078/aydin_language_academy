<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>Seviye Tespit Sınavı | Aydın Language Academy</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <base href="{{ asset('ALA-FRONTEND/TEMPLATE') }}/">

    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-5 text-center">
                        @if ($placementTest->status === 'approved')
                            <i class="fa fa-check-circle text-success display-5 mb-4"></i>
                            <h1 class="mb-3">Sınav Sonucunuz Onaylandı</h1>
                            <p class="mb-4">Seviye tespit sınavınız yönetici tarafından onaylandı.</p>
                            @if ($placementTest->resultLevel)
                                <div class="h4 text-primary mb-4">Nihai seviyeniz: {{ $placementTest->resultLevel->code }}</div>
                            @endif
                        @else
                            <i class="fa fa-clock text-primary display-5 mb-4"></i>
                            <h1 class="mb-3">Sınavınız Tamamlandı</h1>
                            <p class="mb-4">Cevaplarınız kaydedildi. Sonucunuz yönetici onayından sonra kesinleşecektir.</p>
                        @endif
                    </div>

                    <div class="bg-light rounded p-4 mt-4">
                        <h4 class="mb-3">Sınav Bilgileri</h4>
                        @include('frontend.placement-test.attempt-summary', ['placementTest' => $placementTest])
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('frontend.placement-test') }}" class="btn btn-primary py-3 px-5">
                            Seviye Tespit Sınavı Sayfasına Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.footer')

    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
