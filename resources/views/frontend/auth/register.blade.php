<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.register') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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

    <!-- Registration Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 wow fadeIn" data-wow-delay="0.1s">
                    <div class="text-center text-lg-start">
                        <img class="img-fluid mb-4" src="{{ asset('frontend/images/logo/logo-2.png') }}" alt="Aydın Language Academy" style="max-width: 220px;">
                        <h1 class="mb-0">{{ __('dictt.register') }}</h1>
                    </div>
                </div>
                <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded h-100 p-5">
                        <h4 class="mb-4">{{ __('dictt.membership_details') }}</h4>

                        @if (isset($errors) && $errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="name">{{ __('dictt.fullname') }}</label>
                                    <input id="name" class="form-control border-0" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" autocapitalize="words" data-name-titlecase style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="email">{{ __('dictt.email_address') }}</label>
                                    <input id="email" class="form-control border-0" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" style="height: 55px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="phone">{{ __('dictt.phone_number') }}</label>
                                    <input id="phone" class="form-control border-0" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="tel" maxlength="32" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="password">{{ __('dictt.password') }}</label>
                                    <input id="password" class="form-control border-0" type="password" name="password" required autocomplete="new-password" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="password_confirmation">{{ __('dictt.password_repeat') }}</label>
                                    <input id="password_confirmation" class="form-control border-0" type="password" name="password_confirmation" required autocomplete="new-password" style="height: 55px;">
                                </div>
                                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input id="terms" class="form-check-input" type="checkbox" name="terms" required>
                                            <label class="form-check-label" for="terms">
                                                {!! __('dictt.accept_terms_note', [
                                                    'terms' => '<a href="' . route('terms.show') . '" target="_blank">' . __('dictt.terms_of_use') . '</a>',
                                                    'privacy' => '<a href="' . route('policy.show') . '" target="_blank">' . __('dictt.privacy_policy') . '</a>',
                                                ]) !!}
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">{{ __('dictt.register') }}</button>
                                </div>
                            </div>
                        </form>

                        <p class="text-center mb-0 mt-3">{{ __('dictt.have_account') }} <a href="{{ route('login') }}">{{ __('dictt.login_now') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration End -->

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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nameInput = document.querySelector('[data-name-titlecase]');

            if (!nameInput) {
                return;
            }

            const capitalizeNameWords = (value) => value.replace(/(^|\s)(\p{L})/gu, (match, space, letter) => (
                `${space}${letter.toLocaleUpperCase('tr-TR')}`
            ));

            nameInput.value = capitalizeNameWords(nameInput.value);
            nameInput.addEventListener('input', () => {
                nameInput.value = capitalizeNameWords(nameInput.value);
            });
        });
    </script>
</body>

</html>
