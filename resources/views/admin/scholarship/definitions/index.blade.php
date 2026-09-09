<x-app-layout>
    <x-slot name="header">{{ __('scholarship.'.$meta['title']) }}</x-slot>

    @include('admin.scholarship._success')

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('scholarship.'.$meta['title']) }}</h5>
                    <p class="text-muted small mb-0">{{ __('scholarship.'.$meta['help']) }}</p>
                </div>
                <a href="{{ route('admin.scholarship.definitions.create', ['type' => $type]) }}" class="btn btn-sm btn-outline-primary text-nowrap">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('scholarship.definition_add') }}
                </a>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                @foreach (['all' => 'filter_all', 'active' => 'filter_active', 'inactive' => 'filter_inactive'] as $value => $label)
                    <a href="{{ route('admin.scholarship.definitions.index', ['type' => $type, 'status' => $value, 'q' => $search]) }}"
                        class="btn btn-sm {{ $status === $value ? 'btn-primary' : 'btn-outline-primary' }}"
                        @if ($status === $value) aria-current="page" @endif>{{ __('scholarship.'.$label) }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.scholarship.definitions.index', ['type' => $type]) }}" class="row g-2 align-items-end mb-3">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="col-12 col-md-6">
                    <label for="definition-search" class="form-label">{{ __('scholarship.definition_search') }}</label>
                    <input id="definition-search" name="q" type="search" maxlength="255" value="{{ $search }}" class="form-control">
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">{{ __('scholarship.filter_apply') }}</button>
                    <a href="{{ route('admin.scholarship.definitions.index', ['type' => $type]) }}" class="btn btn-outline-secondary">{{ __('scholarship.filter_clear') }}</a>
                </div>
            </form>

            <div class="table-responsive position-relative">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('scholarship.'.$meta['name']) }}</th>
                            <th scope="col">{{ __('scholarship.definition_sort_order') }}</th>
                            <th scope="col">{{ __('scholarship.definition_references') }}</th>
                            <th scope="col">{{ __('scholarship.definition_status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($definitions as $definition)
                            @php($params = ['type' => $type, 'definition' => $definition->id])
                            <tr>
                                <td class="text-break">
                                    <a href="{{ route('admin.scholarship.definitions.edit', $params) }}">{{ $definition->name }}</a>
                                    @if ($type !== 'school')
                                        <div class="text-muted small">{{ __('scholarship.definition_code') }}: {{ $definition->code }}</div>
                                    @endif
                                    @if ($type === 'branch' && $definition->address)
                                        <div class="text-muted small">{{ $definition->address }}</div>
                                    @endif
                                </td>
                                <td>{{ $definition->sort_order }}</td>
                                <td class="text-nowrap">{{ __('scholarship.definition_'.$meta['relation'].'_count', ['count' => $definition->references_count]) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.scholarship.definitions.status.update', $params) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="0">
                                        <div class="form-check form-switch admin-list-switch mb-1">
                                            <input id="definition-active-{{ $definition->id }}" type="checkbox" name="is_active" value="1"
                                                class="form-check-input" role="switch" @checked($definition->is_active)
                                                onchange="this.form.submit()" aria-label="{{ __('scholarship.definition_active_for', ['name' => $definition->name]) }}">
                                        </div>
                                        <noscript><button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('dictt.update') }}</button></noscript>
                                    </form>
                                    <span class="badge {{ $definition->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ __($definition->is_active ? 'dictt.active' : 'dictt.passive') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.scholarship.definitions.edit', $params) }}" class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i><span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        @if ($definition->references_count > 0)
                                            <span title="{{ __('scholarship.definition_has_dependents') }}">
                                                <button type="button" class="btn btn-sm btn-outline-danger" disabled aria-label="{{ __('scholarship.definition_has_dependents') }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                        @else
                                            <form id="definition-delete-{{ $definition->id }}" method="POST" action="{{ route('admin.scholarship.definitions.destroy', $params) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger admin-danger-action"
                                                    title="{{ __('scholarship.definition_delete') }}" data-action-confirmation
                                                    data-confirm-form="definition-delete-{{ $definition->id }}"
                                                    data-confirm-title="{{ __('scholarship.definition_delete') }}"
                                                    data-confirm-content="{{ __('scholarship.definition_delete_confirm', ['name' => $definition->name]) }}"
                                                    data-confirm-action="{{ __('scholarship.definition_delete') }}"
                                                    data-confirm-icon="fa-trash-alt" data-confirm-tone="danger">
                                                    <i class="fa fa-trash" aria-hidden="true"></i><span class="visually-hidden">{{ __('scholarship.definition_delete') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ __('scholarship.definitions_empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-3 mb-0">{{ __('scholarship.definition_active_help') }}</p>
            @if ($definitions->hasPages())
                <div class="mt-3">{{ $definitions->links() }}</div>
            @endif
        </div>
    </div>

    <div class="position-relative" style="z-index: 1055;">
        <x-action-confirmation-modal />
    </div>
</x-app-layout>
