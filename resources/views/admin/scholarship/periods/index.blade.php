<x-app-layout>
    <x-slot name="header">{{ __('dictt.scholarship_periods') }}</x-slot>

    @if (session('modalSuccessTitle') && session('modalSuccessContent'))
        <div class="relative bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow mb-6 w-full" role="status">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                    {{ session('modalSuccessTitle') }}
                </h2>
                <button type="button" onclick="this.parentElement.parentElement.remove()"
                    class="text-gray-500 hover:text-red-600 ml-4" aria-label="{{ __('dictt.close') }}">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="mt-2 text-sm">{{ session('modalSuccessContent') }}</div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.scholarship_periods') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.scholarship_periods_help') }}</p>
                </div>
                <a href="{{ route('admin.scholarship.periods.create') }}" class="btn btn-sm btn-outline-primary text-nowrap">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.scholarship_period_add') }}
                </a>
            </div>

            <p class="text-muted small">{{ __('dictt.scholarship_period_access_help') }}</p>

            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.title') }}</th>
                            <th scope="col">{{ __('dictt.scholarship_application_window') }}</th>
                            <th scope="col">{{ __('dictt.scholarship_exam_window') }}</th>
                            <th scope="col">{{ __('dictt.scholarship_period_status') }}</th>
                            <th scope="col">{{ __('dictt.scholarship_applications_open') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periods as $period)
                            @php
                                $acceptsApplications = $period->acceptsApplications();
                                $applicationState = match (true) {
                                    !$period->is_active => 'scholarship_period_inactive',
                                    !$period->applications_open => 'scholarship_applications_closed_manually',
                                    $acceptsApplications => 'scholarship_applications_accepting',
                                    $period->applications_open_at->isFuture() => 'scholarship_applications_upcoming',
                                    default => 'scholarship_applications_ended',
                                };
                                $hasDependents = $period->sessions_count > 0 || $period->applications_count > 0;
                            @endphp
                            <tr>
                                <td class="text-break">
                                    <a href="{{ route('admin.scholarship.periods.edit', $period) }}">{{ $period->title }}</a>
                                    <div class="text-muted small">{{ __('dictt.scholarship_period_counts', ['sessions' => $period->sessions_count, 'applications' => $period->applications_count]) }}</div>
                                </td>
                                <td class="text-nowrap">
                                    <div>{{ $period->applications_open_at->format('d.m.Y H:i:s') }}</div>
                                    <div>{{ $period->applications_close_at->format('d.m.Y H:i:s') }}</div>
                                </td>
                                <td class="text-nowrap">
                                    <div>{{ $period->exam_starts_on->format('d.m.Y') }}</div>
                                    <div>{{ $period->exam_ends_on->format('d.m.Y') }}</div>
                                </td>
                                <td><span class="badge {{ $period->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ __($period->is_active ? 'dictt.active' : 'dictt.passive') }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.scholarship.periods.applications.update', $period) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="applications_open" value="0">
                                        <div class="form-check form-switch admin-list-switch mb-1">
                                            <input id="period-applications-{{ $period->id }}" type="checkbox" name="applications_open" value="1"
                                                class="form-check-input" role="switch" @checked($period->applications_open)
                                                onchange="this.form.submit()" aria-label="{{ __('dictt.scholarship_applications_open_for', ['title' => $period->title]) }}">
                                        </div>
                                        <noscript><button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('dictt.update') }}</button></noscript>
                                    </form>
                                    <span class="badge {{ $acceptsApplications ? 'text-bg-success' : 'text-bg-secondary' }}">{{ __('dictt.'.$applicationState) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.scholarship.sessions.index', ['period_id' => $period->id]) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('scholarship.sessions') }}">
                                            <i class="fa fa-clock" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('scholarship.sessions') }}</span>
                                        </a>
                                        <a href="{{ route('admin.scholarship.periods.edit', $period) }}" class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        @if ($hasDependents)
                                            <span title="{{ __('dictt.scholarship_period_has_dependents') }}">
                                                <button type="button" class="btn btn-sm btn-outline-danger" disabled aria-label="{{ __('dictt.scholarship_period_has_dependents') }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                        @else
                                            <form id="period-delete-{{ $period->id }}" method="POST" action="{{ route('admin.scholarship.periods.destroy', $period) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger admin-danger-action"
                                                    title="{{ __('dictt.scholarship_period_delete') }}" data-action-confirmation
                                                    data-confirm-form="period-delete-{{ $period->id }}"
                                                    data-confirm-title="{{ __('dictt.scholarship_period_delete') }}"
                                                    data-confirm-content="{{ __('dictt.scholarship_period_delete_confirm', ['title' => $period->title]) }}"
                                                    data-confirm-action="{{ __('dictt.scholarship_period_delete') }}"
                                                    data-confirm-icon="fa-trash-alt" data-confirm-tone="danger">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.scholarship_period_delete') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('dictt.scholarship_periods_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-3 mb-0">{{ __('dictt.scholarship_timezone', ['timezone' => config('app.timezone')]) }}</p>

            @if ($periods->hasPages())
                <div class="mt-3">{{ $periods->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Keep the shared dialog above the fixed admin navigation. --}}
    <div class="position-relative" style="z-index: 1055;">
        <x-action-confirmation-modal />
    </div>
</x-app-layout>
