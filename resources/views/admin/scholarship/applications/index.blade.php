<x-app-layout>
    <x-slot name="header">{{ __('scholarship.applications') }}</x-slot>
    <div class="card" x-data="{ selected: [] }">
        <div class="card-body">
            <h5 class="card-title">{{ __('scholarship.applications') }}</h5>
            @include('admin.scholarship.applications._filters')
            <p class="text-muted small">{{ __('scholarship.application_totals', ['total' => $applications->total(), 'pending' => $pendingCount]) }}</p>
            @if (session('publicationSkipped'))
                <div class="alert alert-warning">
                    <p class="mb-1">{{ __('scholarship.publication_skipped_details') }}</p>
                    <ul class="mb-0">@foreach (session('publicationSkipped') as $skipped)<li>{{ $skipped['number'] }}: {{ $skipped['reason'] }}</li>@endforeach</ul>
                </div>
            @endif
            <form id="bulk-approval" method="POST" action="{{ route('admin.scholarship.applications.approval.preview') }}" class="border rounded p-3 mb-3">
                @csrf
                @foreach ($filters as $key => $value)<input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">@endforeach
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="form-check">
                        <input id="scope-selected" type="radio" name="scope" value="selected" checked class="form-check-input">
                        <label for="scope-selected" class="form-check-label">{{ __('scholarship.scope_selected') }} (<span x-text="selected.length">0</span>)</label>
                    </div>
                    <div class="form-check">
                        <input id="scope-filtered" type="radio" name="scope" value="filtered" class="form-check-input">
                        <label for="scope-filtered" class="form-check-label">{{ __('scholarship.scope_all_filtered') }} ({{ $applications->total() }})</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-success" @disabled($pendingCount === 0)>{{ __('scholarship.approval_preview') }}</button>
                </div>
                <p class="small text-muted mb-0 mt-2">{{ __('scholarship.bulk_selection_help') }}</p>
                <div class="row g-3 align-items-end mt-1">
                    <div class="col-md-5">
                        <label for="publication-phase" class="form-label">{{ __('scholarship.publication_phase') }}</label>
                        <select id="publication-phase" name="phase" class="form-select form-select-sm">
                            @foreach (['application', 'result'] as $phase)<option value="{{ $phase }}">{{ __('scholarship.'.$phase.'_publication') }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="publication-action" class="form-label">{{ __('scholarship.publication_action') }}</label>
                        <select id="publication-action" name="published" class="form-select form-select-sm">
                            <option value="1">{{ __('scholarship.publication_enable') }}</option>
                            <option value="0">{{ __('scholarship.publication_disable') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4"><button type="submit" formaction="{{ route('admin.scholarship.applications.publication.preview') }}" class="btn btn-sm btn-outline-primary" @disabled($applications->total() === 0)>{{ __('scholarship.publication_preview') }}</button></div>
                </div>
                <p class="small text-muted mb-0 mt-2">{{ __('scholarship.publication_help') }}</p>
            </form>
            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead><tr>
                        <th scope="col"><input type="checkbox" class="form-check-input" aria-label="{{ __('scholarship.select_all_page') }}"
                            x-on:change="selected = $event.target.checked ? @js($applications->pluck('id')->map(fn ($id) => (string) $id)->values()) : []"></th>
                        <th scope="col">{{ __('scholarship.student_name') }} / {{ __('scholarship.application_number') }}</th>
                        <th scope="col">{{ __('scholarship.current_school') }} / {{ __('scholarship.current_level') }}</th>
                        <th scope="col">{{ __('scholarship.account_contact') }}</th>
                        <th scope="col">{{ __('scholarship.sessions') }}</th>
                        <th scope="col">{{ __('scholarship.approval_status') }}</th>
                        <th scope="col">{{ __('scholarship.publication_management') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="{{ $application->id }}" form="bulk-approval" x-model="selected" class="form-check-input"
                                    aria-label="{{ __('scholarship.select_application', ['number' => $application->application_number]) }}"></td>
                                <td class="text-break"><a href="{{ route('admin.scholarship.applications.show', $application) }}">{{ $application->student_name_snapshot }}</a><div class="small">{{ $application->application_number }}</div></td>
                                <td class="text-break">{{ $application->school_name_snapshot }}<div class="small">{{ $application->student_level_name_snapshot }}</div></td>
                                <td class="text-break">{{ $application->user?->email ?? __('scholarship.account_deleted') }}<div class="small">{{ $application->user?->phone ?? '—' }}</div></td>
                                <td class="text-break"><div>{{ $application->period->title }}</div><div>{{ $application->session->branch->name }} / {{ $application->session->examGroup->name }}</div><div class="small">{{ $application->session->exam_title }}</div><div class="small">{{ $application->session->exam_date->format('d.m.Y') }} {{ $application->session->starts_at }}–{{ $application->session->ends_at }}</div></td>
                                <td><span class="badge {{ $application->status === 'approved' ? 'text-bg-success' : 'text-bg-warning' }}">{{ __('scholarship.approval_'.$application->status) }}</span><div class="small mt-1">{{ __('scholarship.'.($application->application_published ? 'published' : 'unpublished')) }}</div></td>
                                <td>@include('admin.scholarship.applications._publication-switches')</td>
                                <td>@include('admin.scholarship.applications._actions')</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">{{ __('scholarship.applications_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($applications->hasPages())<div class="mt-3">{{ $applications->links() }}</div>@endif
        </div>
    </div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
