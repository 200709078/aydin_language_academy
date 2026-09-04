<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_entries') }}</x-slot>

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
        $entryFilters = [
            'all' => __('dictt.achievement_filter_all'),
            \App\Models\AchievementEntry::STATUS_PUBLISHED => __('dictt.achievement_status_published'),
            \App\Models\AchievementEntry::STATUS_DRAFT => __('dictt.achievement_status_draft'),
        ];
        $entryStatusLabels = [
            \App\Models\AchievementEntry::STATUS_DRAFT => __('dictt.achievement_status_draft'),
            \App\Models\AchievementEntry::STATUS_PUBLISHED => __('dictt.achievement_status_published'),
        ];
        $permissionLabels = [
            \App\Models\AchievementEntry::NAME_PERMISSION_UNKNOWN => __('dictt.achievement_name_permission_unknown'),
            \App\Models\AchievementEntry::NAME_PERMISSION_GRANTED => __('dictt.achievement_name_permission_granted'),
            \App\Models\AchievementEntry::NAME_PERMISSION_DENIED => __('dictt.achievement_name_permission_denied'),
        ];
        $branchLabels = collect(\App\Models\AchievementEntry::BRANCHES)
            ->mapWithKeys(fn (string $branch): array => [$branch => __('dictt.branch_' . $branch)])
            ->all();
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="row g-2 align-items-center mb-4">
                <div class="col-auto order-2 order-md-1">
                    <a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.achievement_back') }}
                    </a>
                </div>
                <div class="col-12 col-md order-1 order-md-2 text-center px-md-2" style="min-width: 0;">
                    <h5 class="card-title mb-1 text-break">{{ __('dictt.achievement_entries') }}</h5>
                    <p class="text-muted small mb-0 text-break">
                        {{ $achievementYear->year }} — {{ $achievementYear->title }}
                    </p>
                </div>
                <div class="col-auto order-3 order-md-3 ms-auto ms-md-0">
                    <a href="{{ route('admin.achievements.entries.create', $achievementYear) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.achievement_entry_add_short') }}
                    </a>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                @foreach ($entryFilters as $filterKey => $filterLabel)
                    <a href="{{ route('admin.achievements.entries.index', [$achievementYear, 'entry_filter' => $filterKey]) }}"
                        class="btn btn-sm {{ $entryFilter === $filterKey ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $filterLabel }}
                    </a>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.achievement_entry_student') }}</th>
                            <th scope="col">{{ __('dictt.achievement_entry_placement') }}</th>
                            <th scope="col">{{ __('dictt.branch') }}</th>
                            <th scope="col">{{ __('dictt.achievement_name_permission') }}</th>
                            <th scope="col">{{ __('dictt.achievement_publication_status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            @php
                                $statusLabel = $entryStatusLabels[$entry->status] ?? $entry->status;
                                $statusClass = $entry->status === \App\Models\AchievementEntry::STATUS_DRAFT
                                    ? 'text-bg-secondary'
                                    : 'text-bg-success';
                                $permissionClass = match ($entry->name_permission_status) {
                                    \App\Models\AchievementEntry::NAME_PERMISSION_GRANTED => 'text-bg-success',
                                    \App\Models\AchievementEntry::NAME_PERMISSION_DENIED => 'text-bg-danger',
                                    default => 'text-bg-secondary',
                                };
                                $canMoveUp = $moveAvailability[$entry->id]['up'] ?? false;
                                $canMoveDown = $moveAvailability[$entry->id]['down'] ?? false;
                            @endphp
                            <tr>
                                <td class="text-break" style="min-width: 12rem;">
                                    <div class="fw-semibold">{{ $entry->full_name }}</div>
                                    <span class="badge text-bg-warning">{{ __('dictt.achievement_private') }}</span>
                                </td>
                                <td class="text-break" style="min-width: 15rem;">
                                    @if ($entry->university_name)
                                        <div class="fw-semibold">{{ $entry->university_name }}</div>
                                    @endif
                                    @if ($entry->department_name)
                                        <div class="small text-muted">{{ $entry->department_name }}</div>
                                    @endif
                                    @if ($entry->description)
                                        <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($entry->description, 100) }}</div>
                                    @endif
                                </td>
                                <td>{{ $branchLabels[$entry->branch] ?? '—' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <form method="POST"
                                            action="{{ route('admin.achievements.entries.name-permission.update', [$achievementYear, $entry]) }}"
                                            class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name_permission_granted" value="0">
                                            <div class="form-check form-switch admin-list-switch mb-0">
                                                <input id="achievement-entry-name-permission-{{ $entry->id }}" type="checkbox"
                                                    class="form-check-input" name="name_permission_granted" value="1" role="switch"
                                                    @checked($entry->name_permission_status === \App\Models\AchievementEntry::NAME_PERMISSION_GRANTED)
                                                    onchange="this.form.submit()"
                                                    aria-label="{{ __('dictt.achievement_name_permission') }}">
                                            </div>
                                            <noscript>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary mt-1">
                                                    {{ __('dictt.update') }}
                                                </button>
                                            </noscript>
                                        </form>
                                        <span class="badge {{ $permissionClass }}">
                                            {{ $permissionLabels[$entry->name_permission_status] ?? $entry->name_permission_status }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <form method="POST"
                                            action="{{ route('admin.achievements.entries.status.update', [$achievementYear, $entry]) }}"
                                            class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="0">
                                            <div class="form-check form-switch admin-list-switch mb-0">
                                                <input id="achievement-entry-status-{{ $entry->id }}" type="checkbox"
                                                    class="form-check-input" name="is_published" value="1" role="switch"
                                                    @checked($entry->status === \App\Models\AchievementEntry::STATUS_PUBLISHED)
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
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.achievements.entries.edit', [$achievementYear, $entry]) }}"
                                            class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        @if ($entryFilter === \App\Models\AchievementEntry::STATUS_DRAFT)
                                            @php $isEntryDeletable = $entry->status === \App\Models\AchievementEntry::STATUS_DRAFT; @endphp
                                            <form id="entry-delete-{{ $entry->id }}" method="POST"
                                                action="{{ route('admin.achievements.entries.destroy', [$achievementYear, $entry]) }}" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm {{ $isEntryDeletable ? 'btn-outline-danger admin-danger-action' : 'btn-outline-secondary' }}"
                                                    @disabled(! $isEntryDeletable)
                                                    title="{{ __('dictt.achievement_entry_permanently_delete') }}"
                                                    data-action-confirmation
                                                    data-confirm-form="entry-delete-{{ $entry->id }}"
                                                    data-confirm-title="{{ __('dictt.achievement_entry_permanently_delete') }}"
                                                    data-confirm-content="{{ __('dictt.achievement_entry_force_delete_confirm', ['name' => $entry->full_name]) }}"
                                                    data-confirm-action="{{ __('dictt.achievement_entry_permanently_delete') }}"
                                                    data-confirm-icon="fa-trash-alt"
                                                    data-confirm-tone="danger">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.achievement_entry_permanently_delete') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.achievements.entries.move', [
                                                'achievementYear' => $achievementYear,
                                                'achievementEntry' => $entry,
                                                'entry_filter' => $entryFilter,
                                            ]) }}" class="d-inline-block">
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
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dictt.achievement_entry_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($entries->hasPages())
                <div class="mt-3">{{ $entries->links() }}</div>
            @endif
        </div>
    </div>

    <x-action-confirmation-modal />
</x-app-layout>
