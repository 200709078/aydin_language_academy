<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>{{ $level->code }} Seviye Tespit Sınavı | Aydın Language Academy</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ asset('ALA-FRONTEND/TEMPLATE') }}/">

    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

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
            height: 8px;
        }

        .placement-question-progress-segment {
            flex: 1 1 0;
            min-width: 4px;
            border-radius: 999px;
            background: #dce7f0;
        }

        .placement-question-progress-segment.is-current {
            background: #045aa2;
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
            background: rgba(4, 90, 162, 0.04);
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
                        <img src="{{ asset('frontend/images/logo-2.png') }}" class="placement-exam-logo" alt="Aydın Language Academy">
                        <div>
                            <div class="fw-bold text-dark">Seviye Tespit Sınavı</div>
                            <div class="small text-muted">Cevaplarınız seçtiğiniz anda kaydedilir.</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-sm-end gap-2">
                        <span class="badge bg-primary fs-6">{{ $level->code }}</span>
                        <span class="small text-muted">Soru {{ $currentQuestion->display_position }} / {{ $questions->count() }}</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-3 mt-3">
                    <span class="small text-muted">{{ $answeredQuestionCount }} soru cevaplandı</span>
                    <span class="small text-muted" data-placement-elapsed-time
                        data-started-at="{{ $placementTest->started_at?->getTimestamp() }}" aria-live="polite">
                        Sınavda Geçen Süre: hesaplanıyor...
                    </span>
                    <span class="small text-muted">Bu sınavda süre sınırlaması yoktur.</span>
                </div>

                <div class="placement-question-progress d-flex gap-1 mt-3" role="progressbar"
                    aria-label="Soru ilerlemesi" aria-valuenow="{{ $currentQuestion->display_position }}"
                    aria-valuemin="1" aria-valuemax="{{ $questions->count() }}">
                    @foreach ($questions as $question)
                        <span class="placement-question-progress-segment {{ $question->id === $currentQuestion->id ? 'is-current' : '' }}"
                            title="Soru {{ $question->display_position }}"></span>
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
                                <h2 class="h5 mb-0">Ortak İçerik</h2>
                            </div>

                            @if ($contentSnapshot->type_snapshot === 'text')
                                <div class="text-dark lh-lg">{!! nl2br(e($contentSnapshot->text_content_snapshot)) !!}</div>
                            @elseif ($contentSnapshot->type_snapshot === 'image')
                                <img src="{{ $mediaUrl }}" class="img-fluid rounded" alt="Soruya ait ortak görsel">
                            @elseif ($contentSnapshot->type_snapshot === 'audio')
                                <audio controls preload="metadata" class="w-100" data-placement-media
                                    data-content-key="{{ $contentSnapshot->id }}">
                                    <source src="{{ $mediaUrl }}">
                                    Tarayıcınız ses oynatmayı desteklemiyor.
                                </audio>
                            @elseif ($contentSnapshot->type_snapshot === 'video')
                                <video controls preload="metadata" class="w-100 rounded" data-placement-media
                                    data-content-key="{{ $contentSnapshot->id }}">
                                    <source src="{{ $mediaUrl }}">
                                    Tarayıcınız video oynatmayı desteklemiyor.
                                </video>
                            @endif
                        </section>
                    @endif

                    <section class="placement-question-card bg-white rounded p-4 p-lg-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-secondary">{{ $currentQuestion->display_position }}</span>
                            <span class="small text-muted">Soru</span>
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
                                    <i class="fa fa-check-circle text-success"></i> Cevabınız kaydedildi.
                                @else
                                    Bir şık seçebilir veya soruyu boş bırakabilirsiniz.
                                @endif
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-4 pt-3 border-top">
                                <div class="d-flex gap-2">
                                    @if ($previousQuestion)
                                        <button type="submit" name="go_to_question" value="{{ $previousQuestion->id }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fa fa-arrow-left me-2"></i>Önceki
                                        </button>
                                    @endif
                                    <button type="submit" name="clear_answer" value="1" class="btn btn-outline-secondary">
                                        Cevabı Temizle
                                    </button>
                                </div>

                                @if ($nextQuestion)
                                    <button type="submit" name="go_to_question" value="{{ $nextQuestion->id }}" class="btn btn-primary">
                                        Sonraki<i class="fa fa-arrow-right ms-2"></i>
                                    </button>
                                @else
                                    <button type="submit" name="finish_level" value="1" class="btn btn-success">
                                        Seviyeyi Tamamla<i class="fa fa-check ms-2"></i>
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
                                setStatus('Cevabınız kaydediliyor...');

                                const response = await fetch(answerForm.action, {
                                    method: 'POST',
                                    headers: {
                                        Accept: 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    },
                                    body: formData,
                                });

                                if (response.status === 401 || response.status === 419 || response.redirected) {
                                    throw new Error('Oturumunuz sona erdi. Sayfayı yenileyip tekrar giriş yapın.');
                                }

                                const isJsonResponse = response.headers
                                    .get('content-type')
                                    ?.includes('application/json');
                                const payload = isJsonResponse ? await response.json() : {};

                                if (! response.ok) {
                                    throw new Error(payload.message || 'Cevabınız kaydedilemedi.');
                                }

                                setStatus(payload.message, 'success');
                            })
                            .catch((error) => {
                                setStatus(`${error.message} Sonraki veya önceki düğmesiyle tekrar deneyin.`);
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

                        elapsedTime.textContent = `Sınavda Geçen Süre: ${elapsedMinutes} dakika ${remainingSeconds} saniye`;
                    };

                    updateElapsedTime();
                    window.setInterval(updateElapsedTime, 1000);
                }
            }
        })();
    </script>
</body>

</html>
