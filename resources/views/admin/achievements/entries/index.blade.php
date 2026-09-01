<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_entries') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
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
            <div class="d-flex flex-nowrap align-items-center justify-content-between gap-2 mb-4">
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.achievement_back') }}
                    </a>
                </div>
                <div class="flex-grow-1 text-center px-2" style="min-width: 0;">
                    <h5 class="card-title mb-1">{{ __('dictt.achievement_entries') }}</h5>
                    <p class="text-muted small mb-0 text-break">
                        {{ $achievementYear->year }} — {{ $achievementYear->title }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.achievements.entries.create', $achievementYear) }}" class="btn btn-sm btn-primary">
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
                            <th scope="col">{{ __('dictt.achievement_name_permission') }}</th>
                            <th scope="col">{{ __('dictt.achievement_entry_placement') }}</th>
                            <th scope="col">{{ __('dictt.branch') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
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
                                <td>
                                    <span class="badge {{ $permissionClass }}">
                                        {{ $permissionLabels[$entry->name_permission_status] ?? $entry->name_permission_status }}
                                    </span>
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
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.achievements.entries.edit', [$achievementYear, $entry]) }}"
                                            class="btn btn-sm btn-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.achievements.entries.status.update', [$achievementYear, $entry]) }}"
                                            class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="0">
                                            <div class="form-check form-switch mb-0">
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
                                        <div class="d-flex flex-column gap-1">
                                            <form method="POST" action="{{ route('admin.achievements.entries.move', [
                                                'achievementYear' => $achievementYear,
                                                'achievementEntry' => $entry,
                                                'entry_filter' => $entryFilter,
                                            ]) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    @disabled(! $canMoveUp) title="{{ __('dictt.move_up') }}">
                                                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_up') }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.achievements.entries.move', [
                                                'achievementYear' => $achievementYear,
                                                'achievementEntry' => $entry,
                                                'entry_filter' => $entryFilter,
                                            ]) }}">
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
</x-app-layout>
