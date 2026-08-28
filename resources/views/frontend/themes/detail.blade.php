<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ $theme->name }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="{{ asset('frontend/css/themes.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <!-- Theme Detail Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <nav aria-label="breadcrumb animated slideInDown" class="mb-5 wow fadeIn" data-wow-delay="0.1s">
                <ol class="breadcrumb text-uppercase themes-crumb mb-0 py-3 px-4">
                    <li class="breadcrumb-item text-primary">{{ __('dictt.documents') }}</li>
                    <li class="breadcrumb-item text-primary">{{ $theme->levels->name }}</li>
                    <li class="breadcrumb-item text-primary">
                        <a class="text-primary" href="{{ route('frontend.themes.list', [$theme->levels->slug, $theme->sub_levels->slug]) }}">{{ $theme->sub_levels->name }}</a>
                    </li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">{{ Str::limit($theme->name, 25) }}</li>
                </ol>
            </nav>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('dictt.placement_test_close') }}"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('dictt.placement_test_close') }}"></button>
                </div>
            @endif

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
                            @php
                                $isOpenExercise = (int) session('open_exercise_id') === (int) $exercise->id;
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $isOpenExercise ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#exercise-{{ $exercise->id }}"
                                        aria-expanded="{{ $isOpenExercise ? 'true' : 'false' }}">
                                        {{ Str::limit($exercise->qtext ?: '#' . $exercise->id, 70) }}
                                    </button>
                                </h2>
                                <div id="exercise-{{ $exercise->id }}"
                                    class="accordion-collapse collapse {{ $isOpenExercise ? 'show' : '' }}" data-bs-parent="#themeAccordion">
                                    <div class="accordion-body">
                                        @if ($exercise->image || $exercise->qtext || $exercise->video || $exercise->voice)
                                            <section class="theme-exercise-content-card rounded p-3 p-lg-4 mb-4">
                                                @if ($exercise->image)
                                                    <img src="{{ asset('photos/' . $exercise->image) }}"
                                                        class="theme-exercise-media img-fluid rounded mb-3" alt="">
                                                @endif
                                                @if ($exercise->qtext)
                                                    <p class="text-dark lh-lg mb-3">{{ $exercise->qtext }}</p>
                                                @endif
                                                @if ($exercise->video || $exercise->voice)
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if ($exercise->video)
                                                            <a href="{{ $exercise->video }}" class="btn btn-primary btn-sm" target="_blank"
                                                                rel="noopener"><i class="fab fa-youtube"></i> {{ __('dictt.video') }}</a>
                                                        @endif
                                                        @if ($exercise->voice)
                                                            <a href="{{ $exercise->voice }}" class="btn btn-primary btn-sm" target="_blank"
                                                                rel="noopener"><i class="fab fa-itunes-note"></i> {{ __('dictt.voice') }}</a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </section>
                                        @endif
                                        @php
                                            $openAttempt = $exercise->attempts->firstWhere('status', 'in_progress');
                                            $completedAttemptCount = $exercise->attempts->where('status', 'completed')->count();
                                            $attemptSummary = $openAttempt?->summaryFor($exercise->questions);
                                            $answersByQuestion = $openAttempt?->answers->keyBy('question_id') ?? collect();
                                        @endphp

                                        <section class="theme-exercise-attempt-panel rounded p-3 p-lg-4 mb-4">
                                            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                                <div>
                                                    @if ($exercise->questions->isEmpty())
                                                        <h3 class="h6 text-dark mb-1">{{ __('dictt.exercise_attempt_no_questions') }}</h3>
                                                    @elseif ($openAttempt)
                                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                            <h3 class="h6 text-dark mb-0">{{ __('dictt.exercise_attempt_status_in_progress') }}</h3>
                                                            <span class="badge bg-primary text-white">{{ __('dictt.exercise_attempt_status_in_progress') }}</span>
                                                        </div>
                                                        <p class="small text-muted mb-0" data-exercise-answered-count
                                                            data-answered-count-template="{{ __('dictt.exercise_attempt_answered_count', ['answered' => '__ANSWERED__', 'total' => '__TOTAL__']) }}">
                                                            {{ __('dictt.exercise_attempt_answered_count', [
                                                                'answered' => $attemptSummary['answered'],
                                                                'total' => $attemptSummary['total'],
                                                            ]) }}
                                                        </p>
                                                    @else
                                                        <h3 class="h6 text-dark mb-1">{{ __('dictt.exercises') }}</h3>
                                                        <p class="small text-muted mb-0">{{ __('dictt.exercise_attempt_answers_autosave_note') }}</p>
                                                    @endif
                                                </div>

                                                <div class="d-flex flex-wrap gap-2">
                                                    @if (! $exercise->questions->isEmpty() && ! $openAttempt)
                                                        <form method="POST" action="{{ route('frontend.exercise-attempts.start', ['exercise' => $exercise]) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-play me-2" aria-hidden="true"></i>
                                                                {{ $completedAttemptCount > 0 ? __('dictt.exercise_attempt_new') : __('dictt.exercise_attempt_start') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('frontend.exercise-attempts.index', ['exercise' => $exercise]) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-history me-2" aria-hidden="true"></i>{{ __('dictt.exercise_attempt_history') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </section>

                                        @if ($openAttempt)
                                            <form method="POST"
                                                action="{{ route('frontend.exercise-attempts.complete', [
                                                    'exercise' => $exercise,
                                                    'exerciseAttempt' => $openAttempt,
                                                ]) }}"
                                                data-exercise-attempt-form
                                                data-total-questions="{{ $exercise->questions->count() }}"
                                                data-saving-message="{{ __('dictt.exercise_attempt_saving') }}"
                                                data-saved-message="{{ __('dictt.exercise_attempt_answer_saved') }}"
                                                data-failed-message="{{ __('dictt.exercise_attempt_save_failed') }}"
                                                data-retry-message="{{ __('dictt.exercise_attempt_retry_note') }}">
                                                @csrf
                                                @foreach ($exercise->questions as $question)
                                                    @php
                                                        $answer = $answersByQuestion->get($question->id);
                                                        $selectedOptionId = $answer?->question_option_id;
                                                    @endphp
                                                    <section class="theme-exercise-question-card rounded p-3 p-lg-4 mb-3"
                                                        data-exercise-question
                                                        data-answer-url="{{ route('frontend.exercise-attempts.answer', [
                                                            'exercise' => $exercise,
                                                            'exerciseAttempt' => $openAttempt,
                                                            'question' => $question,
                                                        ]) }}">
                                                        <div class="d-flex align-items-center gap-2 mb-3">
                                                            <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                                            <span class="small text-muted">{{ __('dictt.placement_test_question_label') }}</span>
                                                        </div>

                                                        <h3 class="h5 text-dark mb-4">{{ $question->question }}</h3>

                                                        @if ($question->image)
                                                            <img src="{{ asset('photos/' . $question->image) }}"
                                                                class="theme-exercise-media img-fluid rounded mb-4" alt="">
                                                        @endif

                                                        <div class="d-flex flex-column gap-3">
                                                            @foreach ($question->options as $option)
                                                                <label class="theme-exercise-option border rounded p-3 d-flex align-items-start gap-3 mb-0"
                                                                    for="attempt{{ $openAttempt->id }}_q{{ $question->id }}_o{{ $option->id }}">
                                                                    <input class="form-check-input mt-1" type="radio"
                                                                        name="answers[{{ $question->id }}]"
                                                                        id="attempt{{ $openAttempt->id }}_q{{ $question->id }}_o{{ $option->id }}"
                                                                        value="{{ $option->id }}"
                                                                        @checked((int) $selectedOptionId === (int) $option->id)>
                                                                    <span class="theme-exercise-option-text">{{ $option->option_text }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </section>
                                                @endforeach

                                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mt-4 pt-3 border-top">
                                                    <p class="small text-muted mb-0" data-exercise-save-status role="status" aria-live="polite">
                                                        @if ($attemptSummary['answered'] > 0)
                                                            <i class="fa fa-check-circle text-success" aria-hidden="true"></i> {{ __('dictt.exercise_attempt_answer_saved') }}
                                                        @else
                                                            {{ __('dictt.exercise_attempt_answers_autosave_note') }}
                                                        @endif
                                                    </p>
                                                    <button type="submit" class="btn btn-success" data-exercise-complete-button>
                                                        {{ __('dictt.theme_exercise_complete') }}<i class="fa fa-check ms-2" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
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


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/easing/easing.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('frontend/js/main.js') }}"></script>
    <script src="{{ asset('frontend/js/exercise-attempt.js') }}"></script>
</body>

</html>
