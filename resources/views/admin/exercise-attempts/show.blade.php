<x-app-layout>
    <x-slot name="header">{{ __('dictt.exercise_attempt_review') }} #{{ $exerciseAttempt->id }}</x-slot>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <a href="{{ route('admin.exercise-attempts.index') }}" class="btn btn-sm btn-secondary align-self-md-start">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back') }}
                </a>
                <div>
                    @if ($exerciseAttempt->status === 'in_progress')
                        <span class="badge text-bg-primary">{{ __('dictt.exercise_attempt_status_in_progress') }}</span>
                    @else
                        <span class="badge text-bg-success">{{ __('dictt.exercise_attempt_status_completed') }}</span>
                    @endif
                </div>
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('dictt.member') }}</dt>
                <dd class="col-sm-9">{{ $exerciseAttempt->user?->name ?? ($exerciseAttempt->user_id ? '#' . $exerciseAttempt->user_id : '—') }}</dd>
                <dt class="col-sm-3">{{ __('dictt.email') }}</dt>
                <dd class="col-sm-9">{{ $exerciseAttempt->user?->email ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.theme') }}</dt>
                <dd class="col-sm-9">{{ $theme?->name ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.exercise') }}</dt>
                <dd class="col-sm-9">{{ $exercise->qtext ?: $exercise->title }}</dd>
                <dt class="col-sm-3">{{ __('dictt.exercise_attempt_started_at') }}</dt>
                <dd class="col-sm-9">{{ $exerciseAttempt->started_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
                <dt class="col-sm-3">{{ __('dictt.exercise_attempt_completed_at') }}</dt>
                <dd class="col-sm-9">{{ $exerciseAttempt->completed_at?->format('d.m.Y H:i:s') ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <section class="card mb-4">
        <div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="small text-muted">{{ __('dictt.questions') }}</div>
                    <div class="fw-semibold">{{ $summary['total'] }}</div>
                </div>
                <div class="col-4 col-md-3">
                    <div class="small text-muted">{{ __('dictt.exercise_attempt_correct') }}</div>
                    <div class="fw-semibold text-success">{{ $summary['correct'] }}</div>
                </div>
                <div class="col-4 col-md-3">
                    <div class="small text-muted">{{ __('dictt.exercise_attempt_wrong') }}</div>
                    <div class="fw-semibold text-danger">{{ $summary['wrong'] }}</div>
                </div>
                <div class="col-4 col-md-3">
                    <div class="small text-muted">{{ __('dictt.exercise_attempt_blank') }}</div>
                    <div class="fw-semibold text-warning">{{ $summary['blank'] }}</div>
                </div>
            </div>
        </div>
    </section>

    <div class="alert alert-info" role="note">
        <i class="fa fa-info-circle" aria-hidden="true"></i> {{ __('dictt.exercise_attempt_read_only_note') }}
    </div>
    <div class="alert alert-secondary" role="note">
        <i class="fa fa-check-circle" aria-hidden="true"></i> {{ __('dictt.placement_test_review_answer_note') }}
    </div>

    @php
        $answersByQuestion = $exerciseAttempt->answers->keyBy('question_id');
    @endphp

    @forelse ($exercise->questions as $question)
        @php
            $answer = $answersByQuestion->get($question->id);
            $selectedOption = $answer?->selectedOption;
            $selectedOption = $selectedOption && (int) $selectedOption->question_id === (int) $question->id
                ? $selectedOption
                : null;
            $correctOption = $question->options->firstWhere('is_correct', true);
            $answerStatus = match (true) {
                $selectedOption === null => 'blank',
                $correctOption !== null && (int) $selectedOption->id === (int) $correctOption->id => 'correct',
                default => 'wrong',
            };
        @endphp
        <article class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-bg-secondary">{{ $loop->iteration }}</span>
                        <span class="small text-muted">{{ __('dictt.placement_test_question_label') }}</span>
                    </div>
                    @if ($answerStatus === 'correct')
                        <span class="badge text-bg-success">{{ __('dictt.exercise_attempt_correct') }}</span>
                    @elseif ($answerStatus === 'wrong')
                        <span class="badge text-bg-danger">{{ __('dictt.exercise_attempt_wrong') }}</span>
                    @else
                        <span class="badge text-bg-warning">{{ __('dictt.exercise_attempt_blank') }}</span>
                    @endif
                </div>

                <div class="h6 text-dark mb-3">{{ $question->question }}</div>

                @if ($question->image)
                    <img src="{{ asset('photos/' . $question->image) }}" class="img-fluid rounded mb-3" alt="">
                @endif

                <div class="vstack gap-2">
                    @foreach ($question->options as $option)
                        @php
                            $isCorrect = $correctOption !== null && (int) $option->id === (int) $correctOption->id;
                            $isSelected = $selectedOption !== null && (int) $option->id === (int) $selectedOption->id;
                            $optionClass = $isCorrect ? 'border-success' : ($isSelected ? 'border-danger' : 'border-light');
                            $optionStyle = $isCorrect
                                ? 'background-color: rgba(25, 135, 84, .10);'
                                : ($isSelected ? 'background-color: rgba(220, 53, 69, .10);' : '');
                        @endphp
                        <div class="border rounded p-3 {{ $optionClass }}" @if ($optionStyle) style="{{ $optionStyle }}" @endif>
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                                <div class="d-flex align-items-start gap-2 text-dark">
                                    <span class="badge text-bg-secondary">{{ $loop->iteration }}</span>
                                    <span>{{ $option->option_text }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    @if ($isSelected)
                                        <span class="badge {{ $isCorrect ? 'text-bg-success' : 'text-bg-danger' }}">{{ __('dictt.placement_test_student_answer') }}</span>
                                    @endif
                                    @if ($isCorrect)
                                        <span class="badge text-bg-success">{{ __('dictt.exercise_attempt_correct_answer') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($answerStatus === 'blank')
                    <div class="alert alert-warning small py-2 mt-3 mb-0" role="alert">
                        {{ __('dictt.placement_test_answer_blank') }}
                    </div>
                @elseif ($answer?->answered_at)
                    <p class="small text-muted mt-3 mb-0">
                        {{ __('dictt.placement_test_answered_at') }} {{ $answer->answered_at->format('d.m.Y H:i:s') }}
                    </p>
                @endif
            </div>
        </article>
    @empty
        <div class="alert alert-warning" role="alert">{{ __('dictt.exercise_attempt_no_questions') }}</div>
    @endforelse
</x-app-layout>
