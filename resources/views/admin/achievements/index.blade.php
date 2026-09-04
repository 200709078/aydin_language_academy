<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievements') }}</x-slot>

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

    @php
        $statusLabels = [
            \App\Models\Achievement::STATUS_DRAFT => __('dictt.achievement_status_draft'),
            \App\Models\Achievement::STATUS_PUBLISHED => __('dictt.achievement_status_published'),
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.achievement_years') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.achievement_years_admin_help') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.achievements.settings') }}" class="btn btn-sm btn-outline-secondary"
                        title="{{ __('dictt.achievement_page_settings') }}"
                        aria-label="{{ __('dictt.achievement_page_settings') }}">
                        <i class="fa fa-gear" aria-hidden="true"></i>
                        <span class="visually-hidden">{{ __('dictt.achievement_page_settings') }}</span>
                    </a>
                    <a href="{{ route('admin.achievements.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.achievement_year_add') }}
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.achievement_year_title') }}</th>
                            <th scope="col">{{ __('dictt.achievement_publication_status') }}</th>
                            <th scope="col">{{ __('dictt.achievement_entries_count') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($years as $item)
                            @php
                                $statusLabel = $statusLabels[$item->status] ?? $item->status;
                                $statusClass = $item->status === \App\Models\Achievement::STATUS_DRAFT
                                    ? 'text-bg-secondary'
                                    : 'text-bg-success';
                                $canMoveUp = $moveAvailability[$item->id]['up'] ?? false;
                                $canMoveDown = $moveAvailability[$item->id]['down'] ?? false;
                                $isDeletable = $item->status === \App\Models\Achievement::STATUS_DRAFT;
                            @endphp
                            <tr>
                                <td class="text-break">
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    @if ($item->description)
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->description, 70) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <form method="POST" action="{{ route('admin.achievements.status.update', $item) }}"
                                            class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="0">
                                            <div class="form-check form-switch admin-list-switch mb-0">
                                                <input id="achievement-status-{{ $item->id }}" type="checkbox"
                                                    class="form-check-input" name="is_published" value="1" role="switch"
                                                    @checked($item->status === \App\Models\Achievement::STATUS_PUBLISHED)
                                                    onchange="this.form.submit()"
                                                    aria-label="{{ __('dictt.achievement_publication_status') }}">
                                            </div>
                                            <noscript>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary mt-1">
                                                    {{ __('dictt.update') }}
                                                </button>
                                            </noscript>
                                        </form>
                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                </td>
                                <td>{{ $item->entries_count }}</td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.achievements.entries.index', $item) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.achievement_entries_list') }}">
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.achievement_entries_list') }}</span>
                                        </a>
                                        <a href="{{ route('admin.achievements.edit', $item) }}" class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form id="achievement-delete-{{ $item->id }}" method="POST"
                                            action="{{ route('admin.achievements.destroy', $item) }}" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm {{ $isDeletable ? 'btn-outline-danger admin-danger-action' : 'btn-outline-secondary' }}"
                                                @disabled(! $isDeletable)
                                                title="{{ __('dictt.achievement_year_permanently_delete') }}"
                                                data-action-confirmation
                                                data-confirm-form="achievement-delete-{{ $item->id }}"
                                                data-confirm-title="{{ __('dictt.achievement_year_permanently_delete') }}"
                                                data-confirm-content="{{ __('dictt.achievement_year_force_delete_confirm', ['year' => $item->title]) }}"
                                                data-confirm-action="{{ __('dictt.achievement_year_permanently_delete') }}"
                                                data-confirm-icon="fa-trash-alt"
                                                data-confirm-tone="danger">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('dictt.achievement_year_permanently_delete') }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.achievements.move', $item) }}"
                                            class="d-inline-block">
                                            @csrf
                                            <div class="btn-group-vertical btn-group-sm" role="group"
                                                aria-label="{{ __('dictt.move_up') }} / {{ __('dictt.move_down') }}">
                                                <button type="submit" name="direction" value="up"
                                                    class="btn btn-outline-secondary px-2 py-0"
                                                    @disabled(! $canMoveUp) title="{{ __('dictt.move_up') }}">
                                                    <i class="fa-solid fa-chevron-up fa-xs" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_up') }}</span>
                                                </button>
                                                <button type="submit" name="direction" value="down"
                                                    class="btn btn-outline-secondary px-2 py-0"
                                                    @disabled(! $canMoveDown) title="{{ __('dictt.move_down') }}">
                                                    <i class="fa-solid fa-chevron-down fa-xs" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_down') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">{{ __('dictt.achievement_year_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($years->hasPages())
                <div class="mt-3">{{ $years->links() }}</div>
            @endif
        </div>
    </div>

    <x-action-confirmation-modal />
</x-app-layout>
