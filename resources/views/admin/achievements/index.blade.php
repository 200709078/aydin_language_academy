<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievements') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
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
                    <a href="{{ route('admin.achievements.create') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.achievement_year_add') }}
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.achievement_year') }}</th>
                            <th scope="col">{{ __('dictt.achievement_year_title') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
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
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $item->year }}</td>
                                <td class="text-break">
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    @if ($item->description)
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->description, 70) }}</div>
                                    @endif
                                </td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>{{ $item->entries_count }}</td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.achievements.entries.index', $item) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.achievement_entries_list') }}">
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.achievement_entries_list') }}</span>
                                        </a>
                                        <a href="{{ route('admin.achievements.edit', $item) }}" class="btn btn-sm btn-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.achievements.status.update', $item) }}"
                                            class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="0">
                                            <div class="form-check form-switch mb-0">
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
                                        <div class="d-flex flex-column gap-1">
                                            <form method="POST" action="{{ route('admin.achievements.move', $item) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    @disabled(! $canMoveUp) title="{{ __('dictt.move_up') }}">
                                                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_up') }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.achievements.move', $item) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    @disabled(! $canMoveDown) title="{{ __('dictt.move_down') }}">
                                                    <i class="fa fa-arrow-down" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_down') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dictt.achievement_year_empty') }}</td>
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
</x-app-layout>
