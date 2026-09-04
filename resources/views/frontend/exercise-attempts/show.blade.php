<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.exercise_attempt_review_title') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

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
    <link href="{{ asset('frontend/css/themes.css') }}" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-5 wow fadeIn" data-wow-delay="0.1s">
                <ol class="breadcrumb text-uppercase themes-crumb mb-0 py-3 px-4">
                    <li class="breadcrumb-item text-primary">{{ __('dictt.documents') }}</li>
                    <li class="breadcrumb-item text-primary">{{ $theme->levels->name }}</li>
                    <li class="breadcrumb-item text-primary">
                        <a class="text-primary" href="{{ route('frontend.themes.list', [$theme->levels->slug, $theme->sub_levels->slug]) }}">
                            {{ $theme->sub_levels->name }}
                        </a>
                    </li>
                    <li class="breadcrumb-item text-primary">
                        <a class="text-primary" href="{{ route('frontend.themes.detail', ['theme_id' => $theme]) }}">{{ Str::limit($theme->name, 25) }}</a>
                    </li>
                    <li class="breadcrumb-item text-primary">
                        <a class="text-primary" href="{{ route('frontend.exercise-attempts.index', ['exercise' => $exercise]) }}">
                            {{ __('dictt.exercise_attempt_history') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">{{ __('dictt.exercise_attempt_review') }}</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-xl-10 wow fadeIn" data-wow-delay="0.15s">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
                        <div>
                            <h1 class="h2 mb-1">{{ __('dictt.exercise_attempt_review_title') }}</h1>
                            <p class="text-muted mb-0">{{ Str::limit($exercise->qtext ?: $exercise->title, 120) }}</p>
                        </div>
                        <a href="{{ route('frontend.exercise-attempts.index', ['exercise' => $exercise]) }}"
                            class="btn btn-outline-primary align-self-sm-start">
                            <i class="fa fa-arrow-left me-2" aria-hidden="true"></i>{{ __('dictt.back_short') }}
                        </a>
                    </div>

                    <section class="theme-exercise-review-summary rounded p-3 p-md-4 mb-4">
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="small text-muted">{{ __('dictt.exercise_attempt_started_at') }}</div>
                                <div class="fw-semibold">{{ $exerciseAttempt->started_at?->format('d.m.Y H:i') ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small text-muted">{{ __('dictt.exercise_attempt_completed_at') }}</div>
                                <div class="fw-semibold">{{ $exerciseAttempt->completed_at?->format('d.m.Y H:i') ?? '—' }}</div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="small text-muted">{{ __('dictt.exercise_attempt_correct') }}</div>
                                <div class="fw-semibold text-success">{{ $summary['correct'] }}</div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="small text-muted">{{ __('dictt.exercise_attempt_wrong') }}</div>
                                <div class="fw-semibold text-danger">{{ $summary['wrong'] }}</div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="small text-muted">{{ __('dictt.exercise_attempt_blank') }}</div>
                                <div class="fw-semibold text-warning">{{ $summary['blank'] }}</div>
                            </div>
                        </div>
                    </section>

                    <div class="alert alert-info" role="note">
                        <i class="fa fa-info-circle me-1" aria-hidden="true"></i>{{ __('dictt.exercise_attempt_read_only_note') }}
                    </div>
                    <div class="alert alert-secondary" role="note">
                        <i class="fa fa-check-circle me-1" aria-hidden="true"></i>{{ __('dictt.exercise_attempt_review_note') }}
                    </div>

                    @php
                        $answersByQuestion = $exerciseAttempt->answers->keyBy('question_id');
                    @endphp

                    @forelse ($exercise->questions as $question)
                        @php
                            $answer = $answersByQuestion->get($question->id);
                            $selectedOption = $answer?->selectedOption;
                            $correctOption = $question->options->firstWhere('is_correct', true);
                            $answerStatus = match (true) {
                                $selectedOption === null => 'blank',
                                $correctOption !== null && (int) $selectedOption->id === (int) $correctOption->id => 'correct',
                                default => 'wrong',
                            };
                        @endphp
                        <article class="theme-exercise-question-card rounded p-3 p-lg-4 mb-3">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                    <span class="small text-muted">{{ __('dictt.placement_test_question_label') }}</span>
                                </div>
                                @if ($answerStatus === 'correct')
                                    <span class="badge bg-success text-white">{{ __('dictt.exercise_attempt_correct') }}</span>
                                @elseif ($answerStatus === 'wrong')
                                    <span class="badge bg-danger text-white">{{ __('dictt.exercise_attempt_wrong') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ __('dictt.exercise_attempt_blank') }}</span>
                                @endif
                            </div>

                            <h2 class="h5 text-dark mb-4">{{ $question->question }}</h2>

                            @if ($imageUrl = $question->privateImageUrl())
                                <img src="{{ $imageUrl }}" class="theme-exercise-media img-fluid rounded mb-4" alt="">
                            @endif

                            <div class="d-flex flex-column gap-2">
                                @foreach ($question->options as $option)
                                    @php
                                        $isCorrect = $correctOption !== null && (int) $option->id === (int) $correctOption->id;
                                        $isSelected = $selectedOption !== null && (int) $option->id === (int) $selectedOption->id;
                                        $optionClass = $isCorrect ? 'is-correct' : ($isSelected ? 'is-selected-wrong' : 'border-light');
                                    @endphp
                                    <div class="theme-exercise-review-option border rounded p-3 {{ $optionClass }}">
                                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                                            <div class="d-flex align-items-start gap-2 text-dark">
                                                <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                                                <span>{{ $option->option_text }}</span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if ($isSelected)
                                                    <span class="badge {{ $isCorrect ? 'bg-success text-white' : 'bg-danger text-white' }}">{{ __('dictt.exercise_attempt_my_answer') }}</span>
                                                @endif
                                                @if ($isCorrect)
                                                    <span class="badge bg-success text-white">{{ __('dictt.exercise_attempt_correct_answer') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($answerStatus === 'blank')
                                <div class="alert alert-warning small py-2 mt-3 mb-0" role="alert">
                                    {{ __('dictt.exercise_attempt_blank_note') }}
                                </div>
                            @elseif ($answer?->answered_at)
                                <p class="small text-muted mt-3 mb-0">
                                    {{ __('dictt.placement_test_answered_at') }} {{ $answer->answered_at->format('d.m.Y H:i:s') }}
                                </p>
                            @endif
                        </article>
                    @empty
                        <div class="alert alert-warning" role="alert">{{ __('dictt.exercise_attempt_no_questions') }}</div>
                    @endforelse
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
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
