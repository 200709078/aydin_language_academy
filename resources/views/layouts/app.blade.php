<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $header }} - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link href="{{ asset('front/') }}/img/favicon.png" rel="icon">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <!-- My Styles -->
    <link rel="stylesheet" href="{{ asset('css/myStyles.css') }}">
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            @php
                $isThemesMenuRoute = request()->routeIs(
                    'levels_*',
                    'level_*',
                    'sub_levels_*',
                    'sub_level_*',
                    'themes_*',
                    'theme_*',
                    'courses_*',
                    'course_*',
                    'declarations_*',
                    'declaration_*',
                    'exercises_*',
                    'exercise_*',
                    'questions_*',
                    'question_*',
                );
                $showThemesBreadcrumb = request()->is('admin', 'admin/*')
                    && $isThemesMenuRoute;
                $showPlacementTestBreadcrumb = request()->is('admin', 'admin/*')
                    && request()->routeIs(
                        'placement_test_levels_*',
                        'placement_test_question_contents_*',
                        'placement_test_questions_*',
                    );
            @endphp

            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        @if ($showThemesBreadcrumb)
                            {{ __('dictt.themes') }}
                            <svg class="admin-breadcrumb-separator" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        @elseif ($showPlacementTestBreadcrumb)
                            Placement Test
                            <svg class="admin-breadcrumb-separator" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        @endif
                        {{ $header }}
                    </h2>
                </div>
            </header>
        @endif

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Errors Start -->
                @if ($errors->any())
                    <div class="relative bg-red-100 text-red-800 px-6 py-4 rounded-lg shadow mb-6 w-full max-w-full">
                        <div
                            class="absolute bottom-[-10px] left-10 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-t-[10px] border-t-red-100">
                        </div>
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-times-circle mr-2"></i>
                                {{__('dictt.errors')}}
                            </h2>
                            <button onclick="this.parentElement.parentElement.remove()"
                                class="text-gray-500 hover:text-red-600 ml-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{$error}}</li>
                            @endforeach
                        </div>
                    </div>
                @endif
                <!-- Errors End -->
                {{ $slot }}
            </div>
        </div>
    </div>

    @stack('modals')
    @isset($js)
        {{ $js }}
    @endisset
    @livewireScripts
</body>

</html>
