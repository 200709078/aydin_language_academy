<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.achievements') }} | {{ __('dictt.ala') }}</title>
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
    <link href="{{ asset('frontend/css/style.css') }}?v=20260904-news-nocrop-1" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Achievements Start -->
    <main class="container-xxl py-5 ala-achievements">
        <div class="container">
            @php
                $pageTitle = $pageSettings?->localized_title ?: __('dictt.achievements');
                $pageDescription = $pageSettings?->localized_description ?: __('dictt.achievement_public_intro');
                $hasHeroImage = $pageSettings?->heroMediaAsset !== null;
            @endphp

            <header class="row g-5 align-items-center mb-5">
                <div class="{{ $hasHeroImage ? 'col-lg-7' : 'col-12' }} wow fadeIn" data-wow-delay="0.1s">
                    <div class="ala-achievements__intro {{ $hasHeroImage ? '' : 'text-center mx-auto' }}">
                        <div class="d-flex align-items-center gap-3 {{ $hasHeroImage ? '' : 'justify-content-center' }} mb-3">
                            <span class="ala-achievements__intro-icon flex-shrink-0" aria-hidden="true">
                                <i class="fa fa-trophy"></i>
                            </span>
                            <h1 class="mb-0">{{ $pageTitle }}</h1>
                        </div>
                        <p class="mb-0">{{ $pageDescription }}</p>
                    </div>
                </div>
                @if ($hasHeroImage)
                    <div class="col-lg-5 wow fadeIn" data-wow-delay="0.5s">
                        <div class="text-center">
                            <img class="img-fluid" src="{{ $pageSettings->heroMediaAsset->publicUrl() }}"
                                alt="{{ $pageTitle }}" style="max-height: 360px;">
                        </div>
                    </div>
                @endif
            </header>

            @forelse ($achievementYears as $achievementYear)
                @if ($loop->first)
                    <div class="accordion ala-achievements__accordion wow fadeInUp" id="achievementYearsAccordion"
                        data-wow-delay="0.15s">
                @endif

                <section class="accordion-item">
                    @php
                        $isInitiallyOpen = (int) $achievementYear->id === (int) $initialOpenYearId;
                    @endphp
                    <h2 class="accordion-header" id="achievement-year-heading-{{ $achievementYear->id }}">
                        <button class="accordion-button {{ $isInitiallyOpen ? '' : 'collapsed' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#achievement-year-{{ $achievementYear->id }}"
                            aria-expanded="{{ $isInitiallyOpen ? 'true' : 'false' }}"
                            aria-controls="achievement-year-{{ $achievementYear->id }}">
                            <span class="ala-achievements__year-heading">
                                <span class="ala-achievements__year">{{ $achievementYear->year }}</span>
                                <span class="ala-achievements__year-title">{{ $achievementYear->title }}</span>
                            </span>
                        </button>
                    </h2>
                    <div id="achievement-year-{{ $achievementYear->id }}"
                        class="accordion-collapse collapse {{ $isInitiallyOpen ? 'show' : '' }}"
                        aria-labelledby="achievement-year-heading-{{ $achievementYear->id }}">
                        <div class="accordion-body">
                            @if (filled($achievementYear->description))
                                <p class="ala-achievements__year-description">{{ $achievementYear->description }}</p>
                            @endif

                            <div class="row g-4">
                                @foreach ($achievementYear->publicEntries as $entry)
                                    @php
                                        $publicName = $entry->publicDisplayName();
                                        $placement = collect([$entry->university_name, $entry->department_name])
                                            ->filter(fn (?string $value): bool => filled($value))
                                            ->implode(' · ');
                                    @endphp
                                    <div class="col-lg-6 d-flex">
                                        <article class="ala-achievement-entry h-100 w-100">
                                            <div class="ala-achievement-entry__identity">
                                                <span class="ala-achievement-entry__icon" aria-hidden="true">
                                                    <i class="fa fa-graduation-cap"></i>
                                                </span>
                                                <h3 class="h5 mb-0">
                                                    {{ $publicName ?? __('dictt.achievement_anonymous_student') }}
                                                </h3>
                                            </div>

                                            @if ($placement !== '')
                                                <p class="ala-achievement-entry__placement">
                                                    <i class="fa fa-university" aria-hidden="true"></i>{{ $placement }}
                                                </p>
                                            @endif

                                            @if (filled($entry->description))
                                                <p class="ala-achievement-entry__description">{{ $entry->description }}</p>
                                            @endif

                                            @if (filled($entry->branch) || filled($entry->card_sub_title))
                                                <div class="ala-achievement-entry__tags">
                                                    @if (filled($entry->card_sub_title))
                                                        <span>{{ $entry->card_sub_title }}</span>
                                                    @endif
                                                    @if (filled($entry->branch))
                                                        <span>{{ __('dictt.branch_' . $entry->branch) }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                @if ($loop->last)
                    </div>
                @endif
            @empty
                <section class="ala-achievements__empty text-center wow fadeInUp" data-wow-delay="0.15s">
                    <i class="fa fa-trophy" aria-hidden="true"></i>
                    <p class="mb-0">{{ __('dictt.achievement_public_empty') }}</p>
                </section>
            @endforelse
        </div>
    </main>
    <!-- Achievements End -->

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
</body>

</html>
