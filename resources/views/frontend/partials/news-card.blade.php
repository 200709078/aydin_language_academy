<article class="news-card h-100 w-100">
    <a class="news-card__media" href="{{ route('frontend.news.show', ['news' => $news->slug]) }}" aria-label="{{ $news->title }}">
        @if ($news->coverMediaAsset)
            <img src="{{ $news->coverMediaAsset->publicUrl() }}" alt="{{ $news->title }}">
        @else
            <span class="news-card__media-placeholder" aria-hidden="true"><i class="fa fa-newspaper"></i></span>
        @endif
    </a>

    <div class="news-card__body d-flex flex-column">
        <time class="news-card__date" datetime="{{ $news->published_at?->toDateString() }}">
            <i class="far fa-calendar-alt me-2" aria-hidden="true"></i>{{ $news->published_at?->translatedFormat('d F Y') }}
        </time>
        <h2 class="news-card__title h4">
            <a href="{{ route('frontend.news.show', ['news' => $news->slug]) }}">{{ $news->title }}</a>
        </h2>

        @if (filled($news->excerpt))
            <p class="news-card__excerpt">{{ \Illuminate\Support\Str::limit($news->excerpt, 170) }}</p>
        @endif

        <a class="news-card__link mt-auto" href="{{ route('frontend.news.show', ['news' => $news->slug]) }}">
            {{ __('dictt.news_read_more') }}<i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
        </a>
    </div>
</article>
