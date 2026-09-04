@php
    $newsPageTitle = trim((string) ($news->seo_title ?: $news->title));
    $newsPageDescription = trim((string) ($news->seo_description ?: $news->excerpt ?: $news->title));
    $newsCanonicalUrl = $news->canonical_url ?: route('frontend.news.show', ['news' => $news->slug]);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>{{ $newsPageTitle }} | {{ __('dictt.ala') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="{{ $newsPageDescription }}" name="description">
    <link rel="canonical" href="{{ $newsCanonicalUrl }}">

    @if ($news->coverMediaAsset)
        <meta property="og:image" content="{{ $news->coverMediaAsset->publicUrl() }}">
    @endif

    <link href="{{ asset('frontend/images/logo/favicon.png') }}" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/style.css') }}?v=20260904-news-nocrop-1" rel="stylesheet">
</head>

<body>
    @include('frontend.partials.header')

    <main class="container-xxl py-5 ala-news-detail">
        <div class="container">
            <div class="mx-auto" style="max-width: 920px;">
                <div class="news-detail__heading mb-5">
                    <a class="news-detail__back btn btn-sm btn-outline-primary" href="{{ route('frontend.news.index') }}">
                        <i class="fa fa-arrow-left me-2" aria-hidden="true"></i>{{ __('dictt.news_back_to_list') }}
                    </a>
                    <h2 class="h1 mb-0">{{ __('dictt.news_detail') }}</h2>
                </div>

                <article class="news-detail-card wow fadeInUp" data-wow-delay="0.1s">
                    @if ($news->coverMediaAsset)
                        <figure class="news-detail-card__cover mb-0">
                            <img src="{{ $news->coverMediaAsset->publicUrl() }}" alt="{{ $news->title }}">
                        </figure>
                    @endif

                    <div class="news-detail-card__body">
                        <time class="news-card__date" datetime="{{ $news->published_at?->toDateString() }}">
                            <i class="far fa-calendar-alt me-2" aria-hidden="true"></i>{{ $news->published_at?->translatedFormat('d F Y') }}
                        </time>
                        <h1>{{ $news->title }}</h1>

                        @if (filled($news->excerpt))
                            <p class="news-detail-card__excerpt">{{ $news->excerpt }}</p>
                        @endif

                        <div class="news-detail-card__blocks">
                            @foreach ($news->activeContentBlocks as $block)
                                @php
                                    $mediaAsset = $block->mediaAsset;
                                    $externalUrl = trim((string) $block->external_url);
                                    $hasSecureExternalUrl = $externalUrl !== ''
                                        && filter_var($externalUrl, FILTER_VALIDATE_URL) !== false
                                        && str_starts_with(strtolower($externalUrl), 'https://');
                                    $internalLinkUrl = $block->publicInternalLinkUrl();
                                @endphp

                                <section class="news-detail-block news-detail-block--{{ $block->type }}">
                                    @if (filled($block->heading))
                                        <h2 class="h4">{{ $block->heading }}</h2>
                                    @endif

                                    @if ($block->type === \App\Models\NewsContentBlock::TYPE_RICH_TEXT)
                                        <div class="news-detail-block__text">{!! nl2br(e($block->body)) !!}</div>
                                    @elseif ($block->type === \App\Models\NewsContentBlock::TYPE_IMAGE)
                                        @if ($mediaAsset && $mediaAsset->kind === \App\Models\MediaAsset::KIND_IMAGE)
                                            <img class="news-detail-block__image" src="{{ $mediaAsset->publicUrl() }}" alt="{{ $block->heading ?: $news->title }}">
                                        @elseif ($hasSecureExternalUrl)
                                            <img class="news-detail-block__image" src="{{ $externalUrl }}" alt="{{ $block->heading ?: $news->title }}">
                                        @endif
                                    @elseif ($block->type === \App\Models\NewsContentBlock::TYPE_AUDIO)
                                        @if ($mediaAsset && $mediaAsset->kind === \App\Models\MediaAsset::KIND_AUDIO)
                                            <audio class="news-detail-block__audio" controls preload="metadata">
                                                <source src="{{ $mediaAsset->publicUrl() }}" type="{{ $mediaAsset->mime_type }}">
                                            </audio>
                                        @elseif ($hasSecureExternalUrl)
                                            <audio class="news-detail-block__audio" controls preload="metadata">
                                                <source src="{{ $externalUrl }}">
                                            </audio>
                                        @endif
                                    @elseif ($block->type === \App\Models\NewsContentBlock::TYPE_VIDEO)
                                        @if ($mediaAsset && $mediaAsset->kind === \App\Models\MediaAsset::KIND_VIDEO)
                                            <video class="news-detail-block__video" controls preload="metadata">
                                                <source src="{{ $mediaAsset->publicUrl() }}" type="{{ $mediaAsset->mime_type }}">
                                            </video>
                                        @elseif ($hasSecureExternalUrl)
                                            <video class="news-detail-block__video" controls preload="metadata">
                                                <source src="{{ $externalUrl }}">
                                            </video>
                                        @endif
                                    @elseif ($block->type === \App\Models\NewsContentBlock::TYPE_FILE)
                                        @if ($mediaAsset && $mediaAsset->kind === \App\Models\MediaAsset::KIND_FILE)
                                            <a class="btn btn-outline-primary" href="{{ $mediaAsset->publicUrl() }}" target="_blank" rel="noopener noreferrer">{{ $block->link_label ?: $mediaAsset->original_filename ?: __('dictt.news_file_open') }}<i class="fa fa-file-download ms-2" aria-hidden="true"></i></a>
                                        @elseif ($hasSecureExternalUrl)
                                            <a class="btn btn-outline-primary" href="{{ $externalUrl }}" target="_blank" rel="noopener noreferrer">{{ $block->link_label ?: __('dictt.news_file_open') }}<i class="fa fa-external-link-alt ms-2" aria-hidden="true"></i></a>
                                        @endif
                                    @elseif ($block->type === \App\Models\NewsContentBlock::TYPE_EXTERNAL_LINK && $hasSecureExternalUrl)
                                        <a class="btn btn-outline-primary" href="{{ $externalUrl }}" target="_blank" rel="noopener noreferrer">{{ $block->link_label ?: __('dictt.news_external_open') }}<i class="fa fa-external-link-alt ms-2" aria-hidden="true"></i></a>
                                    @elseif ($block->type === \App\Models\NewsContentBlock::TYPE_INTERNAL_LINK && $internalLinkUrl)
                                        <a class="btn btn-outline-primary" href="{{ $internalLinkUrl }}">{{ $block->link_label ?: __('dictt.news_internal_open') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i></a>
                                    @endif

                                    @if ($block->type !== \App\Models\NewsContentBlock::TYPE_RICH_TEXT && filled($block->body))
                                        <p class="news-detail-block__description">{{ $block->body }}</p>
                                    @endif
                                </section>
                            @endforeach
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </main>

    @include('frontend.partials.footer')

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/easing/easing.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('frontend/vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}?v=20260830-news-2"></script>
</body>

</html>
