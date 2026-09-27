<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Çok Fazla İstek | Aydın Language Academy</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">

    <style>
        .maintenance-icon {
            width: 72px;
            height: 72px;
        }
        .maintenance-card {
            max-width: 620px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-xxl min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="maintenance-card w-100 text-center">
            <img class="img-fluid mb-4" src="{{ asset('frontend/images/logo/logo-2.png') }}" alt="Aydın Language Academy" style="max-width: 220px;">

            <div class="bg-white rounded shadow-sm p-4 p-md-5">
                <div class="maintenance-icon bg-light text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                    <i class="fa fa-hourglass-half fa-2x" aria-hidden="true"></i>
                </div>
                <h1 class="mb-2">Çok Fazla İstek</h1>
                <p class="text-muted mb-1">Kısa sürede çok fazla istek gönderdiniz. Lütfen biraz bekleyip tekrar deneyin.</p>
                <p class="text-muted mb-4">Too many requests. Please wait a moment and try again.</p>
                <button type="button" class="btn btn-primary px-4" onclick="window.location.reload()">Tekrar Dene / Try Again</button>
                <p class="text-muted small mt-3 mb-0">429</p>
            </div>
        </div>
    </div>
</body>
</html>
