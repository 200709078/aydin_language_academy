<x-app-layout>
    <x-slot name="header">{{ __('dictt.exercise_attempt_results') }}</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.exercise_attempt_results') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.exercise_attempts_admin_note') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $filters = [
                            'all' => __('dictt.filter_all'),
                            'in_progress' => __('dictt.exercise_attempt_status_in_progress'),
                            'completed' => __('dictt.exercise_attempt_status_completed'),
                        ];
                    @endphp
                    @foreach ($filters as $filterKey => $filterLabel)
                        <a href="{{ route('admin.exercise-attempts.index', ['filter' => $filterKey]) }}"
                            class="btn btn-sm {{ $filter === $filterKey ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $filterLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.member') }}</th>
                            <th scope="col">{{ __('dictt.email') }}</th>
                            <th scope="col">{{ __('dictt.theme') }}</th>
                            <th scope="col">{{ __('dictt.exercise') }}</th>
                            <th scope="col">{{ __('dictt.exercise_attempt_started_at') }}</th>
                            <th scope="col">{{ __('dictt.exercise_attempt_completed_at') }}</th>
                            <th scope="col">{{ __('dictt.exercise_attempt_correct') }}</th>
                            <th scope="col">{{ __('dictt.exercise_attempt_wrong') }}</th>
                            <th scope="col">{{ __('dictt.exercise_attempt_blank') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attempts as $exerciseAttempt)
                            @php
                                $exercise = $exerciseAttempt->exercise;
                                $summary = $exercise ? $exerciseAttempt->summaryFor($exercise->questions) : null;
                                $exerciseText = $exercise ? ($exercise->qtext ?: $exercise->title) : null;
                                $themeName = $exercise?->theme?->name ?? '—';
                            @endphp
                            <tr>
                                <td class="text-break">{{ $exerciseAttempt->user?->name ?? ($exerciseAttempt->user_id ? '#' . $exerciseAttempt->user_id : '—') }}</td>
                                <td class="text-break">{{ $exerciseAttempt->user?->email ?? '—' }}</td>
                                <td class="admin-exercise-attempt-theme-cell" title="{{ $themeName }}">
                                    <span class="admin-table-cell-ellipsis">{{ $themeName }}</span>
                                </td>
                                <td class="admin-exercise-attempt-exercise-cell" title="{{ $exerciseText ?? '—' }}">
                                    <span class="admin-table-cell-ellipsis">{{ $exerciseText ?? '—' }}</span>
                                </td>
                                <td class="text-nowrap">{{ $exerciseAttempt->started_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td class="text-nowrap">{{ $exerciseAttempt->completed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td class="text-success fw-semibold">{{ $summary['correct'] ?? '—' }}</td>
                                <td class="text-danger fw-semibold">{{ $summary['wrong'] ?? '—' }}</td>
                                <td class="text-warning fw-semibold">{{ $summary['blank'] ?? '—' }}</td>
                                <td>
                                    @if ($exerciseAttempt->status === 'in_progress')
                                        <span class="badge text-bg-primary">{{ __('dictt.exercise_attempt_status_in_progress') }}</span>
                                    @else
                                        <span class="badge text-bg-success">{{ __('dictt.exercise_attempt_status_completed') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.exercise-attempts.show', $exerciseAttempt) }}"
                                        class="btn btn-sm btn-primary" title="{{ __('dictt.exercise_attempt_review') }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('dictt.exercise_attempt_review') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">{{ __('dictt.exercise_attempts_admin_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attempts->hasPages())
                <div class="mt-3">{{ $attempts->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
