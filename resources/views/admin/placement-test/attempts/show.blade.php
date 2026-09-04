<x-app-layout>
    <x-slot name="header">{{ __('dictt.placement_test_review') }} #{{ $placementTest->id }}</x-slot>

    @if (session('modalSuccessTitle') && session('modalSuccessContent'))
        <div class="relative bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow mb-6 w-full">
            <div
                class="absolute bottom-[-10px] left-10 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-t-[10px] border-t-green-100">
            </div>
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {!! session('modalSuccessTitle') !!}
                </h2>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-red-600 ml-4" title="{{ __('dictt.close') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 text-sm">
                {!! session('modalSuccessContent') !!}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <a href="{{ route('placement_test_attempts_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                </a>

                @if ($placementTest->status === 'pending_approval')
                    <form id="placement-test-approve" method="POST"
                        action="{{ route('placement_test_attempts_approve', $placementTest) }}">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn btn-sm btn-success"
                            data-action-confirmation
                            data-confirm-form="placement-test-approve"
                            data-confirm-title="{{ __('dictt.approve') }}"
                            data-confirm-content="{{ __('dictt.placement_test_attempt_approve_confirm') }}"
                            data-confirm-action="{{ __('dictt.approve') }}"
                            data-confirm-icon="fa-check"
                            data-confirm-tone="success">
                            <i class="fa fa-check" aria-hidden="true"></i> {{ __('dictt.approve') }}
                        </button>
                    </form>
                @endif
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('dictt.member') }}</dt>
                <dd class="col-sm-9">{{ $placementTest->user?->name ?? ('#' . $placementTest->user_id) }}</dd>
                <dt class="col-sm-3">{{ __('dictt.email') }}</dt>
                <dd class="col-sm-9 text-break">{{ $placementTest->user?->email ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.placement_test_started_at') }}</dt>
                <dd class="col-sm-9">{{ $placementTest->started_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.placement_test_submitted_at') }}</dt>
                <dd class="col-sm-9">{{ $placementTest->submitted_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.placement_test_result_level') }}</dt>
                <dd class="col-sm-9">{{ $placementTest->resultLevel?->code ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.status') }}</dt>
                <dd class="col-sm-9">
                    @if ($placementTest->status === 'pending_approval')
                        <span class="badge text-bg-warning">{{ __('dictt.status_pending') }}</span>
                    @else
                        <span class="badge text-bg-success">{{ __('dictt.status_approved') }}</span>
                    @endif
                </dd>
                @if ($placementTest->status === 'approved')
                    <dt class="col-sm-3">{{ __('dictt.placement_test_approved_by') }}</dt>
                    <dd class="col-sm-9">
                        {{ $placementTest->approver?->name ?? '—' }}
                        @if ($placementTest->approved_at)
                            <span class="text-muted">— {{ $placementTest->approved_at->format('d.m.Y H:i:s') }}</span>
                        @endif
                    </dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="alert alert-info" role="note">
        <i class="fa fa-info-circle" aria-hidden="true"></i> {{ __('dictt.placement_test_review_answer_note') }}
    </div>

    @forelse ($levelResults as $levelResult)
        <section class="card mb-4">
            <div class="card-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                <h2 class="h5 mb-0">{{ __('dictt.level') }} {{ $levelResult->level?->code ?? '—' }}</h2>
                @if ($levelResult->result === 'success')
                    <span class="badge text-bg-success">{{ __('dictt.placement_test_level_success') }}</span>
                @elseif ($levelResult->result === 'unsuccess')
                    <span class="badge text-bg-danger">{{ __('dictt.placement_test_level_unsuccess') }}</span>
                @endif
            </div>
            <div class="card-body">
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
                                : route('placement_test_attempts_media', [
                                    'placementTest' => $placementTest,
                                    'placementTestLevelResultContent' => $contentSnapshot,
                                ]);
                        @endphp

                        <section class="border rounded p-3 p-lg-4 mb-3 bg-light">
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

                    <article class="border rounded p-3 p-lg-4 mb-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge text-bg-secondary">{{ $question->display_position }}</span>
                                <span class="small text-muted">{{ __('dictt.placement_test_question_label') }}</span>
                            </div>
                            @if ($answerStatus === 'correct')
                                <span class="badge text-bg-success">{{ __('dictt.placement_test_answer_correct') }}</span>
                            @elseif ($answerStatus === 'wrong')
                                <span class="badge text-bg-danger">{{ __('dictt.placement_test_answer_wrong') }}</span>
                            @else
                                <span class="badge text-bg-warning">{{ __('dictt.placement_test_answer_blank_label') }}</span>
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
                                            <span class="badge text-bg-secondary">{{ $position }}</span>
                                            <span>{{ $option['text'] }}</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if ($isSelected)
                                                <span class="badge {{ $isCorrect ? 'text-bg-success' : 'text-bg-danger' }}">{{ __('dictt.placement_test_student_answer') }}</span>
                                            @endif
                                            @if ($isCorrect)
                                                <span class="badge text-bg-success">{{ __('dictt.placement_test_correct_answer') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($selectedPosition === null)
                            <div class="alert alert-warning small py-2 mt-3 mb-0" role="alert">
                                {{ __('dictt.placement_test_answer_blank') }}
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
            </div>
        </section>
    @empty
        <div class="alert alert-warning" role="alert">{{ __('dictt.placement_test_attempt_questions_empty') }}</div>
    @endforelse

    <x-action-confirmation-modal />
</x-app-layout>
