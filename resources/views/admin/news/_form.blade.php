@php
    $currentNews = $news ?? null;
    $isEditing = $currentNews !== null;
    $initialStatus = old('status', $currentNews?->status ?? \App\Models\News::STATUS_DRAFT);
    $initialDisplayLocation = old('display_location', $currentNews?->display_location ?? \App\Models\News::DISPLAY_NONE);
    $publishedAt = old('published_at', $currentNews?->published_at?->format('Y-m-d\TH:i'));
    $unpublishedAt = old('unpublished_at', $currentNews?->unpublished_at?->format('Y-m-d\TH:i'));
    $statusOptions = [
        \App\Models\News::STATUS_DRAFT => __('dictt.news_status_draft'),
        \App\Models\News::STATUS_PUBLISHED => __('dictt.news_status_published'),
        \App\Models\News::STATUS_ARCHIVED => __('dictt.news_status_archived'),
    ];
    $displayOptions = [
        \App\Models\News::DISPLAY_NONE => __('dictt.news_display_none'),
        \App\Models\News::DISPLAY_HOMEPAGE => __('dictt.news_display_homepage'),
        \App\Models\News::DISPLAY_HERO => __('dictt.news_display_hero'),
    ];
@endphp

<div class="card" x-data="{ status: @js($initialStatus) }">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                @if ($isEditing)
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                        </a>
                        <button type="submit" form="news-form" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
                    </div>
                @else
                    <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                    </a>
                @endif
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
                <div class="col-md-8 mb-3">
                    <label for="title" class="form-label">{{ __('dictt.news_title') }}</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $currentNews?->title) }}"
                        class="form-control @error('title') is-invalid @enderror" maxlength="255" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="slug" class="form-label">{{ __('dictt.news_slug') }}</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $currentNews?->slug) }}"
                        class="form-control @error('slug') is-invalid @enderror" maxlength="191">
                    <div class="form-text">{{ __('dictt.news_slug_help') }}</div>
                    @error('slug')
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
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">{{ __('dictt.news_status') }}</label>
                        <select id="status" name="status" x-model="status"
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
                    <div class="col-md-4 mb-3">
                        <label for="display_location" class="form-label">{{ __('dictt.news_display_location') }}</label>
                        <select id="display_location" name="display_location"
                            class="form-select @error('display_location') is-invalid @enderror" required>
                            @foreach ($displayOptions as $locationValue => $locationLabel)
                                <option value="{{ $locationValue }}" @selected((string) $initialDisplayLocation === (string) $locationValue)>
                                    {{ $locationLabel }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ __('dictt.news_display_help') }}</div>
                        @error('display_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sort_order" class="form-label">{{ __('dictt.news_sort_order') }}</label>
                        <input id="sort_order" type="number" min="0" name="sort_order"
                            value="{{ old('sort_order', $currentNews?->sort_order) }}"
                            class="form-control @error('sort_order') is-invalid @enderror">
                        <div class="form-text">{{ __('dictt.news_sort_order_help') }}</div>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row" x-show="status === @js(\App\Models\News::STATUS_PUBLISHED)" style="display: none;">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="published_at" class="form-label">{{ __('dictt.news_published_at') }}</label>
                        <input id="published_at" type="datetime-local" name="published_at" value="{{ $publishedAt }}"
                            x-bind:required="status === @js(\App\Models\News::STATUS_PUBLISHED)"
                            class="form-control @error('published_at') is-invalid @enderror">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="unpublished_at" class="form-label">{{ __('dictt.news_unpublished_at') }}</label>
                        <input id="unpublished_at" type="datetime-local" name="unpublished_at" value="{{ $unpublishedAt }}"
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
                        <img src="{{ route('admin.news.media.show', $currentNews->coverMediaAsset) }}"
                            alt="{{ $currentNews->title }}" class="img-thumbnail" style="max-width: 16rem; max-height: 10rem;">
                        <div class="mt-2">
                            <a href="{{ route('admin.news.media.show', $currentNews->coverMediaAsset) }}" target="_blank"
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

            <details class="border rounded p-3 mb-4">
                <summary class="fw-semibold">{{ __('dictt.news_seo') }}</summary>
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label for="seo_title" class="form-label">{{ __('dictt.news_seo_title') }}</label>
                        <input id="seo_title" type="text" name="seo_title" value="{{ old('seo_title', $currentNews?->seo_title) }}"
                            class="form-control @error('seo_title') is-invalid @enderror" maxlength="255">
                        @error('seo_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="canonical_url" class="form-label">{{ __('dictt.news_canonical_url') }}</label>
                        <input id="canonical_url" type="url" name="canonical_url"
                            value="{{ old('canonical_url', $currentNews?->canonical_url) }}"
                            class="form-control @error('canonical_url') is-invalid @enderror" maxlength="2048">
                        @error('canonical_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="seo_description" class="form-label">{{ __('dictt.news_seo_description') }}</label>
                        <textarea id="seo_description" name="seo_description" rows="3" maxlength="320"
                            class="form-control @error('seo_description') is-invalid @enderror">{{ old('seo_description', $currentNews?->seo_description) }}</textarea>
                        @error('seo_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </details>

            @unless ($isEditing)
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
                </div>
            @endunless
        </form>
    </div>
</div>
