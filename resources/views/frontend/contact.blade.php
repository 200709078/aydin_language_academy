<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.contact') }} | {{ __('dictt.ala') }}</title>
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
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Contact Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 wow fadeIn" data-wow-delay="0.1s">
                    @php
                        $contactSubmitted = session('modalSuccessTitle') && session('modalSuccessContent');
                    @endphp
                    @if (session('modalSuccessTitle') && session('modalSuccessContent'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>
                                {!! session('modalSuccessTitle') !!}
                            </h4>
                            <p>{!! session('modalSuccessContent') !!}</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                            <h5 class="alert-heading d-flex align-items-center mb-2">
                                <i class="fas fa-times-circle me-2"></i>
                                {{ __('dictt.errors') }}
                            </h5>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="bg-light rounded p-5">
                        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                            <h6 class="text-primary text-uppercase mb-2">{{ __('dictt.contactus') }}</h6>
                            <p class="mb-0 text-uppercase">{{ $branchName }}</p>
                        </div>
                        <form method="post" action="{{ route('frontend.contact.submit', ['branch' => $branch]) }}" id="contactForm">
                            @csrf
                            <div class="visually-hidden" aria-hidden="true">
                                <label for="contact-website">Website</label>
                                <input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off" />
                            </div>
                            <div class="form-floating mb-2">
                                <input class="form-control" name="fullname" type="text"
                                    placeholder="{{ __('dictt.fullname') }}" value="{{ old('fullname', $contactSubmitted ? '' : auth()->user()?->name) }}"
                                    autocomplete="name" autocapitalize="words" data-name-titlecase />
                                <label>{{ __('dictt.fullname') }} :</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input class="form-control" name="email" type="email" placeholder="{{ __('dictt.email') }}" value="{{ old('email', $contactSubmitted ? '' : auth()->user()?->email) }}" />
                                <label>{{ __('dictt.email') }} :</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input class="form-control" name="telephone" type="tel"
                                    placeholder="+905XXXXXXXXX" value="{{ old('telephone', $contactSubmitted ? '' : auth()->user()?->phone) }}"
                                    required autocomplete="tel" inputmode="tel" maxlength="16" pattern="\+[1-9][0-9]{7,14}"
                                    title="{{ __('dictt.phone_international_format') }}" data-international-phone />
                                <label>{{ __('dictt.phone') }} :</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input class="form-control" name="subject" type="text"
                                    placeholder="{{ __('dictt.subject') }}" value="{{ old('subject', $contactSubmitted ? '' : __('dictt.contact_default_subject')) }}" />
                                <label>{{ __('dictt.subject') }} :</label>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" name="message" placeholder="{{ __('dictt.message') }}"
                                    style="height: 12rem">{{ old('message', $contactSubmitted ? '' : __('dictt.contact_default_message')) }}</textarea>
                                <label>{{ __('dictt.message') }} :</label>
                            </div>
                            <br />
                            <button class="btn btn-primary text-uppercase" type="submit">{{ __('dictt.send') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    @include('frontend.partials.footer')


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/easing/easing.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
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
                space + letter.toLocaleUpperCase('tr-TR')
            ));

            nameInput.value = capitalizeNameWords(nameInput.value);
            nameInput.addEventListener('input', () => {
                nameInput.value = capitalizeNameWords(nameInput.value);
            });

            const phoneInput = document.querySelector('[data-international-phone]');
            const defaultPrefix = '+905';
            const normalizePhone = (value) => {
                const digits = value.replace(/\D/g, '');

                return digits === '' ? '' : '+' + digits.slice(0, 15);
            };

            if (phoneInput) {
                if (phoneInput.value) {
                    phoneInput.value = normalizePhone(phoneInput.value);
                }

                phoneInput.addEventListener('focus', () => {
                    if (!phoneInput.value) {
                        phoneInput.value = defaultPrefix;
                        phoneInput.setSelectionRange(defaultPrefix.length, defaultPrefix.length);
                    }
                });

                phoneInput.addEventListener('input', () => {
                    phoneInput.value = normalizePhone(phoneInput.value);
                });
            }
        });
    </script>
</body>

</html>
