<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ $level->code }} {{ __('dictt.placement_test') }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('frontend/js/button-titles.js') }}" defer></script>

    <style>
        body {
            background: #f5f7fb;
        }

        .placement-exam-shell {
            min-height: 100vh;
        }

        .placement-exam-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: 4px solid var(--primary);
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.07);
        }

        .placement-exam-logo {
            height: 48px;
            width: auto;
        }

        .placement-question-progress {
            height: 16px;
        }

        .placement-exam-status-bar {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 1rem;
        }

        .placement-exam-status-bar > :last-child {
            text-align: right;
        }

        @media (max-width: 575.98px) {
            .placement-exam-status-bar {
                grid-template-columns: 1fr;
                gap: 0.35rem;
            }

            .placement-exam-status-bar > :last-child {
                text-align: left;
            }
        }

        .placement-question-progress-segment {
            flex: 1 1 0;
            min-width: 4px;
            border-radius: 999px;
            background: #dce7f0;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            line-height: 1;
        }

        .placement-question-progress-segment.is-answered {
            background: var(--primary);
            color: #ffffff;
        }

        .placement-question-progress-segment.is-current {
            background: #f8d7da;
            color: #842029;
        }

        .placement-question-progress-link {
            display: block;
            flex: 1 1 0;
            min-width: 4px;
        }

        a.placement-question-progress-link {
            cursor: pointer;
        }

        .placement-question-progress-link .placement-question-progress-segment {
            height: 100%;
        }

        .placement-question-card,
        .placement-content-card {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.07);
        }

        .placement-option {
            cursor: pointer;
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }

        .placement-option:hover {
            border-color: var(--primary) !important;
            background: rgba(var(--primary-rgb), 0.04);
        }

        .placement-option input:checked + .placement-option-text {
            color: var(--primary);
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="placement-exam-shell">
        <header class="placement-exam-topbar bg-white">
            <div class="container py-3">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('frontend/images/logo/logo-2.png') }}" class="placement-exam-logo" alt="Aydın Language Academy">
                        <div>
                            <div class="fw-bold text-dark">{{ __('dictt.placement_test') }}</div>
                            <div class="small text-muted">{{ __('dictt.placement_test_answers_autosave_note') }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-sm-end gap-2">
                        <span class="badge bg-primary fs-6">{{ $level->code }}</span>
                    </div>
                </div>

                <div class="placement-exam-status-bar mt-3">
                    <span class="small text-muted">{{ __('dictt.placement_test_answered_count', ['count' => $answeredQuestionCount]) }}</span>
                    <span class="small text-muted" data-placement-elapsed-time
                        data-started-at="{{ $placementTest->started_at?->getTimestamp() }}" aria-live="polite">
                        {{ __('dictt.placement_test_elapsed_calculating') }}
                    </span>
                    <span class="small text-muted">{{ __('dictt.placement_test_no_time_limit') }}</span>
                </div>

                <div class="placement-question-progress d-flex gap-1 mt-3" role="progressbar"
                    aria-label="{{ __('dictt.placement_test_progress_aria') }}" aria-valuenow="{{ $currentQuestion->display_position }}"
                    aria-valuemin="1" aria-valuemax="{{ $questions->count() }}">
                    @foreach ($questions as $question)
                        @if ($question->id === $currentQuestion->id)
                            <span class="placement-question-progress-link" aria-current="step">
                                <span class="placement-question-progress-segment is-current"
                                    title="{{ __('dictt.placement_test_question_n', ['n' => $question->display_position]) }}">{{ $question->display_position }}</span>
                            </span>
                        @else
                            <a class="placement-question-progress-link"
                                href="{{ route('frontend.placement-test.question', [
                                    'placementTest' => $placementTest,
                                    'placementTestLevelQuestion' => $question,
                                ]) }}"
                                aria-label="{{ __('dictt.placement_test_go_to_question', ['n' => $question->display_position]) }}">
                                <span class="placement-question-progress-segment {{ $question->selected_option !== null ? 'is-answered' : 'is-unanswered' }}"
                                    title="{{ __('dictt.placement_test_question_n', ['n' => $question->display_position]) }}">{{ $question->display_position }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </header>

        <main class="container py-4 py-lg-5">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    @if ($contentSnapshot)
                        @php
                            $mediaUrl = $contentSnapshot->type_snapshot === 'text'
                                ? null
                                : route('frontend.placement-test.media', [
                                    'placementTest' => $placementTest,
                                    'placementTestLevelResultContent' => $contentSnapshot,
                                ]);
                        @endphp

                        <section class="placement-content-card bg-white rounded p-4 p-lg-5 mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fa fa-book-open text-primary"></i>
                                <h2 class="h5 mb-0">{{ __('dictt.placement_test_shared_content') }}</h2>
                            </div>

                            @if ($contentSnapshot->type_snapshot === 'text')
                                <div class="text-dark lh-lg">{!! \Mews\Purifier\Facades\Purifier::clean($contentSnapshot->text_content_snapshot, 'quill') !!}</div>
                            @elseif ($contentSnapshot->type_snapshot === 'image')
                                <img src="{{ $mediaUrl }}" class="img-fluid rounded" alt="{{ __('dictt.placement_test_shared_image_alt') }}">
                            @elseif ($contentSnapshot->type_snapshot === 'audio')
                                <audio controls preload="metadata" class="w-100" data-placement-media
                                    data-content-key="{{ $contentSnapshot->id }}">
                                    <source src="{{ $mediaUrl }}">
                                    {{ __('dictt.placement_test_audio_unsupported') }}
                                </audio>
                            @elseif ($contentSnapshot->type_snapshot === 'video')
                                <video controls preload="metadata" class="w-100 rounded" data-placement-media
                                    data-content-key="{{ $contentSnapshot->id }}">
                                    <source src="{{ $mediaUrl }}">
                                    {{ __('dictt.placement_test_video_unsupported') }}
                                </video>
                            @endif
                        </section>
                    @endif

                    <section class="placement-question-card bg-white rounded p-4 p-lg-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-secondary">{{ $currentQuestion->display_position }}</span>
                            <span class="small text-muted">{{ __('dictt.placement_test_question_label') }}</span>
                        </div>

                        <h1 class="h4 text-dark mb-4">{!! nl2br(e($currentQuestion->question_text_snapshot)) !!}</h1>

                        <form id="placement-answer-form" method="POST"
                            action="{{ route('frontend.placement-test.answer', [
                                'placementTest' => $placementTest,
                                'placementTestLevelQuestion' => $currentQuestion,
                            ]) }}"
                            data-placement-answer-form>
                            @csrf
                            @method('PUT')

                            <div class="vstack gap-3">
                                @foreach ($currentQuestion->options_snapshot as $option)
                                    <label class="placement-option border rounded p-3 d-flex align-items-start gap-3 mb-0">
                                        <input type="radio" class="form-check-input mt-1" name="selected_option"
                                            value="{{ $option['position'] }}"
                                            @checked((int) $currentQuestion->selected_option === (int) $option['position'])>
                                        <span class="placement-option-text">{{ $option['text'] }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div id="placement-save-status" class="small text-muted mt-3" role="status" aria-live="polite">
                                @if ($currentQuestion->selected_option !== null)
                                    <i class="fa fa-check-circle text-success"></i> {{ __('dictt.placement_test_answer_saved') }}
                                @else
                                    {{ __('dictt.placement_test_answer_hint') }}
                                @endif
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-4 pt-3 border-top">
                                <div class="d-flex gap-2">
                                    @if ($previousQuestion)
                                        <button type="submit" name="go_to_question" value="{{ $previousQuestion->id }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fa fa-arrow-left me-2"></i>{{ __('dictt.placement_test_previous') }}
                                        </button>
                                    @endif
                                    <button type="submit" name="clear_answer" value="1" class="btn btn-outline-secondary">
                                        {{ __('dictt.placement_test_clear_answer') }}
                                    </button>
                                </div>

                                @if ($nextQuestion)
                                    <button type="submit" name="go_to_question" value="{{ $nextQuestion->id }}" class="btn btn-primary">
                                        {{ __('dictt.placement_test_next') }}<i class="fa fa-arrow-right ms-2"></i>
                                    </button>
                                @else
                                    <button type="submit" name="finish_level" value="1" class="btn btn-success">
                                        {{ __('dictt.placement_test_finish_level') }}<i class="fa fa-check ms-2"></i>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <script>
        (() => {
            const examI18n = {!! json_encode([
                'duration' => __('dictt.placement_test_duration'),
                'minutes' => __('dictt.placement_test_duration_minutes'),
                'seconds' => __('dictt.placement_test_duration_seconds'),
                'saving' => __('dictt.placement_test_saving_answer'),
                'sessionExpired' => __('dictt.placement_test_session_expired'),
                'saveFailed' => __('dictt.placement_test_save_failed'),
                'retryNote' => __('dictt.placement_test_retry_note'),
            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

            const answerForm = document.querySelector('[data-placement-answer-form]');
            const saveStatus = document.getElementById('placement-save-status');

            if (answerForm && saveStatus) {
                let saveQueue = Promise.resolve();

                const setStatus = (message, type = 'muted') => {
                    const icon = type === 'success' ? 'fa-check-circle text-success' : 'fa-info-circle text-muted';
                    saveStatus.innerHTML = `<i class="fa ${icon}"></i> ${message}`;
                };

                answerForm.querySelectorAll('input[name="selected_option"]').forEach((input) => {
                    input.addEventListener('change', () => {
                        const formData = new FormData(answerForm);
                        formData.delete('clear_answer');
                        formData.delete('finish_level');
                        formData.delete('go_to_question');

                        saveQueue = saveQueue
                            .catch(() => undefined)
                            .then(async () => {
                                setStatus(examI18n.saving);

                                const response = await fetch(answerForm.action, {
                                    method: 'POST',
                                    headers: {
                                        Accept: 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    },
                                    body: formData,
                                });

                                if (response.status === 401 || response.status === 419 || response.redirected) {
                                    throw new Error(examI18n.sessionExpired);
                                }

                                const isJsonResponse = response.headers
                                    .get('content-type')
                                    ?.includes('application/json');
                                const payload = isJsonResponse ? await response.json() : {};

                                if (! response.ok) {
                                    throw new Error(payload.message || examI18n.saveFailed);
                                }

                                setStatus(payload.message, 'success');
                            })
                            .catch((error) => {
                                setStatus(`${error.message} ${examI18n.retryNote}`);
                            });
                    });
                });
            }

            document.querySelectorAll('[data-placement-media]').forEach((media) => {
                const storageKey = `placement-test-media-${media.dataset.contentKey}`;

                media.addEventListener('loadedmetadata', () => {
                    try {
                        const savedTime = Number(sessionStorage.getItem(storageKey));

                        if (Number.isFinite(savedTime) && savedTime > 0 && savedTime < media.duration) {
                            media.currentTime = savedTime;
                        }
                    } catch (_) {
                        // Browser storage may be unavailable; playback still works normally.
                    }
                }, { once: true });

                media.addEventListener('timeupdate', () => {
                    try {
                        sessionStorage.setItem(storageKey, String(media.currentTime));
                    } catch (_) {
                        // Browser storage may be unavailable; playback still works normally.
                    }
                });
            });

            const elapsedTime = document.querySelector('[data-placement-elapsed-time]');

            if (elapsedTime) {
                const startedAt = Number(elapsedTime.dataset.startedAt);

                if (Number.isFinite(startedAt) && startedAt > 0) {
                    const updateElapsedTime = () => {
                        const elapsedSeconds = Math.max(0, Math.floor(Date.now() / 1000) - startedAt);
                        const elapsedMinutes = Math.floor(elapsedSeconds / 60);
                        const remainingSeconds = elapsedSeconds % 60;

                        elapsedTime.textContent = `${examI18n.duration} ${elapsedMinutes} ${examI18n.minutes} ${remainingSeconds} ${examI18n.seconds}`;
                    };

                    updateElapsedTime();
                    window.setInterval(updateElapsedTime, 1000);
                }
            }
        })();
    </script>
</body>

</html>
