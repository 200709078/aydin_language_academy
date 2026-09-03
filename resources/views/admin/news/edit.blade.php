<x-app-layout>
    <x-slot name="header">{{ __('dictt.news_edit') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    @include('admin.news._form', [
        'news' => $news,
        'action' => route('admin.news.update', $news),
        'method' => 'PUT',
        'pageTitle' => __('dictt.news_edit'),
        'submitLabel' => __('dictt.save'),
    ])

    @php
        $typeLabels = [
            \App\Models\NewsContentBlock::TYPE_RICH_TEXT => __('dictt.news_block_type_rich_text'),
            \App\Models\NewsContentBlock::TYPE_IMAGE => __('dictt.news_block_type_image'),
            \App\Models\NewsContentBlock::TYPE_AUDIO => __('dictt.news_block_type_audio'),
            \App\Models\NewsContentBlock::TYPE_VIDEO => __('dictt.news_block_type_video'),
            \App\Models\NewsContentBlock::TYPE_FILE => __('dictt.news_block_type_file'),
            \App\Models\NewsContentBlock::TYPE_EXTERNAL_LINK => __('dictt.news_block_type_external_link'),
        ];
    @endphp

    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.news_content_blocks') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.news_content_blocks_help') }}</p>
                </div>
                <a href="{{ route('admin.news.blocks.create', $news) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.news_block_add') }}
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.news_block_position') }}</th>
                            <th scope="col">{{ __('dictt.news_block_type') }}</th>
                            <th scope="col">{{ __('dictt.content') }}</th>
                            <th scope="col">{{ __('dictt.news_block_publication_status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($news->contentBlocks as $block)
                            <tr>
                                <td>{{ $block->position }}</td>
                                <td>{{ $typeLabels[$block->type] ?? $block->type }}</td>
                                <td class="text-break" style="min-width: 18rem;">
                                    @if ($block->heading)
                                        <div class="fw-semibold">{{ $block->heading }}</div>
                                    @endif
                                    @if ($block->type === \App\Models\NewsContentBlock::TYPE_RICH_TEXT)
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($block->body, 180) }}</div>
                                    @elseif ($block->mediaAsset)
                                        <a href="{{ route('admin.news.media.show', $block->mediaAsset) }}" target="_blank" rel="noopener"
                                            class="btn btn-sm btn-outline-secondary mt-1">
                                            <i class="fa fa-up-right-from-square" aria-hidden="true"></i> {{ __('dictt.news_media_open') }}
                                        </a>
                                    @elseif ($block->external_url)
                                        <a href="{{ $block->external_url }}" target="_blank" rel="noopener noreferrer" class="small text-break">
                                            {{ $block->external_url }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <form method="POST" action="{{ route('admin.news.blocks.status.update', [$news, $block]) }}" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="form-check form-switch admin-list-switch mb-0">
                                                <input id="news-block-status-{{ $block->id }}" type="checkbox"
                                                    class="form-check-input" name="is_active" value="1" role="switch"
                                                    @checked($block->is_active)
                                                    onchange="this.form.submit()"
                                                    aria-label="{{ __('dictt.news_block_publication_status') }}"
                                                    title="{{ __('dictt.news_block_publication_status') }}">
                                            </div>
                                            <noscript>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary mt-1" title="{{ __('dictt.update') }}">{{ __('dictt.update') }}</button>
                                            </noscript>
                                        </form>
                                        <span class="badge {{ $block->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $block->is_active ? __('dictt.active') : __('dictt.passive') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.news.blocks.edit', [$news, $block]) }}" class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form id="news-block-delete-{{ $block->id }}" method="POST"
                                            action="{{ route('admin.news.blocks.destroy', [$news, $block]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-primary admin-danger-action" title="{{ __('dictt.delete') }}"
                                                data-action-confirmation
                                                data-confirm-form="news-block-delete-{{ $block->id }}"
                                                data-confirm-title="{{ __('dictt.delete') }}"
                                                data-confirm-content="{{ __('dictt.news_block_delete_confirm') }}"
                                                data-confirm-action="{{ __('dictt.delete') }}"
                                                data-confirm-icon="fa-trash-alt"
                                                data-confirm-tone="danger">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('dictt.delete') }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.news.blocks.move', [$news, $block]) }}"
                                            class="d-inline-block">
                                            @csrf
                                            <div class="btn-group-vertical btn-group-sm" role="group"
                                                aria-label="{{ __('dictt.move_up') }} / {{ __('dictt.move_down') }}">
                                                <button type="submit" name="direction" value="up"
                                                    class="btn btn-outline-secondary px-2 py-0"
                                                    @disabled($loop->first) title="{{ __('dictt.move_up') }}">
                                                    <i class="fa-solid fa-chevron-up fa-xs" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_up') }}</span>
                                                </button>
                                                <button type="submit" name="direction" value="down"
                                                    class="btn btn-outline-secondary px-2 py-0"
                                                    @disabled($loop->last) title="{{ __('dictt.move_down') }}">
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
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dictt.news_block_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-action-confirmation-modal />
</x-app-layout>
