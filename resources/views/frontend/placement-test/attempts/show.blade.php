<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.placement_test_review') }} | {{ __('dictt.ala') }}</title>
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
</head>

<body>
    @include('frontend.partials.header')

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 wow fadeIn" data-wow-delay="0.1s">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
                        <div>
                            <h1 class="mb-1">{{ __('dictt.placement_test_my_review_title') }}</h1>
                            <p class="mb-0 text-muted">{{ __('dictt.placement_test_history_read_only_note') }}</p>
                        </div>
                        <a href="{{ route('frontend.placement-test.attempts') }}" class="btn btn-outline-primary align-self-start align-self-sm-center">
                            <i class="fa fa-arrow-left me-2" aria-hidden="true"></i>{{ __('dictt.placement_test_my_attempts') }}
                        </a>
                    </div>

                    <div class="bg-light rounded p-3 p-md-4 mb-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ __('dictt.placement_test_started_at') }}</dt>
                            <dd class="col-sm-8">{{ $placementTest->started_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
                            <dt class="col-sm-4">{{ __('dictt.placement_test_submitted_at') }}</dt>
                            <dd class="col-sm-8">{{ $placementTest->submitted_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
                            <dt class="col-sm-4">{{ __('dictt.placement_test_history_approved_at') }}</dt>
                            <dd class="col-sm-8">{{ $placementTest->approved_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
                            <dt class="col-sm-4">{{ __('dictt.placement_test_result_level') }}</dt>
                            <dd class="col-sm-8">{{ $placementTest->resultLevel?->code ?? '—' }}</dd>
                        </dl>
                    </div>

                    <div class="alert alert-info" role="note">
                        <i class="fa fa-info-circle" aria-hidden="true"></i> {{ __('dictt.placement_test_my_review_answer_note') }}
                    </div>

                    @forelse ($levelResults as $levelResult)
                        <section class="bg-light rounded p-3 p-md-4 mb-4">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-4">
                                <h2 class="h4 mb-0">{{ __('dictt.level') }} {{ $levelResult->level?->code ?? '—' }}</h2>
                                @if ($levelResult->result === 'success')
                                    <span class="badge bg-success text-white">{{ __('dictt.placement_test_level_success') }}</span>
                                @elseif ($levelResult->result === 'unsuccess')
                                    <span class="badge bg-danger text-white">{{ __('dictt.placement_test_level_unsuccess') }}</span>
                                @endif
                            </div>

                            @php
                                $shownContentIds = [];
                            @endphp

                            @forelse ($levelResult->levelQuestions as $question)
                                @php
                                    $contentSnapshot = $question->contentSnapshot;
                                @endphp

                                @if ($contentSnapshot && ! in_array($contentSnapshot->id, $shownContentIds, true))
                                    @php
                                        $shownContentIds[] = $contentSnapshot->id;
                                        $mediaUrl = $contentSnapshot->type_snapshot === 'text'
                                            ? null
                                            : route('frontend.placement-test.attempts.media', [
                                                'placementTest' => $placementTest,
                                                'placementTestLevelResultContent' => $contentSnapshot,
                                            ]);
                                    @endphp

                                    <section class="border rounded p-3 p-lg-4 mb-3 bg-white">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <i class="fa fa-book-open text-primary" aria-hidden="true"></i>
                                            <h3 class="h6 mb-0">{{ __('dictt.placement_test_shared_content') }}</h3>
                                        </div>

                                        @if ($contentSnapshot->type_snapshot === 'text')
                                            <div class="text-dark lh-lg">{!! nl2br(e($contentSnapshot->text_content_snapshot)) !!}</div>
                                        @elseif ($contentSnapshot->type_snapshot === 'image')
                                            <img src="{{ $mediaUrl }}" class="img-fluid rounded" alt="{{ __('dictt.placement_test_shared_image_alt') }}">
                                        @elseif ($contentSnapshot->type_snapshot === 'audio')
                                            <audio controls preload="metadata" class="w-100">
                                                <source src="{{ $mediaUrl }}">
                                                {{ __('dictt.placement_test_audio_unsupported') }}
                                            </audio>
                                        @elseif ($contentSnapshot->type_snapshot === 'video')
                                            <video controls preload="metadata" class="w-100 rounded">
                                                <source src="{{ $mediaUrl }}">
                                                {{ __('dictt.placement_test_video_unsupported') }}
                                            </video>
                                        @endif
                                    </section>
                                @endif

                                @php
                                    $options = collect($question->options_snapshot ?? [])
                                        ->filter(static fn ($option): bool => is_array($option)
                                            && array_key_exists('position', $option)
                                            && array_key_exists('text', $option))
                                        ->sortBy(static fn (array $option): int => (int) $option['position'])
                                        ->values();
                                    $correctPosition = (int) $question->correct_option_snapshot;
                                    $selectedPosition = $question->selected_option === null ? null : (int) $question->selected_option;
                                    $hasCorrectOption = $options->contains(static fn (array $option): bool => (int) $option['position'] === $correctPosition);
                                    $hasSelectedOption = $selectedPosition === null
                                        || $options->contains(static fn (array $option): bool => (int) $option['position'] === $selectedPosition);
                                    $answerStatus = match (true) {
                                        $selectedPosition === null => 'blank',
                                        $selectedPosition === $correctPosition => 'correct',
                                        default => 'wrong',
                                    };
                                @endphp

                                <article class="border rounded p-3 p-lg-4 mb-3 bg-white">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-2 mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary text-white">{{ $question->display_position }}</span>
                                            <span class="small text-muted">{{ __('dictt.placement_test_question_label') }}</span>
                                        </div>
                                        @if ($answerStatus === 'correct')
                                            <span class="badge bg-success text-white">{{ __('dictt.placement_test_answer_correct') }}</span>
                                        @elseif ($answerStatus === 'wrong')
                                            <span class="badge bg-danger text-white">{{ __('dictt.placement_test_answer_wrong') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ __('dictt.placement_test_answer_blank_label') }}</span>
                                        @endif
                                    </div>

                                    <div class="h6 text-dark mb-3">{!! nl2br(e($question->question_text_snapshot)) !!}</div>

                                    @if (! $hasCorrectOption || ! $hasSelectedOption)
                                        <div class="alert alert-danger small py-2" role="alert">{{ __('dictt.placement_test_snapshot_invalid') }}</div>
                                    @endif

                                    <div class="vstack gap-2">
                                        @foreach ($options as $option)
                                            @php
                                                $position = (int) $option['position'];
                                                $isCorrect = $position === $correctPosition;
                                                $isSelected = $selectedPosition !== null && $position === $selectedPosition;
                                                $optionClass = $isCorrect
                                                    ? 'border-success'
                                                    : ($isSelected ? 'border-danger' : 'border-light');
                                                $optionStyle = $isCorrect
                                                    ? 'background-color: rgba(25, 135, 84, .10);'
                                                    : ($isSelected ? 'background-color: rgba(220, 53, 69, .10);' : '');
                                            @endphp
                                            <div class="border rounded p-3 {{ $optionClass }}" @if ($optionStyle) style="{{ $optionStyle }}" @endif>
                                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                                                    <div class="d-flex align-items-start gap-2 text-dark">
                                                        <span class="badge bg-secondary text-white">{{ $position }}</span>
                                                        <span>{{ $option['text'] }}</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @if ($isSelected)
                                                            <span class="badge {{ $isCorrect ? 'bg-success text-white' : 'bg-danger text-white' }}">{{ __('dictt.placement_test_my_answer') }}</span>
                                                        @endif
                                                        @if ($isCorrect)
                                                            <span class="badge bg-success text-white">{{ __('dictt.placement_test_correct_answer') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($selectedPosition === null)
                                        <div class="alert alert-warning small py-2 mt-3 mb-0" role="alert">
                                            {{ __('dictt.placement_test_my_answer_blank') }}
                                        </div>
                                    @elseif ($question->answered_at)
                                        <p class="small text-muted mt-3 mb-0">
                                            {{ __('dictt.placement_test_answered_at') }} {{ $question->answered_at->format('d.m.Y H:i:s') }}
                                        </p>
                                    @endif
                                </article>
                            @empty
                                <p class="text-muted mb-0">{{ __('dictt.placement_test_attempt_questions_empty') }}</p>
                            @endforelse
                        </section>
                    @empty
                        <div class="alert alert-warning" role="alert">{{ __('dictt.placement_test_attempt_questions_empty') }}</div>
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
    <script src="{{ asset('frontend/vendor/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
</body>

</html>
