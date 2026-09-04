@php
    $currentNews = $news ?? null;
    $isEditing = $currentNews !== null;
    $initialStatus = old('status', $currentNews?->status ?? \App\Models\News::STATUS_DRAFT);
    $initialDisplayLocation = old('display_location', $currentNews?->display_location ?? \App\Models\News::DISPLAY_HOMEPAGE);
    $publishedAt = old('published_at', $currentNews?->published_at?->format('Y-m-d\TH:i'));
    $unpublishedAt = old('unpublished_at', $currentNews?->unpublished_at?->format('Y-m-d\TH:i'));
    $statusOptions = [
        \App\Models\News::STATUS_DRAFT => __('dictt.news_status_draft'),
        \App\Models\News::STATUS_PUBLISHED => __('dictt.news_status_published'),
        \App\Models\News::STATUS_ARCHIVED => __('dictt.news_status_archived'),
    ];
    $displayOptions = [
        \App\Models\News::DISPLAY_HOMEPAGE => __('dictt.news_display_homepage'),
        \App\Models\News::DISPLAY_HERO => __('dictt.news_display_hero'),
    ];
@endphp

<div class="card" x-data="{
    status: @js($initialStatus),
    publishedAt: @js($publishedAt),
    setPublishedAtToNow() {
        if (this.publishedAt) {
            return;
        }

        const now = new Date();
        const pad = (value) => String(value).padStart(2, '0');

        this.publishedAt = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    },
    openDatePicker(event) {
        const input = event.currentTarget;

        if (typeof input.showPicker === 'function') {
            input.showPicker();
        }
    },
}">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                    <button type="submit" form="news-form" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="news-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="title" class="form-label">{{ __('dictt.news_title') }}</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $currentNews?->title) }}"
                        class="form-control @error('title') is-invalid @enderror" maxlength="255" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="excerpt" class="form-label">{{ __('dictt.news_excerpt') }}</label>
                <textarea id="excerpt" name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror"
                    maxlength="2000">{{ old('excerpt', $currentNews?->excerpt) }}</textarea>
                @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="mb-3">{{ __('dictt.news_publication_settings') }}</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">{{ __('dictt.news_status') }}</label>
                        <select id="status" name="status" x-model="status"
                            x-on:change="if ($event.target.value === @js(\App\Models\News::STATUS_PUBLISHED)) setPublishedAtToNow()"
                            class="form-select @error('status') is-invalid @enderror" required>
                            @foreach ($statusOptions as $statusValue => $statusLabel)
                                <option value="{{ $statusValue }}" @selected((string) $initialStatus === (string) $statusValue)>
                                    {{ $statusLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="display_location" class="form-label">{{ __('dictt.news_display_location') }}</label>
                        <select id="display_location" name="display_location"
                            class="form-select @error('display_location') is-invalid @enderror" required>
                        @foreach ($displayOptions as $locationValue => $locationLabel)
                            <option value="{{ $locationValue }}" @selected((string) $initialDisplayLocation === (string) $locationValue)>
                                {{ $locationLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('display_location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    </div>
                </div>

                <div class="row" x-show="status === @js(\App\Models\News::STATUS_PUBLISHED)" style="display: none;">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="published_at" class="form-label">{{ __('dictt.news_published_at') }}</label>
                        <input id="published_at" type="datetime-local" name="published_at" value="{{ $publishedAt }}" x-model="publishedAt"
                            x-bind:required="status === @js(\App\Models\News::STATUS_PUBLISHED)"
                            x-on:click="openDatePicker($event)" x-on:keydown.prevent x-on:paste.prevent x-on:drop.prevent
                            inputmode="none"
                            class="form-control @error('published_at') is-invalid @enderror">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="unpublished_at" class="form-label">{{ __('dictt.news_unpublished_at') }}</label>
                        <input id="unpublished_at" type="datetime-local" name="unpublished_at" value="{{ $unpublishedAt }}"
                            x-on:click="openDatePicker($event)" x-on:keydown.prevent x-on:paste.prevent x-on:drop.prevent
                            inputmode="none"
                            class="form-control @error('unpublished_at') is-invalid @enderror">
                        @error('unpublished_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="mb-3">{{ __('dictt.news_cover_image') }}</h6>
                @if ($currentNews?->coverMediaAsset)
                    <div class="mb-3">
                        <img src="{{ $currentNews->coverMediaAsset->publicUrl() }}"
                            alt="{{ $currentNews->title }}" class="img-thumbnail" style="max-width: 16rem; max-height: 10rem;">
                        <div class="mt-2">
                            <a href="{{ $currentNews->coverMediaAsset->publicUrl() }}" target="_blank"
                                rel="noopener" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-up-right-from-square" aria-hidden="true"></i> {{ __('dictt.news_media_open') }}
                            </a>
                        </div>
                    </div>
                @endif
                <label for="cover_image" class="form-label">{{ __('dictt.news_cover_image') }}</label>
                <input id="cover_image" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp"
                    class="form-control @error('cover_image') is-invalid @enderror">
                <div class="form-text">{{ __('dictt.news_upload_max') }}</div>
                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if ($currentNews?->coverMediaAsset)
                    <div class="form-check mt-2">
                        <input id="remove_cover_image" type="hidden" name="remove_cover_image" value="0">
                        <input id="remove_cover_image_checkbox" class="form-check-input" type="checkbox" name="remove_cover_image" value="1"
                            @checked(old('remove_cover_image'))>
                        <label for="remove_cover_image_checkbox" class="form-check-label">{{ __('dictt.news_cover_remove') }}</label>
                    </div>
                @endif
            </div>

        </form>
    </div>
</div>
