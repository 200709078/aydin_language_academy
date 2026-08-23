<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ $theme->name }} | {{ __('dictt.ala') }}</title>
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
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/themes.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Theme Detail Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <nav aria-label="breadcrumb animated slideInDown" class="mb-5 wow fadeIn" data-wow-delay="0.1s">
                <ol class="breadcrumb text-uppercase themes-crumb mb-0 py-3 px-4">
                    <li class="breadcrumb-item">{{ $theme->levels->name }}</li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.themes.list', [$theme->levels->slug, $theme->sub_levels->slug]) }}">{{ $theme->sub_levels->name }}</a>
                    </li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">{{ Str::limit($theme->name, 25) }}</li>
                </ol>
            </nav>

            <div id="themeAccordion" class="wow fadeIn" data-wow-delay="0.2s">
                <!-- Declarations -->
                @if ($declarations->isNotEmpty())
                    <h5 class="text-uppercase mb-3">{{ __('dictt.declarations') }}</h5>
                    <div class="accordion mb-5">
                        @foreach ($declarations as $declaration)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#declaration-{{ $declaration->id }}"
                                        aria-expanded="false">
                                        {{ $declaration->title }}
                                    </button>
                                </h2>
                                <div id="declaration-{{ $declaration->id }}"
                                    class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                                    <div class="accordion-body text-center">
                                        @if ($declaration->content)
                                            <p>{{ $declaration->contents }}</p>
                                        @endif
                                        @if ($declaration->image)
                                            <img class="img-fluid rounded my-2"
                                                src="{{ asset('photos/' . $declaration->image) }}" style="height:120px"
                                                alt="{{ $declaration->title }}" />
                                        @endif
                                        @if ($declaration->pdf)
                                            <iframe src="{{ asset('pdfs/' . $declaration->pdf) }}" width="90%"
                                                height="600px" title="{{ $declaration->title }}"></iframe>
                                        @endif
                                        <div class="d-flex flex-wrap justify-content-center gap-1 mt-2">
                                            @if ($declaration->video)
                                                <a href="{{ $declaration->video }}" class="btn btn-primary"
                                                    target="_blank" rel="noopener"><i class="fab fa-youtube"></i>
                                                    {{ __('dictt.video') }}</a>
                                            @endif
                                            @if ($declaration->voice)
                                                <a href="{{ $declaration->voice }}" class="btn btn-primary"
                                                    target="_blank" rel="noopener"><i class="fab fa-itunes-note"></i>
                                                    {{ __('dictt.voice') }}</a>
                                            @endif
                                            @if ($declaration->answerkey)
                                                <a href="{{ asset('pdfs/' . $declaration->answerkey) }}"
                                                    class="btn btn-primary" target="_blank"><i class="fab fa-adobe"></i>
                                                    {{ __('dictt.answerkey') }}</a>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-0 mt-2">{{ $declaration->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Exercises -->
                @if ($exercises->isNotEmpty())
                    <h5 class="text-uppercase mb-3">{{ __('dictt.exercises') }}</h5>
                    <div class="accordion">
                        @foreach ($exercises as $exercise)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#exercise-{{ $exercise->id }}"
                                        aria-expanded="false">
                                        {{ Str::limit($exercise->qtext ?: '#' . $exercise->id, 70) }}
                                    </button>
                                </h2>
                                <div id="exercise-{{ $exercise->id }}"
                                    class="accordion-collapse collapse" data-bs-parent="#themeAccordion">
                                    <div class="accordion-body">
                                    @if ($exercise->image)
                                        <img src="{{ asset('photos/' . $exercise->image) }}" style="width:20%"
                                            class="img-fluid mb-2" alt="">
                                    @endif
                                    @if ($exercise->qtext)
                                        <p>{{ $exercise->qtext }}</p>
                                    @endif
                                    @if ($exercise->video)
                                        <a href="{{ $exercise->video }}" class="btn btn-primary btn-sm m-1" target="_blank"
                                            rel="noopener"><i class="fab fa-youtube"></i> {{ __('dictt.video') }}</a>
                                    @endif
                                    @if ($exercise->voice)
                                        <a href="{{ $exercise->voice }}" class="btn btn-primary btn-sm m-1" target="_blank"
                                            rel="noopener"><i class="fab fa-itunes-note"></i> {{ __('dictt.voice') }}</a>
                                    @endif

                                    <form method="POST" action="{{ route('exercises.result', $exercise->id) }}">
                                        @csrf
                                        @foreach ($exercise->questions as $question)
                                            <p class="mb-1"><strong>{{ $loop->iteration }}) </strong>{{ $question->question }}</p>
                                            @if ($question->image)
                                                <img src="{{ asset('photos/' . $question->image) }}" style="width:20%"
                                                    class="img-fluid mb-2" alt="">
                                            @endif
                                            @foreach (['answer1', 'answer2', 'answer3', 'answer4', 'answer5'] as $answerKey)
                                                @if ($question->{$answerKey})
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="{{ $question->id }}"
                                                            id="q{{ $question->id }}_{{ $answerKey }}"
                                                            value="{{ $answerKey }}">
                                                        <label class="form-check-label"
                                                            for="q{{ $question->id }}_{{ $answerKey }}">
                                                            {{ $question->{$answerKey} }}
                                                        </label>
                                                    </div>
                                                @endif
                                            @endforeach
                                            <hr>
                                        @endforeach
                                        <button type="submit"
                                            class="btn btn-success w-100">{{ __('dictt.check') }}</button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($declarations->isEmpty() && $exercises->isEmpty())
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="fa fa-search text-primary fs-3"></i>
                        </div>
                        <h5 class="text-muted mb-0">{{ __('dictt.themenotfound') }}</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Theme Detail End -->

    @include('frontend.partials.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
