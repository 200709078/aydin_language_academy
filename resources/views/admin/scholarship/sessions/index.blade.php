<x-app-layout>
    <x-slot name="header">{{ __('scholarship.sessions') }}</x-slot>

    @include('admin.scholarship._success')

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('scholarship.sessions') }}</h5>
                    <p class="text-muted small mb-0">{{ __('scholarship.sessions_help') }}</p>
                </div>
                <a href="{{ route('admin.scholarship.sessions.create', ['period_id' => $filters['period_id'] ?? null]) }}" class="btn btn-sm btn-outline-primary text-nowrap">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('scholarship.session_add') }}
                </a>
            </div>

            <form method="GET" action="{{ route('admin.scholarship.sessions.index') }}" class="row g-3 mb-4">
                @foreach (['period_id' => [$periods, 'period', 'title'], 'branch_id' => [$branches, 'branch', 'name'], 'exam_group_id' => [$groups, 'exam_group', 'name']] as $field => [$options, $label, $textField])
                    <div class="col-md-4">
                        <label for="filter-{{ $field }}" class="form-label">{{ __('scholarship.'.$label) }}</label>
                        <select id="filter-{{ $field }}" name="{{ $field }}" class="form-select @error($field) is-invalid @enderror">
                            <option value="">{{ __('dictt.filter_all') }}</option>
                            @foreach ($options as $option)
                                <option value="{{ $option->id }}" @selected(($filters[$field] ?? '') == $option->id)>{{ $option->{$textField} }}{{ $option->is_active ? '' : ' ('.__('dictt.passive').')' }}</option>
                            @endforeach
                        </select>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                <div class="col-md-4">
                    <label for="filter-state" class="form-label">{{ __('scholarship.session_state') }}</label>
                    <select id="filter-state" name="state" class="form-select @error('state') is-invalid @enderror">
                        <option value="">{{ __('dictt.filter_all') }}</option>
                        @foreach (['active', 'suspended', 'archived'] as $state)
                            <option value="{{ $state }}" @selected(($filters['state'] ?? '') === $state)>{{ __('scholarship.session_'.$state) }}</option>
                        @endforeach
                    </select>
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="filter-q" class="form-label">{{ __('scholarship.exam_title') }}</label>
                    <input id="filter-q" name="q" value="{{ $filters['q'] ?? '' }}" type="search" maxlength="150" class="form-control @error('q') is-invalid @enderror">
                    @error('q')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">{{ __('scholarship.filter_apply') }}</button>
                    <a href="{{ route('admin.scholarship.sessions.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('scholarship.filter_clear') }}</a>
                </div>
            </form>

            <p class="text-muted small">{{ __('scholarship.session_totals', $totals) }}</p>

            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('scholarship.exam_title') }}</th>
                            <th scope="col">{{ __('scholarship.branch') }} / {{ __('scholarship.exam_group') }}</th>
                            <th scope="col">{{ __('scholarship.session_date_time') }}</th>
                            <th scope="col">{{ __('scholarship.occupancy') }}</th>
                            <th scope="col">{{ __('scholarship.session_state') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                            @php
                                $archived = $session->archived_at !== null;
                                $state = $archived ? 'archived' : ($session->is_active ? 'active' : 'suspended');
                                $full = $session->applications_count >= $session->capacity;
                            @endphp
                            <tr>
                                <td class="text-break">
                                    <a href="{{ route('admin.scholarship.sessions.edit', $session) }}">{{ $session->exam_title }}</a>
                                    <div class="text-muted small">{{ $session->period->title }}</div>
                                    @if (!$session->period->acceptsApplications())
                                        <div class="text-muted small">{{ __('scholarship.period_not_accepting') }}</div>
                                    @elseif ($session->startsAt()->isPast())
                                        <div class="text-muted small">{{ __('scholarship.session_started') }}</div>
                                    @endif
                                </td>
                                <td class="text-break">
                                    <div>{{ $session->branch->name }}{{ $session->branch->is_active ? '' : ' ('.__('dictt.passive').')' }}</div>
                                    <div>{{ $session->examGroup->name }}{{ $session->examGroup->is_active ? '' : ' ('.__('dictt.passive').')' }}</div>
                                </td>
                                <td class="text-nowrap">
                                    <div>{{ $session->exam_date->format('d.m.Y') }}</div>
                                    <div>{{ $session->starts_at }}–{{ $session->ends_at }}</div>
                                </td>
                                <td>
                                    <strong>{{ $session->applications_count }} / {{ $session->capacity }}</strong>
                                    @if ($full)
                                        <div><span class="badge text-bg-warning">{{ __('scholarship.session_full') }}</span></div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $state === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ __('scholarship.session_'.$state) }}</span>
                                    @if (!$archived)
                                        <form method="POST" action="{{ route('admin.scholarship.sessions.status.update', $session) }}" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="form-check form-switch admin-list-switch mb-0">
                                                <input id="session-active-{{ $session->id }}" type="checkbox" name="is_active" value="1" role="switch"
                                                    class="form-check-input" @checked($session->is_active) onchange="this.form.submit()"
                                                    aria-label="{{ __('scholarship.session_active_for', ['title' => $session->exam_title]) }}">
                                            </div>
                                            <noscript><button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('dictt.update') }}</button></noscript>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.scholarship.sessions.edit', $session) }}" class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i><span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form id="session-archive-{{ $session->id }}" method="POST" action="{{ route('admin.scholarship.sessions.archive.update', $session) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="archived" value="{{ $archived ? '0' : '1' }}">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-action-confirmation
                                                title="{{ __('scholarship.'.($archived ? 'session_restore' : 'session_archive')) }}"
                                                data-confirm-form="session-archive-{{ $session->id }}"
                                                data-confirm-title="{{ __('scholarship.'.($archived ? 'session_restore' : 'session_archive')) }}"
                                                data-confirm-content="{{ __('scholarship.'.($archived ? 'session_restore_confirm' : 'session_archive_confirm'), ['title' => $session->exam_title]) }}"
                                                data-confirm-action="{{ __('scholarship.'.($archived ? 'session_restore' : 'session_archive')) }}"
                                                data-confirm-icon="{{ $archived ? 'fa-box-open' : 'fa-archive' }}" data-confirm-tone="neutral">
                                                <i class="fa {{ $archived ? 'fa-box-open' : 'fa-archive' }}" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('scholarship.'.($archived ? 'session_restore' : 'session_archive')) }}</span>
                                            </button>
                                        </form>
                                        @if ($session->applications_count > 0)
                                            <span title="{{ __('scholarship.session_has_applications') }}">
                                                <button type="button" class="btn btn-sm btn-outline-danger" disabled aria-label="{{ __('scholarship.session_has_applications') }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                            </span>
                                        @else
                                            <form id="session-delete-{{ $session->id }}" method="POST" action="{{ route('admin.scholarship.sessions.destroy', $session) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger admin-danger-action" title="{{ __('scholarship.session_delete') }}"
                                                    data-action-confirmation data-confirm-form="session-delete-{{ $session->id }}"
                                                    data-confirm-title="{{ __('scholarship.session_delete') }}"
                                                    data-confirm-content="{{ __('scholarship.session_delete_confirm', ['title' => $session->exam_title]) }}"
                                                    data-confirm-action="{{ __('scholarship.session_delete') }}" data-confirm-icon="fa-trash-alt" data-confirm-tone="danger">
                                                    <i class="fa fa-trash" aria-hidden="true"></i><span class="visually-hidden">{{ __('scholarship.session_delete') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('scholarship.sessions_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-3">{{ __('scholarship.session_states_help') }}</p>
            <p class="text-muted small mb-0">{{ __('dictt.scholarship_timezone', ['timezone' => config('app.timezone')]) }}</p>
            @if ($sessions->hasPages())
                <div class="mt-3">{{ $sessions->links() }}</div>
            @endif
        </div>
    </div>

    <div class="position-relative" style="z-index: 1055;"><x-action-confirmation-modal /></div>
</x-app-layout>
