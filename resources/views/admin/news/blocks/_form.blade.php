@php
    $currentBlock = $newsContentBlock ?? null;
    $initialType = old('type', $currentBlock?->type ?? \App\Models\NewsContentBlock::TYPE_RICH_TEXT);
    $initialSourceMode = old(
        'source_mode',
        $currentBlock?->media_asset_id
            ? 'upload'
            : ($currentBlock?->external_url ? 'external' : 'upload'),
    );
    $typeOptions = [
        \App\Models\NewsContentBlock::TYPE_RICH_TEXT => __('dictt.news_block_type_rich_text'),
        \App\Models\NewsContentBlock::TYPE_IMAGE => __('dictt.news_block_type_image'),
        \App\Models\NewsContentBlock::TYPE_AUDIO => __('dictt.news_block_type_audio'),
        \App\Models\NewsContentBlock::TYPE_VIDEO => __('dictt.news_block_type_video'),
        \App\Models\NewsContentBlock::TYPE_FILE => __('dictt.news_block_type_file'),
        \App\Models\NewsContentBlock::TYPE_EXTERNAL_LINK => __('dictt.news_block_type_external_link'),
    ];
    $hasExistingAsset = $currentBlock?->media_asset_id !== null;
@endphp

<div class="card" x-data="{
    type: @js($initialType),
    sourceMode: @js($initialSourceMode),
    hasExistingAsset: @js($hasExistingAsset),
    get isMedia() {
        return ['image', 'audio', 'video', 'file'].includes(this.type);
    },
    get isExternalLink() {
        return this.type === 'external_link';
    },
    get needsExternalUrl() {
        return this.isExternalLink || (this.isMedia && this.sourceMode === 'external');
    },
    get accepts() {
        return {
            image: '.jpg,.jpeg,.png,.webp',
            audio: '.mp3,.wav,.ogg,.m4a,.aac',
            video: '.mp4,.webm,.ogv',
            file: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv',
        }[this.type] ?? '';
    }
}">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="news-block-form" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="news-block-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="{{ $currentBlock ? 'type-label' : 'type' }}" class="form-label">{{ __('dictt.news_block_type') }}</label>
                    @if ($currentBlock)
                        <input id="type-label" type="text" class="form-control" value="{{ $typeOptions[$currentBlock->type] }}" readonly>
                        <input id="type" type="hidden" name="type" value="{{ $currentBlock->type }}">
                        <div class="form-text">{{ __('dictt.news_block_type_immutable') }}</div>
                    @else
                        <select id="type" name="type" x-model="type" class="form-select @error('type') is-invalid @enderror" required>
                            @foreach ($typeOptions as $typeValue => $typeLabel)
                                <option value="{{ $typeValue }}" @selected((string) $initialType === (string) $typeValue)>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="heading" class="form-label">{{ __('dictt.news_block_heading') }}</label>
                <input id="heading" type="text" name="heading" value="{{ old('heading', $currentBlock?->heading) }}"
                    class="form-control @error('heading') is-invalid @enderror" maxlength="255">
                @error('heading')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="body" class="form-label" x-text="type === 'rich_text' ? @js(__('dictt.news_block_body')) : @js(__('dictt.news_block_description'))"></label>
                <textarea id="body" name="body" rows="{{ $currentBlock?->type === \App\Models\NewsContentBlock::TYPE_RICH_TEXT ? 9 : 4 }}"
                    x-bind:required="type === 'rich_text'" class="form-control @error('body') is-invalid @enderror">{{ old('body', $currentBlock?->body) }}</textarea>
                <div class="form-text" x-show="type === 'rich_text'">{{ __('dictt.news_block_plain_text_help') }}</div>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div x-show="isMedia" style="display: none;" class="border rounded p-3 mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="source_mode" class="form-label">{{ __('dictt.news_block_source') }}</label>
                        <select id="source_mode" name="source_mode" x-model="sourceMode" x-bind:disabled="!isMedia"
                            class="form-select @error('source_mode') is-invalid @enderror">
                            <option value="upload">{{ __('dictt.news_block_source_upload') }}</option>
                            <option value="external">{{ __('dictt.news_block_source_external') }}</option>
                        </select>
                        @error('source_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3" x-show="sourceMode === 'upload'" style="display: none;">
                        <label for="media_file" class="form-label">{{ __('dictt.news_block_media_file') }}</label>
                        <input id="media_file" type="file" name="media_file" x-bind:accept="accepts"
                            x-bind:disabled="!isMedia || sourceMode !== 'upload'"
                            x-bind:required="isMedia && sourceMode === 'upload' && !hasExistingAsset"
                            class="form-control @error('media_file') is-invalid @enderror">
                        <div class="form-text">{{ __('dictt.news_upload_max') }}</div>
                        @error('media_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if ($currentBlock?->mediaAsset)
                    <div class="mb-3">
                        <div class="small fw-semibold mb-2">{{ __('dictt.news_block_media_current') }}</div>
                        @if ($currentBlock->type === \App\Models\NewsContentBlock::TYPE_IMAGE)
                            <img src="{{ $currentBlock->mediaAsset->publicUrl() }}" alt="{{ $currentBlock->heading ?? '' }}"
                                class="img-thumbnail" style="max-width: 16rem; max-height: 10rem;">
                        @elseif ($currentBlock->type === \App\Models\NewsContentBlock::TYPE_AUDIO)
                            <audio controls class="w-100">
                                <source src="{{ $currentBlock->mediaAsset->publicUrl() }}" type="{{ $currentBlock->mediaAsset->mime_type }}">
                            </audio>
                        @elseif ($currentBlock->type === \App\Models\NewsContentBlock::TYPE_VIDEO)
                            <video controls class="w-100" style="max-width: 32rem;">
                                <source src="{{ $currentBlock->mediaAsset->publicUrl() }}" type="{{ $currentBlock->mediaAsset->mime_type }}">
                            </video>
                        @else
                            <a href="{{ $currentBlock->mediaAsset->publicUrl() }}" target="_blank" rel="noopener"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-up-right-from-square" aria-hidden="true"></i> {{ __('dictt.news_media_open') }}
                            </a>
                        @endif
                        <div class="form-text">{{ __('dictt.news_block_media_retain') }}</div>
                    </div>
                @endif
            </div>

            <div x-show="needsExternalUrl" style="display: none;" class="mb-4">
                <label for="external_url" class="form-label">{{ __('dictt.news_external_url') }}</label>
                <input id="external_url" type="url" name="external_url" value="{{ old('external_url', $currentBlock?->external_url) }}"
                    x-bind:disabled="!needsExternalUrl" x-bind:required="needsExternalUrl"
                    class="form-control @error('external_url') is-invalid @enderror" maxlength="2048" placeholder="https://">
                <div class="form-text">{{ __('dictt.news_external_url_https') }}</div>
                @error('external_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div x-show="type !== 'rich_text'" style="display: none;" class="mb-4">
                <label for="link_label" class="form-label">{{ __('dictt.news_block_link_label') }}</label>
                <input id="link_label" type="text" name="link_label" value="{{ old('link_label', $currentBlock?->link_label) }}"
                    x-bind:disabled="type === 'rich_text'" class="form-control @error('link_label') is-invalid @enderror" maxlength="255">
                @error('link_label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </form>
    </div>
</div>
