<x-app-layout>
    <x-slot name="header">{{ __('scholarship.applications') }}</x-slot>
    <div class="card" x-data="{ selected: [] }">
        <div class="card-body">
            <h5 class="card-title">{{ __('scholarship.applications') }}</h5>
            @include('admin.scholarship.applications._filters')
            <p class="text-muted small">{{ __('scholarship.application_totals', ['total' => $applications->total(), 'pending' => $pendingCount]) }}</p>
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
                        <label for="scope-filtered" class="form-check-label">{{ __('scholarship.scope_filtered') }} ({{ $pendingCount }})</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-success" @disabled($pendingCount === 0)>{{ __('scholarship.approval_preview') }}</button>
                </div>
                <p class="small text-muted mb-0 mt-2">{{ __('scholarship.selection_help') }}</p>
            </form>
            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead><tr>
                        <th scope="col"><input type="checkbox" class="form-check-input" aria-label="{{ __('scholarship.select_page') }}"
                            x-on:change="selected = $event.target.checked ? @js($applications->where('status', 'pending')->pluck('id')->map(fn ($id) => (string) $id)->values()) : []"></th>
                        <th scope="col">{{ __('scholarship.student_name') }} / {{ __('scholarship.application_number') }}</th>
                        <th scope="col">{{ __('scholarship.current_school') }} / {{ __('scholarship.current_level') }}</th>
                        <th scope="col">{{ __('scholarship.account_contact') }}</th>
                        <th scope="col">{{ __('scholarship.sessions') }}</th>
                        <th scope="col">{{ __('scholarship.approval_status') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="{{ $application->id }}" form="bulk-approval" x-model="selected" class="form-check-input"
                                    @disabled($application->status !== 'pending') aria-label="{{ __('scholarship.select_application', ['number' => $application->application_number]) }}"></td>
                                <td class="text-break"><a href="{{ route('admin.scholarship.applications.show', $application) }}">{{ $application->student_name_snapshot }}</a><div class="small">{{ $application->application_number }}</div></td>
                                <td class="text-break">{{ $application->school_name_snapshot }}<div class="small">{{ $application->student_level_name_snapshot }}</div></td>
                                <td class="text-break">{{ $application->user?->email ?? __('scholarship.account_deleted') }}<div class="small">{{ $application->user?->phone ?? '—' }}</div></td>
                                <td class="text-break"><div>{{ $application->period->title }}</div><div>{{ $application->session->branch->name }} / {{ $application->session->examGroup->name }}</div><div class="small">{{ $application->session->exam_title }}</div><div class="small">{{ $application->session->exam_date->format('d.m.Y') }} {{ $application->session->starts_at }}–{{ $application->session->ends_at }}</div></td>
                                <td><span class="badge {{ $application->status === 'approved' ? 'text-bg-success' : 'text-bg-warning' }}">{{ __('scholarship.approval_'.$application->status) }}</span><div class="small mt-1">{{ __('scholarship.'.($application->application_published ? 'published' : 'unpublished')) }}</div></td>
                                <td>@include('admin.scholarship.applications._actions')</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">{{ __('scholarship.applications_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($applications->hasPages())<div class="mt-3">{{ $applications->links() }}</div>@endif
        </div>
    </div>
    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
