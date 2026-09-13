<x-app-layout>
    <x-slot name="header">{{ __('scholarship.score_management') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('scholarship.score_management') }}</h5>
            <p class="text-muted small">{{ __('scholarship.score_help') }}</p>
            @include('admin.scholarship.scores._filters')
            <p class="text-muted small">{{ __('scholarship.score_total', ['total' => $applications->total()]) }}</p>
            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead><tr>
                        <th scope="col">{{ __('scholarship.student_name') }} / {{ __('scholarship.application_number') }}</th>
                        <th scope="col">{{ __('scholarship.current_school') }} / {{ __('scholarship.current_level') }}</th>
                        <th scope="col">{{ __('scholarship.sessions') }}</th>
                        <th scope="col">{{ __('scholarship.attendance') }}</th>
                        <th scope="col">{{ __('scholarship.score') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr id="score-row-{{ $application->id }}">
                                <td class="text-break"><a href="{{ route('admin.scholarship.applications.show', $application) }}">{{ $application->student_name_snapshot }}</a><div class="small">{{ $application->application_number }}</div></td>
                                <td class="text-break">{{ $application->school_name_snapshot }}<div class="small">{{ $application->student_level_name_snapshot }}</div></td>
                                <td class="text-break"><div>{{ $application->period->title }}</div><div>{{ $application->session->branch->name }} / {{ $application->session->examGroup->name }}</div><div class="small">{{ $application->session->exam_title }}</div><div class="small">{{ $application->session->exam_date->format('d.m.Y') }} {{ $application->session->starts_at }}–{{ $application->session->ends_at }}</div></td>
                                <td><span class="badge {{ $application->attendance_status === 'attended' ? 'text-bg-success' : ($application->attendance_status === 'absent' ? 'text-bg-secondary' : 'text-bg-warning') }}">{{ __('scholarship.attendance_'.$application->attendance_status) }}</span></td>
                                <td>
                                    @if ($application->attendance_status === 'absent')
                                        <strong>0</strong>
                                        <p class="small text-muted mb-1">{{ __('scholarship.score_absent_help') }}</p>
                                        <a class="small" href="{{ route('admin.scholarship.attendance.index', ['q' => $application->application_number]) }}">{{ __('scholarship.attendance_management') }}</a>
                                    @else
                                        <form id="score-{{ $application->id }}" method="POST"
                                            action="{{ route('admin.scholarship.scores.update', ['application' => $application->id, ...$filters]) }}"
                                            x-data="{ score: @js((string) ($application->score ?? '')) }"
                                            x-on:submit="if (score === '' && @js($application->score !== null)) {
                                                $event.preventDefault();
                                                $dispatch('ala-action-confirmation', @js([
                                                    'formId' => 'score-'.$application->id,
                                                    'title' => __('scholarship.score_clear'),
                                                    'content' => __('scholarship.score_clear_confirm', ['number' => $application->application_number]),
                                                    'actionLabel' => __('dictt.save'),
                                                    'icon' => 'fa-eraser',
                                                    'tone' => 'danger',
                                                ]));
                                            }">
                                            @csrf @method('PATCH')
                                            <label for="score-value-{{ $application->id }}" class="visually-hidden">{{ __('scholarship.score_for', ['number' => $application->application_number]) }}</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <input id="score-value-{{ $application->id }}" name="score" type="number" min="0" max="100" step="1"
                                                    value="{{ $application->score ?? '' }}" x-model="score" @required($application->result_published)
                                                    placeholder="{{ __('scholarship.not_entered') }}" class="form-control form-control-sm" style="width: 7rem;">
                                                <button type="submit" class="btn btn-sm btn-primary">{{ __('dictt.save') }}</button>
                                            </div>
                                        </form>
                                    @endif
                                    <div class="small mt-2">{{ __('scholarship.result_publication') }}: {{ __('scholarship.'.($application->result_published ? 'published' : 'unpublished')) }}</div>
                                    @if ($application->result_published)<p class="small text-muted mb-0">{{ __('scholarship.score_published_help') }}</p>@endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ __('scholarship.applications_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($applications->hasPages())<div class="mt-3">{{ $applications->links() }}</div>@endif
        </div>
    </div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
