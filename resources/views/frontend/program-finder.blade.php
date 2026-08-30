@php
    $formValue = static function (string $name) use ($answers): string {
        $value = old($name, $answers[$name] ?? '');

        return is_string($value) ? $value : '';
    };
    $learnerOptions = [
        'student' => [
            'title' => 'dictt.program_finder_learner_student',
            'description' => 'dictt.program_finder_learner_student_description',
            'icon' => 'fa-user-graduate',
        ],
        'adult' => [
            'title' => 'dictt.program_finder_learner_adult',
            'description' => 'dictt.program_finder_learner_adult_description',
            'icon' => 'fa-user',
        ],
    ];
    $goalOptions = [
        'school_support' => [
            'title' => 'dictt.program_finder_goal_school_support',
            'description' => 'dictt.program_finder_goal_school_support_description',
            'icon' => 'fa-school',
            'for' => 'student',
        ],
        'general' => [
            'title' => 'dictt.program_finder_goal_general',
            'description' => 'dictt.program_finder_goal_general_description',
            'icon' => 'fa-comments',
            'for' => 'student adult',
        ],
        'speaking' => [
            'title' => 'dictt.program_finder_goal_speaking',
            'description' => 'dictt.program_finder_goal_speaking_description',
            'icon' => 'fa-comment-dots',
            'for' => 'student adult',
        ],
        'exam' => [
            'title' => 'dictt.program_finder_goal_exam',
            'description' => 'dictt.program_finder_goal_exam_description',
            'icon' => 'fa-clipboard-check',
            'for' => 'student adult',
        ],
    ];
    $schoolStageOptions = [
        'preschool' => ['title' => 'dictt.preschool', 'icon' => 'fa-child'],
        'primary' => ['title' => 'dictt.primary_school', 'icon' => 'fa-pencil-alt'],
        'middle' => ['title' => 'dictt.middle_school', 'icon' => 'fa-book-open'],
        'high' => ['title' => 'dictt.high_school', 'icon' => 'fa-graduation-cap'],
    ];
    $examOptions = [
        'ielts' => ['title' => 'dictt.ielts_prep', 'icon' => 'fa-plane-departure'],
        'yks_dil' => ['title' => 'dictt.yks_dil_prep', 'icon' => 'fa-university'],
        'yds_yokdil' => ['title' => 'dictt.yds_yokdil', 'icon' => 'fa-book'],
        'toefl' => ['title' => 'dictt.toefl', 'icon' => 'fa-globe'],
        'pte_academic' => ['title' => 'dictt.pte_academic', 'icon' => 'fa-laptop'],
        'test_of_english' => ['title' => 'dictt.test_of_english', 'icon' => 'fa-check-circle'],
        'sat' => ['title' => 'dictt.sat', 'icon' => 'fa-calculator'],
    ];
    $levelOptions = [
        'unknown' => 'dictt.program_finder_level_unknown',
        'A1' => 'dictt.program_finder_level_a1',
        'A2' => 'dictt.program_finder_level_a2',
        'B1' => 'dictt.program_finder_level_b1',
        'B2' => 'dictt.program_finder_level_b2',
        'C1' => 'dictt.program_finder_level_c1',
        'C2' => 'dictt.program_finder_level_c2',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('dictt.program_finder_title') }} | {{ __('dictt.ala') }}</title>
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
    <link href="{{ asset('frontend/css/style.css') }}?v=20260830-program-finder-3" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <main class="container-xxl py-5 program-finder-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <section class="program-finder-hero rounded p-4 p-lg-5 text-center wow fadeIn" data-wow-delay="0.1s">
                        <span class="program-finder-eyebrow"><i class="fa fa-compass me-2" aria-hidden="true"></i>{{ __('dictt.ourcourses') }}</span>
                        <h1 class="mb-3">{{ __('dictt.program_finder_title') }}</h1>
                        <p class="mb-0 mx-auto">{{ __('dictt.program_finder_intro') }}</p>
                    </section>

                    @if ($recommendation)
                        <section class="program-finder-card bg-light rounded p-4 p-lg-5 mt-4 wow fadeIn" data-wow-delay="0.15s">
                            <div class="text-center mx-auto mb-4" style="max-width: 650px;">
                                <span class="program-finder-eyebrow"><i class="fa fa-check-circle me-2" aria-hidden="true"></i>{{ __('dictt.program_finder_result_eyebrow') }}</span>
                                <h2 class="h1 mt-2 mb-3">{{ __('dictt.program_finder_result_title') }}</h2>
                                <p class="mb-0">{{ __('dictt.program_finder_result_note') }}</p>
                            </div>

                            <div class="program-finder-level-summary rounded p-3 p-lg-4 mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="program-finder-level-summary__icon"><i class="fa fa-signal" aria-hidden="true"></i></span>
                                    <div>
                                        <h3 class="h6 mb-1">{{ __('dictt.program_finder_level_summary_title') }}</h3>
                                        @if ($recommendation['level']['source'] === 'placement_test')
                                            <p class="mb-0">{{ __('dictt.program_finder_level_summary_placement', ['level' => $recommendation['level']['code']]) }}</p>
                                        @elseif ($recommendation['level']['code'] === 'unknown')
                                            <p class="mb-0">{{ __('dictt.program_finder_level_summary_unknown') }}</p>
                                        @else
                                            <p class="mb-0">{{ __('dictt.program_finder_level_summary_self', ['level' => $recommendation['level']['code']]) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 justify-content-center">
                                <div class="col-lg-{{ $recommendation['alternative'] ? '6' : '8' }}">
                                    <article class="program-finder-result-card bg-white rounded h-100 p-4">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="program-finder-result-card__icon d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-light rounded-circle">
                                                <i class="fa {{ $recommendation['primary']['icon'] }} text-primary fs-4" aria-hidden="true"></i>
                                            </div>
                                            <div>
                                                <span class="text-primary fw-bold small text-uppercase">{{ __('dictt.program_finder_result_primary') }}</span>
                                                <h3 class="h4 mt-1">{{ __($recommendation['primary']['title_key']) }}</h3>
                                                <p class="mb-4">{{ __($recommendation['primary']['description_key']) }}</p>
                                                <a href="{{ route($recommendation['primary']['route']) }}" class="btn btn-outline-primary fw-bold">
                                                    {{ __('dictt.program_finder_view_program') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                @if ($recommendation['alternative'])
                                    <div class="col-lg-6">
                                        <article class="program-finder-result-card bg-white rounded h-100 p-4">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="program-finder-result-card__icon d-inline-flex flex-shrink-0 align-items-center justify-content-center bg-light rounded-circle">
                                                    <i class="fa {{ $recommendation['alternative']['icon'] }} text-primary fs-4" aria-hidden="true"></i>
                                                </div>
                                                <div>
                                                    <span class="text-primary fw-bold small text-uppercase">{{ __('dictt.program_finder_result_alternative') }}</span>
                                                    <h3 class="h4 mt-1">{{ __($recommendation['alternative']['title_key']) }}</h3>
                                                    <p class="mb-4">{{ __($recommendation['alternative']['description_key']) }}</p>
                                                    <a href="{{ route($recommendation['alternative']['route']) }}" class="btn btn-outline-primary fw-bold">
                                                        {{ __('dictt.program_finder_view_program') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endif
                            </div>

                            <section class="program-finder-consultation bg-white rounded p-4 p-lg-5 mt-4 text-center">
                                <span class="program-finder-eyebrow"><i class="fa fa-comments me-2" aria-hidden="true"></i>{{ __('dictt.program_finder_consultation_eyebrow') }}</span>
                                <h3 class="h4 mt-2">{{ __('dictt.program_finder_consultation_title') }}</h3>
                                <p class="mx-auto mb-4" style="max-width: 650px;">{{ __('dictt.program_finder_consultation_text') }}</p>
                                <div class="row g-3 justify-content-center">
                                    @foreach ($branches as $branch)
                                        <div class="col-md-4">
                                            <a href="{{ route('frontend.contact', ['branch' => $branch['slug']]) }}" class="btn btn-outline-primary w-100">
                                                <i class="fa fa-comment-dots me-2" aria-hidden="true"></i>{{ __('dictt.program_finder_contact_branch', ['branch' => __($branch['label_key'])]) }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </section>

                            <div class="text-center mt-4">
                                <a href="{{ route('frontend.program-finder') }}" class="btn btn-link text-decoration-none">
                                    <i class="fa fa-redo me-2" aria-hidden="true"></i>{{ __('dictt.program_finder_restart') }}
                                </a>
                            </div>
                        </section>
                    @else
                        @if ($placementLevelCode)
                            <div class="program-finder-info rounded p-3 p-lg-4 mt-4 wow fadeIn" data-wow-delay="0.15s">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="fa fa-clipboard-check text-primary fs-4 mt-1" aria-hidden="true"></i>
                                    <p class="mb-0">{{ __('dictt.program_finder_placement_available', ['level' => $placementLevelCode]) }}</p>
                                </div>
                            </div>
                        @elseif (auth()->check())
                            <div class="program-finder-info rounded p-3 p-lg-4 mt-4 wow fadeIn" data-wow-delay="0.15s">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="fa fa-info-circle text-primary fs-4 mt-1" aria-hidden="true"></i>
                                    <p class="mb-0">{{ __('dictt.program_finder_no_placement_result') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="program-finder-info rounded p-3 p-lg-4 mt-4 wow fadeIn" data-wow-delay="0.15s">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="fa fa-info-circle text-primary fs-4 mt-1" aria-hidden="true"></i>
                                    <p class="mb-0">{!! __('dictt.program_finder_login_note', ['link' => '<a href="' . route('frontend.login', ['return' => 'frontend.program-finder']) . '">' . __('dictt.login') . '</a>']) !!}</p>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mt-4 mb-0" role="alert">
                                <strong>{{ __('dictt.errors') }}</strong>
                                <ul class="mb-0 mt-2 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('frontend.program-finder.recommend') }}" class="program-finder-card bg-light rounded p-4 p-lg-5 mt-4 wow fadeIn" data-wow-delay="0.2s" data-program-finder data-required-message="{{ __('dictt.program_finder_client_required') }}" data-step-template="{{ __('dictt.program_finder_step_progress', ['current' => ':current', 'total' => ':total']) }}">
                            @csrf

                            <div class="program-finder-progress-wrap mb-4" aria-live="polite">
                                <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                                    <span class="fw-bold" data-program-finder-step-label>{{ __('dictt.program_finder_step_progress', ['current' => 1, 'total' => $placementLevelCode ? 3 : 4]) }}</span>
                                    <span class="small text-muted">{{ __('dictt.program_finder_progress_label') }}</span>
                                </div>
                                <div class="progress program-finder-progress" role="progressbar" aria-label="{{ __('dictt.program_finder_progress_label') }}" aria-valuemin="1" aria-valuemax="{{ $placementLevelCode ? 3 : 4 }}" aria-valuenow="1">
                                    <div class="progress-bar" data-program-finder-progress-bar style="width: {{ $placementLevelCode ? '33.333' : '25' }}%"></div>
                                </div>
                            </div>

                            <fieldset class="border-0 p-0 m-0 program-finder-step" data-program-finder-step data-step-key="learner">
                                <legend class="h3 mb-2">{{ __('dictt.program_finder_question_learner') }}</legend>
                                <p class="text-muted mb-4">{{ __('dictt.program_finder_question_learner_help') }}</p>
                                <div class="row g-3">
                                    @foreach ($learnerOptions as $value => $option)
                                        <div class="col-md-6">
                                            <label class="program-finder-choice border rounded p-3 d-flex align-items-start gap-3 h-100">
                                                <input type="radio" class="form-check-input mt-1" name="learner_type" value="{{ $value }}" @checked($formValue('learner_type') === $value)>
                                                <span class="program-finder-choice__body">
                                                    <span class="program-finder-choice__icon"><i class="fa {{ $option['icon'] }}" aria-hidden="true"></i></span>
                                                    <span class="program-finder-choice__content">
                                                        <strong>{{ __($option['title']) }}</strong>
                                                        <span>{{ __($option['description']) }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('learner_type')
                                    <p class="text-danger small mt-3 mb-0">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <fieldset class="border-0 p-0 m-0 program-finder-step" data-program-finder-step data-step-key="goal">
                                <legend class="h3 mb-2">{{ __('dictt.program_finder_question_goal') }}</legend>
                                <p class="text-muted mb-4">{{ __('dictt.program_finder_question_goal_help') }}</p>
                                <div class="row g-3">
                                    @foreach ($goalOptions as $value => $option)
                                        <div class="col-md-6" data-program-finder-goal data-program-finder-for="{{ $option['for'] }}">
                                            <label class="program-finder-choice border rounded p-3 d-flex align-items-start gap-3 h-100">
                                                <input type="radio" class="form-check-input mt-1" name="goal" value="{{ $value }}" @checked($formValue('goal') === $value)>
                                                <span class="program-finder-choice__body">
                                                    <span class="program-finder-choice__icon"><i class="fa {{ $option['icon'] }}" aria-hidden="true"></i></span>
                                                    <span class="program-finder-choice__content">
                                                        <strong>{{ __($option['title']) }}</strong>
                                                        <span>{{ __($option['description']) }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('goal')
                                    <p class="text-danger small mt-3 mb-0">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <fieldset class="border-0 p-0 m-0 program-finder-step" data-program-finder-step data-step-key="details">
                                <legend class="h3 mb-2">{{ __('dictt.program_finder_question_details') }}</legend>
                                <p class="text-muted mb-4">{{ __('dictt.program_finder_question_details_help') }}</p>

                                <div data-program-finder-condition="student">
                                    <h2 class="h5 mb-3">{{ __('dictt.program_finder_question_school_stage') }}</h2>
                                    <div class="row g-3">
                                        @foreach ($schoolStageOptions as $value => $option)
                                            <div class="col-md-6">
                                                <label class="program-finder-choice border rounded p-3 d-flex align-items-start gap-3 h-100">
                                                    <input type="radio" class="form-check-input mt-1" name="school_stage" value="{{ $value }}" @checked($formValue('school_stage') === $value)>
                                                    <span class="program-finder-choice__body">
                                                        <span class="program-finder-choice__icon"><i class="fa {{ $option['icon'] }}" aria-hidden="true"></i></span>
                                                        <span class="program-finder-choice__content"><strong>{{ __($option['title']) }}</strong></span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('school_stage')
                                        <p class="text-danger small mt-3 mb-0">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-4" data-program-finder-condition-goal="exam">
                                    <h2 class="h5 mb-3">{{ __('dictt.program_finder_question_exam') }}</h2>
                                    <div class="row g-3">
                                        @foreach ($examOptions as $value => $option)
                                            <div class="col-md-6">
                                                <label class="program-finder-choice border rounded p-3 d-flex align-items-start gap-3 h-100">
                                                    <input type="radio" class="form-check-input mt-1" name="exam" value="{{ $value }}" @checked($formValue('exam') === $value)>
                                                    <span class="program-finder-choice__body">
                                                        <span class="program-finder-choice__icon"><i class="fa {{ $option['icon'] }}" aria-hidden="true"></i></span>
                                                        <span class="program-finder-choice__content"><strong>{{ __($option['title']) }}</strong></span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('exam')
                                        <p class="text-danger small mt-3 mb-0">{{ $message }}</p>
                                    @enderror
                                </div>
                            </fieldset>

                            @unless ($placementLevelCode)
                                <fieldset class="border-0 p-0 m-0 program-finder-step" data-program-finder-step data-step-key="level">
                                    <legend class="h3 mb-2">{{ __('dictt.program_finder_question_level') }}</legend>
                                    <p class="text-muted mb-4">{{ __('dictt.program_finder_question_level_help') }}</p>
                                    <div class="row g-3">
                                        @foreach ($levelOptions as $value => $label)
                                            <div class="col-md-6 col-lg-4">
                                                <label class="program-finder-choice border rounded p-3 d-flex align-items-start gap-3 h-100">
                                                    <input type="radio" class="form-check-input mt-1" name="self_level" value="{{ $value }}" @checked($formValue('self_level') === $value)>
                                                    <span class="program-finder-choice__body">
                                                        <span class="program-finder-choice__content"><strong>{{ __($label) }}</strong></span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('self_level')
                                        <p class="text-danger small mt-3 mb-0">{{ $message }}</p>
                                    @enderror
                                </fieldset>
                            @endunless

                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-4 pt-4 border-top" data-program-finder-controls>
                                <button type="button" class="btn btn-outline-primary" data-program-finder-back hidden>
                                    <i class="fa fa-arrow-left me-2" aria-hidden="true"></i>{{ __('dictt.program_finder_previous') }}
                                </button>
                                <div class="d-flex flex-column flex-sm-row gap-2 ms-sm-auto">
                                    <button type="button" class="btn btn-primary" data-program-finder-next hidden>
                                        {{ __('dictt.program_finder_next') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
                                    </button>
                                    <button type="submit" class="btn btn-primary" data-program-finder-submit>
                                        {{ __('dictt.program_finder_show_result') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="program-finder-client-error text-danger small mt-3 mb-0" data-program-finder-client-error role="alert" hidden></p>
                            <p class="small text-muted mt-3 mb-0"><i class="fa fa-shield-alt me-2" aria-hidden="true"></i>{{ __('dictt.program_finder_privacy_note') }}</p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </main>

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
    <script src="{{ asset('frontend/js/program-finder.js') }}?v=1"></script>
</body>

</html>
