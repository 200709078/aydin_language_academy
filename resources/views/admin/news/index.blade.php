<x-app-layout>
    <x-slot name="header">{{ __('dictt.news') }}</x-slot>

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
        $filters = [
            'all' => __('dictt.news_filter_all'),
            \App\Models\News::STATUS_DRAFT => __('dictt.news_status_draft'),
            \App\Models\News::STATUS_PUBLISHED => __('dictt.news_status_published'),
            'scheduled' => __('dictt.news_status_scheduled'),
            \App\Models\News::STATUS_ARCHIVED => __('dictt.news_status_archived'),
        ];
        $statusLabels = [
            \App\Models\News::STATUS_DRAFT => __('dictt.news_status_draft'),
            \App\Models\News::STATUS_PUBLISHED => __('dictt.news_status_published'),
            \App\Models\News::STATUS_ARCHIVED => __('dictt.news_status_archived'),
        ];
        $displayLabels = [
            \App\Models\News::DISPLAY_NONE => __('dictt.news_display_none'),
            \App\Models\News::DISPLAY_HOMEPAGE => __('dictt.news_display_homepage'),
            \App\Models\News::DISPLAY_HERO => __('dictt.news_display_hero'),
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <h5 class="card-title mb-0">{{ __('dictt.news') }}</h5>
                <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.news_add') }}
                </a>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                @foreach ($filters as $filterKey => $filterLabel)
                    <a href="{{ route('admin.news.index', ['filter' => $filterKey]) }}"
                        class="btn btn-sm {{ $filter === $filterKey ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $filterLabel }}
                    </a>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.news_title') }}</th>
                            <th scope="col">{{ __('dictt.news_status') }}</th>
                            <th scope="col">{{ __('dictt.news_display_location') }}</th>
                            <th scope="col">{{ __('dictt.news_content_blocks') }}</th>
                            <th scope="col">{{ __('dictt.news_published_at') }}</th>
                            <th scope="col">{{ __('dictt.updated_at') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($news as $item)
                            @php
                                $isScheduled = $item->status === \App\Models\News::STATUS_PUBLISHED
                                    && $item->published_at?->isFuture();
                                $statusLabel = $isScheduled
                                    ? __('dictt.news_status_scheduled')
                                    : $statusLabels[$item->status] ?? $item->status;
                                $statusClass = match ($item->status) {
                                    \App\Models\News::STATUS_DRAFT => 'text-bg-secondary',
                                    \App\Models\News::STATUS_ARCHIVED => 'text-bg-dark',
                                    default => $isScheduled ? 'text-bg-info' : 'text-bg-success',
                                };
                            @endphp
                            <tr>
                                <td class="text-break">
                                    <div class="fw-semibold">{{ Illuminate\Support\Str::limit($item->title, 50) }}</div>
                                </td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>{{ $displayLabels[$item->display_location] ?? $item->display_location }}</td>
                                <td>{{ $item->content_blocks_count }}</td>
                                <td class="text-nowrap">{{ $item->published_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td class="text-nowrap">{{ $item->updated_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        @if ($filter === \App\Models\News::STATUS_ARCHIVED)
                                            <form id="news-force-delete-{{ $item->id }}" method="POST"
                                                action="{{ route('admin.news.force-destroy', $item) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-primary admin-danger-action"
                                                    title="{{ __('dictt.news_permanently_delete') }}"
                                                    data-action-confirmation
                                                    data-confirm-form="news-force-delete-{{ $item->id }}"
                                                    data-confirm-title="{{ __('dictt.news_permanently_delete') }}"
                                                    data-confirm-content="{{ __('dictt.news_force_delete_confirm', ['title' => $item->title]) }}"
                                                    data-confirm-action="{{ __('dictt.news_permanently_delete') }}"
                                                    data-confirm-icon="fa-trash-alt"
                                                    data-confirm-tone="danger">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.news_permanently_delete') }}</span>
                                                </button>
                                            </form>
                                        @elseif ($item->status === \App\Models\News::STATUS_ARCHIVED)
                                            <a href="{{ route('admin.news.index', ['filter' => \App\Models\News::STATUS_ARCHIVED]) }}"
                                                class="btn btn-sm btn-outline-secondary" title="{{ __('dictt.news_archive') }}">
                                                <i class="fa fa-archive" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('dictt.news_archive') }}</span>
                                            </a>
                                        @else
                                            <form id="news-archive-{{ $item->id }}" method="POST"
                                                action="{{ route('admin.news.archive', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    title="{{ __('dictt.news_archive_action') }}"
                                                    data-action-confirmation
                                                    data-confirm-form="news-archive-{{ $item->id }}"
                                                    data-confirm-title="{{ __('dictt.news_archive_action') }}"
                                                    data-confirm-content="{{ __('dictt.news_archive_confirm', ['title' => $item->title]) }}"
                                                    data-confirm-action="{{ __('dictt.news_archive_action') }}"
                                                    data-confirm-icon="fa-archive"
                                                    data-confirm-tone="neutral">
                                                    <i class="fa fa-archive" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.news_archive_action') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($filter === 'all' && $search === '')
                                            @php
                                                $canMoveUp = $moveAvailability[$item->id]['up'] ?? false;
                                                $canMoveDown = $moveAvailability[$item->id]['down'] ?? false;
                                            @endphp
                                            <form method="POST" action="{{ route('admin.news.move', $item) }}" class="d-inline-block">
                                                @csrf
                                                <div class="btn-group-vertical btn-group-sm" role="group" aria-label="{{ __('dictt.move_up') }} / {{ __('dictt.move_down') }}">
                                                    <button type="submit" name="direction" value="up" class="btn btn-outline-secondary px-2 py-0" @disabled(! $canMoveUp) title="{{ __('dictt.move_up') }}">
                                                        <i class="fa-solid fa-chevron-up fa-xs" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.move_up') }}</span>
                                                    </button>
                                                    <button type="submit" name="direction" value="down" class="btn btn-outline-secondary px-2 py-0" @disabled(! $canMoveDown) title="{{ __('dictt.move_down') }}">
                                                        <i class="fa-solid fa-chevron-down fa-xs" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ __('dictt.move_down') }}</span>
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('dictt.news_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($news->hasPages())
                <div class="mt-3">{{ $news->links() }}</div>
            @endif
        </div>
        </div>

    <x-action-confirmation-modal />
</x-app-layout>
