@props(['header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $header }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/myStyles.css') }}">

    @livewireStyles

    <style>
        .frontend-profile-layout .frontend-header {
            --bs-light: #EFF5FF;
            --bs-light-rgb: 239, 245, 255;
            font-family: "Open Sans", sans-serif;
        }

        .frontend-profile-layout .footer {
            --bs-dark: #1B2C51;
            --bs-dark-rgb: 27, 44, 81;
            --bs-light: #EFF5FF;
            --bs-light-rgb: 239, 245, 255;
            background-color: #1B2C51 !important;
            font-family: "Open Sans", sans-serif;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
        }

        .frontend-profile-layout .footer a,
        .frontend-profile-layout .footer .btn.btn-link {
            text-decoration: none;
        }

        .frontend-profile-layout .footer h5 {
            font-family: "Roboto", sans-serif;
        }

        .frontend-profile-layout .footer .fa,
        .frontend-profile-layout .footer .fas,
        .frontend-profile-layout .footer .far {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        .frontend-profile-layout .footer .fab {
            font-family: "Font Awesome 5 Brands";
            font-weight: 400;
        }

        @media (min-width: 1200px) {
            .frontend-profile-content {
                padding-left: calc(16rem + 1.5rem);
            }
        }
    </style>
</head>

<body class="font-sans antialiased frontend-profile-layout">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @include('frontend.partials.header')

        <main class="frontend-profile-content py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if ($errors->any())
                    <div class="relative bg-red-100 text-red-800 px-6 py-4 rounded-lg shadow mb-6 w-full max-w-full">
                        <div
                            class="absolute bottom-[-10px] left-10 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-t-[10px] border-t-red-100">
                        </div>
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-times-circle mr-2"></i>
                                {{ __('dictt.errors') }}
                            </h2>
                            <button onclick="this.parentElement.parentElement.remove()"
                                class="text-gray-500 hover:text-red-600 ml-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @include('frontend.partials.footer')

    @stack('modals')
    @isset($js)
        {{ $js }}
    @endisset
    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var spinner = document.getElementById('spinner');

            if (spinner) {
                spinner.classList.remove('show');
            }
        })();
    </script>
</body>

</html>
