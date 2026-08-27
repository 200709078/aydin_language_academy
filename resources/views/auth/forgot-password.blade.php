<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.password_reset') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('frontend/vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">

    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 wow fadeIn" data-wow-delay="0.1s">
                    <div class="text-center text-lg-start">
                        <img class="img-fluid mb-4" src="{{ asset('frontend/images/logo/logo-2.png') }}"
                            alt="Aydın Language Academy" style="max-width: 220px;">
                        <h1 class="mb-0">{{ __('dictt.password_reset') }}</h1>
                    </div>
                </div>
                <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded h-100 p-5">
                        <h4 class="mb-3">{{ __('dictt.forgot_my_password') }}</h4>
                        <p class="text-muted mb-4">{{ __('dictt.password_reset_instruction') }}</p>

                        @if (session('status'))
                            <div class="alert alert-success" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (isset($errors) && $errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="email">{{ __('dictt.email_address') }}</label>
                                    <input id="email" class="form-control border-0" type="email" name="email"
                                        value="{{ old('email') }}" required autofocus autocomplete="username"
                                        style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">
                                        {{ __('dictt.send_password_reset_link') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <p class="text-center mb-0 mt-3">
                            <a href="{{ route('login') }}">
                                <i class="fa fa-arrow-left me-2"></i>{{ __('dictt.back_to_login') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.footer')

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
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
