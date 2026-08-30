<x-app-layout>
    <x-slot name="header">{{ __('dictt.news') }}</x-slot>

    <div
        x-data="{
            confirmationOpen: false,
            confirmationFormId: null,
            confirmationTitle: '',
            confirmationContent: '',
            confirmationActionLabel: '',
            confirmationIsDanger: false,
            openConfirmation(formId, title, content, actionLabel, isDanger) {
                this.confirmationFormId = formId;
                this.confirmationTitle = title;
                this.confirmationContent = content;
                this.confirmationActionLabel = actionLabel;
                this.confirmationIsDanger = isDanger;
                this.confirmationOpen = true;
            },
            submitConfirmation() {
                const form = document.getElementById(this.confirmationFormId);

                if (!form) {
                    return;
                }

                this.confirmationOpen = false;
                form.submit();
            }
        }"
    >
        @if (session('success') || session('error'))
            <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                {{ session('success') ?? session('error') }}
                <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="{{ __('dictt.close') }}"></button>
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
                <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.news_add') }}
                </a>
            </div>

            <div class="d-flex flex-column gap-3 mb-4">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($filters as $filterKey => $filterLabel)
                        <a href="{{ route('admin.news.index', array_filter(['filter' => $filterKey, 'q' => $search], static fn ($value): bool => $value !== '')) }}"
                            class="btn btn-sm {{ $filter === $filterKey ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $filterLabel }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.news.index') }}" class="row g-2 align-items-center">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <div class="col-sm-8 col-md-6 col-lg-5">
                        <label for="news-search" class="visually-hidden">{{ __('dictt.search') }}</label>
                        <input id="news-search" type="search" name="q" value="{{ $search }}" class="form-control"
                            placeholder="{{ __('dictt.news_search_placeholder') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-magnifying-glass" aria-hidden="true"></i> {{ __('dictt.search') }}
                        </button>
                    </div>
                    @if ($search !== '')
                        <div class="col-auto">
                            <a href="{{ route('admin.news.index', ['filter' => $filter]) }}" class="btn btn-sm btn-link">
                                {{ __('dictt.clear') }}
                            </a>
                        </div>
                    @endif
                </form>
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
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    <div class="small text-muted">/{{ $item->slug }}</div>
                                </td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>{{ $displayLabels[$item->display_location] ?? $item->display_location }}</td>
                                <td>{{ $item->content_blocks_count }}</td>
                                <td class="text-nowrap">{{ $item->published_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td class="text-nowrap">{{ $item->updated_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        @if ($filter === \App\Models\News::STATUS_ARCHIVED)
                                            <form id="news-force-delete-{{ $item->id }}" method="POST"
                                                action="{{ route('admin.news.force-destroy', $item) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    title="{{ __('dictt.news_permanently_delete') }}"
                                                    data-confirm-form="news-force-delete-{{ $item->id }}"
                                                    data-confirm-title="{{ __('dictt.news_permanently_delete') }}"
                                                    data-confirm-content="{{ __('dictt.news_force_delete_confirm', ['title' => $item->title]) }}"
                                                    data-confirm-action="{{ __('dictt.news_permanently_delete') }}"
                                                    x-on:click="openConfirmation($el.dataset.confirmForm, $el.dataset.confirmTitle, $el.dataset.confirmContent, $el.dataset.confirmAction, true)">
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
                                                    data-confirm-form="news-archive-{{ $item->id }}"
                                                    data-confirm-title="{{ __('dictt.news_archive_action') }}"
                                                    data-confirm-content="{{ __('dictt.news_archive_confirm', ['title' => $item->title]) }}"
                                                    data-confirm-action="{{ __('dictt.news_archive_action') }}"
                                                    x-on:click="openConfirmation($el.dataset.confirmForm, $el.dataset.confirmTitle, $el.dataset.confirmContent, $el.dataset.confirmAction, false)">
                                                    <i class="fa fa-archive" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.news_archive_action') }}</span>
                                                </button>
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

        <div
            x-show="confirmationOpen"
            x-on:keydown.escape.window="confirmationOpen = false"
            class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            x-bind:aria-label="confirmationTitle"
        >
            <div x-show="confirmationOpen" class="fixed inset-0 transform transition-all"
                x-on:click="confirmationOpen = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div x-show="confirmationOpen"
                class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
                x-on:click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 w-full relative">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-orange-500 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span x-text="confirmationTitle"></span>
                        </h2>
                        <button type="button" x-on:click="confirmationOpen = false"
                            class="text-gray-400 hover:text-red-500 transition" aria-label="{{ __('dictt.close') }}">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div class="text-gray-700" x-text="confirmationContent"></div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" x-on:click="confirmationOpen = false"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                            <i class="fa fa-ban mr-1"></i> {{ __('dictt.cancel') }}
                        </button>
                        <button type="button" x-on:click="submitConfirmation()"
                            class="px-4 py-2 text-white rounded-md transition"
                            x-bind:class="confirmationIsDanger ? 'bg-red-600 hover:bg-red-700' : 'bg-secondary hover:bg-gray-700'">
                            <i class="fa mr-1" x-bind:class="confirmationIsDanger ? 'fa-trash-alt' : 'fa-archive'"></i>
                            <span x-text="confirmationActionLabel"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
