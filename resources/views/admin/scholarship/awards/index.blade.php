<x-app-layout>
    <x-slot name="header">{{ __('scholarship.award_management') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('scholarship.award_management') }}</h5>
            <p class="text-muted small">{{ __('scholarship.award_help') }}</p>
            @include('admin.scholarship.scores._filters', ['filterRoute' => 'admin.scholarship.awards.index', 'showAwardFilter' => true])
            <p class="text-muted small">{{ __('scholarship.award_total', ['total' => $applications->total()]) }}</p>
            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead><tr>
                        <th scope="col">{{ __('scholarship.applicant_name') }}</th>
                        <th scope="col">{{ __('scholarship.application_info') }}</th>
                        <th scope="col">{{ __('scholarship.session_info') }}</th>
                        <th scope="col" style="width: 10%;">{{ __('scholarship.score') }} / {{ __('scholarship.attendance') }}</th>
                        <th scope="col" style="width: 30%;">{{ __('scholarship.scholarship_percentage') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr id="award-row-{{ $application->id }}">
                                <td class="text-break"><a href="{{ route('admin.scholarship.applications.show', $application) }}">{{ $application->student_name_snapshot }}</a></td>
                                <td class="text-break">{{ $application->school_name_snapshot }}<div class="small">{{ $application->student_level_name_snapshot }}</div></td>
                                <td class="text-break"><div>{{ $application->period->title }}</div><div>{{ $application->session->branch->name }} / {{ $application->session->examGroup->name }}</div><div class="small">{{ $application->session->exam_title }}</div><div class="small">{{ $application->session->exam_date->format('d.m.Y') }} {{ $application->session->starts_at }}–{{ $application->session->ends_at }}</div></td>
                                <td><strong>{{ $application->score ?? __('scholarship.not_entered') }}</strong><div class="small mt-1">{{ __('scholarship.attendance_'.$application->attendance_status) }}</div></td>
                                <td>
                                    @if ($application->attendance_status === 'absent')
                                        <strong>{{ __('scholarship.award_none') }}</strong>
                                        <p class="small text-muted mb-1">{{ __('scholarship.award_absent_help') }}</p>
                                        <a class="small" href="{{ route('admin.scholarship.attendance.index', ['q' => $application->application_number]) }}">{{ __('scholarship.attendance_management') }}</a>
                                    @else
                                        <form id="award-{{ $application->id }}" method="POST"
                                            action="{{ route('admin.scholarship.awards.update', ['application' => $application->id, ...$filters]) }}"
                                            x-data="{ percentage: @js($application->scholarship_percentage ?? -10) }"
                                            x-on:submit="if (percentage === -10 && @js($application->scholarship_percentage !== null)) {
                                                $event.preventDefault();
                                                $dispatch('ala-action-confirmation', @js([
                                                    'formId' => 'award-'.$application->id,
                                                    'title' => __('scholarship.award_clear'),
                                                    'content' => __('scholarship.award_clear_confirm', ['number' => $application->application_number]),
                                                    'actionLabel' => __('dictt.save'),
                                                    'icon' => 'fa-eraser',
                                                    'tone' => 'danger',
                                                ]));
                                            }">
                                            @csrf @method('PATCH')
                                            <div class="mb-2 pe-4">
                                                <label for="award-{{ $application->id }}-range" class="form-label small d-block mb-1">
                                                    {{ __('scholarship.scholarship_percentage') }}:
                                                    <span x-text="percentage === -10 ? @js(__('scholarship.award_unset')) : (percentage === 0 ? @js(__('scholarship.award_none')) : '%' + percentage)">{{ $application->scholarship_percentage === null ? __('scholarship.award_unset') : ($application->scholarship_percentage === 0 ? __('scholarship.award_none') : '%'.$application->scholarship_percentage) }}</span>
                                                </label>
                                                <input id="award-{{ $application->id }}-range" type="range" name="scholarship_percentage" x-model.number="percentage"
                                                    min="{{ $application->result_published ? 0 : -10 }}" max="100" step="10" value="{{ $application->scholarship_percentage ?? -10 }}"
                                                    class="form-range d-block w-100" aria-label="{{ __('scholarship.award_for', ['number' => $application->application_number]) }}">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary mt-2">{{ __('dictt.save') }}</button>
                                        </form>
                                    @endif
                                    @if ($application->result_published)<p class="small text-muted mt-2 mb-0">{{ __('scholarship.award_published_help') }}</p>@endif
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
