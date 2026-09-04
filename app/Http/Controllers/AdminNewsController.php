<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\MediaAsset;
use App\Models\News;
use App\Models\NewsContentBlock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class AdminNewsController extends Controller
{
    private const MEDIA_DISK = 'public';

    private const MEDIA_PATH_PREFIX = 'news/media-assets/';

    private const SOURCE_UPLOAD = 'upload';

    private const SOURCE_EXTERNAL = 'external';
    /**
     * List all non-deleted news records for administrators.
     */
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'all');
        $allowedFilters = [
            'all',
            News::STATUS_DRAFT,
            News::STATUS_PUBLISHED,
            News::STATUS_ARCHIVED,
            'scheduled',
        ];

        if (! in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $search = trim((string) $request->query('q', ''));


        $news = News::query()
            ->with('coverMediaAsset')
            ->withCount('contentBlocks');

        if ($filter === 'scheduled') {
            $news->where('status', News::STATUS_PUBLISHED)
                ->where('published_at', '>', now());
        } elseif ($filter === News::STATUS_PUBLISHED) {
            $news->where('status', News::STATUS_PUBLISHED)
                ->where('published_at', '<=', now());
        } elseif ($filter !== 'all') {
            $news->where('status', $filter);
        }

        if ($search !== '') {
            $news->where(function (Builder $query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($filter === 'all' && $search === '') {
            $news = $news
                ->orderByRaw('sort_order IS NULL')
                ->orderBy('sort_order')
                ->orderBy('id');
        } else {
            $news = $news
                ->orderByRaw("CASE status WHEN 'draft' THEN 0 WHEN 'published' THEN 1 WHEN 'archived' THEN 2 ELSE 3 END")
                ->orderByDesc('updated_at');
        }

        $news = $news
            ->paginate(20)
            ->withQueryString();

        $moveAvailability = $filter === 'all' && $search === ''
            ? $this->newsMoveAvailability()
            : [];

        return view('admin.news.index', compact('filter', 'moveAvailability', 'news', 'search'));
    }

    /**
     * Show the admin form for a new news record.
     */
    public function create(): View
    {
        return view('admin.news.create');
    }

    /**
     * Store one news record and an optional cover image.
     */
    public function store(Request $request): RedirectResponse
    {
        $attributes = $this->validatedNews($request);

        if ($request->hasFile('cover_image')) {
            $attributes['cover_media_asset_id'] = $this->storeMediaAsset(
                $request->file('cover_image'),
                MediaAsset::KIND_IMAGE,
                (int) $request->user()->id,
            )->id;
        }

        $attributes['author_id'] = $request->user()->id;

        if ($attributes['status'] === News::STATUS_PUBLISHED) {
            $attributes['published_by'] = $request->user()->id;
        }

        $attributes['sort_order'] = ((int) News::query()->max('sort_order')) + 1;

        News::create($attributes);

        return redirect()
            ->route('admin.news.index')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => __('dictt.news')]))
            ->with('modalSuccessContent', __('dictt.news_created'));
    }

    /**
     * Show one news record and its ordered content blocks for editing.
     */
    public function edit(News $news): View
    {
        $news->load([
            'coverMediaAsset',
            'contentBlocks.mediaAsset',
        ]);

        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update news metadata and optionally replace or remove the cover image.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $attributes = $this->validatedNews($request, $news);

        if ($request->hasFile('cover_image')) {
            $attributes['cover_media_asset_id'] = $this->storeMediaAsset(
                $request->file('cover_image'),
                MediaAsset::KIND_IMAGE,
                (int) $request->user()->id,
            )->id;
        } elseif ($request->boolean('remove_cover_image')) {
            $attributes['cover_media_asset_id'] = null;
        }

        if (
            $attributes['status'] === News::STATUS_PUBLISHED
            && ($news->status !== News::STATUS_PUBLISHED || $news->published_by === null)
        ) {
            $attributes['published_by'] = $request->user()->id;
        }

        $news->update($attributes);

        return redirect()
            ->route('admin.news.index')
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => __('dictt.news')]))
            ->with('modalSuccessContent', __('dictt.news_updated'));
    }

    /**
     * Swap a news record with its immediately adjacent global neighbour (only for "Tümü" ordering).
     */
    public function move(Request $request, News $news): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ])['direction'];

        DB::transaction(function () use ($direction, $news): void {
            $newsItems = $this->orderedNews()
                ->lockForUpdate()
                ->get();
            $this->ensureNewsSortOrdersCanBeSwapped($newsItems);

            $currentIndex = $newsItems->search(
                fn (News $orderedNews): bool => (int) $orderedNews->id === (int) $news->id,
            );

            if ($currentIndex === false) {
                return;
            }

            $neighbor = $newsItems->get(
                $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1,
            );

            if ($neighbor === null) {
                return;
            }

            /** @var News $current */
            $current = $newsItems->get($currentIndex);
            $currentSortOrder = (int) $current->sort_order;
            $neighborSortOrder = (int) $neighbor->sort_order;
            $temporarySortOrder = ((int) $newsItems->max('sort_order')) + 1;

            $current->update(['sort_order' => $temporarySortOrder]);
            $neighbor->update(['sort_order' => $currentSortOrder]);
            $current->update(['sort_order' => $neighborSortOrder]);
        });

        return redirect()->back();
    }

    /**
     * Move one news record out of the active list without deleting its content.
     */
    public function archive(News $news): RedirectResponse
    {
        $news->update([
            'status' => News::STATUS_ARCHIVED,
        ]);

        return redirect()
            ->route('admin.news.index', ['filter' => News::STATUS_ARCHIVED])
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => __('dictt.news')]))
            ->with('modalSuccessContent', __('dictt.news_archived'));
    }

    /**
     * Permanently delete an archived news record and its content blocks.
     * Uploaded media assets are deliberately retained for separate cleanup.
     */
    public function forceDestroy(News $news): RedirectResponse
    {
        if ($news->status !== News::STATUS_ARCHIVED) {
            abort(404);
        }

        DB::transaction(function () use ($news): void {
            NewsContentBlock::query()
                ->where('news_id', $news->id)
                ->delete();

            $news->forceDelete();
        });

        return redirect()
            ->route('admin.news.index', ['filter' => News::STATUS_ARCHIVED])
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => __('dictt.news')]))
            ->with('modalSuccessContent', __('dictt.news_permanently_deleted'));
    }

    /**
     * Show the form for one new ordered content block.
     */
    public function createBlock(News $news): View
    {
        $nextPosition = $this->nextBlockPosition($news);

        return view('admin.news.blocks.create', compact('news', 'nextPosition'));
    }

    /**
     * Store one rich-text, media, or external-link block.
     */
    public function storeBlock(Request $request, News $news): RedirectResponse
    {
        $attributes = $this->validatedBlock($request);
        $attributes['news_id'] = $news->id;
        $attributes['position'] = $this->nextBlockPosition($news);

        NewsContentBlock::create($attributes);

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Blok']))
            ->with('modalSuccessContent', __('dictt.news_block_created'));
    }

    /**
     * Show the form for one existing content block.
     */
    public function editBlock(News $news, NewsContentBlock $newsContentBlock): View
    {
        $this->ensureBlockBelongsToNews($news, $newsContentBlock);
        $newsContentBlock->load('mediaAsset');

        return view('admin.news.blocks.edit', compact('news', 'newsContentBlock'));
    }

    /**
     * Update one content block without changing its position.
     */
    public function updateBlock(
        Request $request,
        News $news,
        NewsContentBlock $newsContentBlock,
    ): RedirectResponse {
        $this->ensureBlockBelongsToNews($news, $newsContentBlock);

        $newsContentBlock->load('mediaAsset');
        $newsContentBlock->update($this->validatedBlock($request, $newsContentBlock));

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => 'Blok']))
            ->with('modalSuccessContent', __('dictt.news_block_updated'));
    }

    /**
     * Toggle one content block's public visibility from the news edit list.
     */
    public function updateBlockStatus(
        Request $request,
        News $news,
        NewsContentBlock $newsContentBlock,
    ): RedirectResponse {
        $this->ensureBlockBelongsToNews($news, $newsContentBlock);

        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $newsContentBlock->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back();
    }

    /**
     * Delete one block but retain any uploaded source file for deliberate cleanup later.
     */
    public function destroyBlock(News $news, NewsContentBlock $newsContentBlock): RedirectResponse
    {
        $this->ensureBlockBelongsToNews($news, $newsContentBlock);
        $newsContentBlock->delete();

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Blok']))
            ->with('modalSuccessContent', __('dictt.news_block_deleted'));
    }

    /**
     * Move a block one position earlier or later without violating the unique position index.
     */
    public function moveBlock(
        Request $request,
        News $news,
        NewsContentBlock $newsContentBlock,
    ): RedirectResponse {
        $this->ensureBlockBelongsToNews($news, $newsContentBlock);

        $direction = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ])['direction'];

        $moved = DB::transaction(function () use ($direction, $news, $newsContentBlock): bool {
            $block = NewsContentBlock::query()
                ->where('news_id', $news->id)
                ->whereKey($newsContentBlock->id)
                ->lockForUpdate()
                ->firstOrFail();

            $neighborQuery = NewsContentBlock::query()
                ->where('news_id', $news->id);

            if ($direction === 'up') {
                $neighborQuery->where('position', '<', $block->position)
                    ->orderByDesc('position');
            } else {
                $neighborQuery->where('position', '>', $block->position)
                    ->orderBy('position');
            }

            $neighbor = $neighborQuery->lockForUpdate()->first();

            if ($neighbor === null) {
                return false;
            }

            $blockPosition = (int) $block->position;
            $neighborPosition = (int) $neighbor->position;
            $temporaryPosition = ((int) NewsContentBlock::query()
                ->where('news_id', $news->id)
                ->max('position')) + 1;

            $block->update(['position' => $temporaryPosition]);
            $neighbor->update(['position' => $blockPosition]);
            $block->update(['position' => $neighborPosition]);

            return true;
        });

        if ($moved) {
            return redirect()
                ->route('admin.news.edit', $news)
                ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Blok']))
                ->with('modalSuccessContent', __('dictt.news_block_moved'));
        }

        return redirect()
            ->route('admin.news.edit', $news)
            ->with('error', __('dictt.news_block_move_unavailable'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedNews(Request $request, ?News $currentNews = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(News::statuses())],
            'published_at' => [
                Rule::requiredIf($request->input('status') === News::STATUS_PUBLISHED),
                'nullable',
                'date',
            ],
            'unpublished_at' => ['nullable', 'date', 'after:published_at'],
            'display_location' => ['required', Rule::in(News::displayLocations())],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_cover_image' => ['nullable', 'boolean'],
        ], [
            'published_at.required' => __('dictt.news_published_at_required'),
            'unpublished_at.after' => __('dictt.news_unpublished_after'),
            'cover_image.image' => __('dictt.news_cover_image_invalid'),
            'cover_image.mimes' => __('dictt.news_cover_image_invalid'),
            'cover_image.max' => __('dictt.news_upload_max'),
        ]);

        $title = trim((string) $validated['title']);
        if ($title === '') {
            throw ValidationException::withMessages([
                'title' => __('dictt.required_item', ['name' => __('dictt.news_title')]),
            ]);
        }

        $slug = $currentNews?->slug ?: $this->uniqueNewsSlug($title);

        unset($validated['cover_image'], $validated['remove_cover_image']);

        if ($currentNews === null) {
            $validated['seo_title'] = null;
            $validated['seo_description'] = null;
            $validated['canonical_url'] = null;
        }

        return [
            ...$validated,
            'title' => $title,
            'slug' => $slug,
        ];
    }

    private function uniqueNewsSlug(string $title): string
    {
        $maxLength = 191;
        $baseSlug = Str::substr(Str::slug($title), 0, $maxLength);

        if ($baseSlug === '') {
            throw ValidationException::withMessages([
                'slug' => __('dictt.news_slug_invalid'),
            ]);
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (News::withTrashed()->where('slug', $slug)->exists()) {
            $suffixText = '-'.$suffix;
            $slug = Str::substr($baseSlug, 0, $maxLength - strlen($suffixText)).$suffixText;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedBlock(Request $request, ?NewsContentBlock $currentBlock = null): array
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(NewsContentBlock::types())],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'link_label' => ['nullable', 'string', 'max:255'],
            'internal_destination' => ['nullable', 'string', 'max:100'],
        ]);

        $type = $validated['type'];

        if ($currentBlock !== null && $type !== $currentBlock->type) {
            throw ValidationException::withMessages([
                'type' => __('dictt.news_block_type_immutable'),
            ]);
        }

        $attributes = [
            'type' => $type,
            'content_format' => NewsContentBlock::CONTENT_FORMAT_PLAIN,
            'heading' => $this->nullableTrimmedValue($validated['heading'] ?? null),
            'body' => $validated['body'] ?? null,
            'link_label' => $this->nullableTrimmedValue($validated['link_label'] ?? null),
            'is_active' => $currentBlock?->is_active ?? true,
            'metadata' => null,
        ];

        if ($type === NewsContentBlock::TYPE_RICH_TEXT) {
            $body = trim((string) ($validated['body'] ?? ''));

            if ($body === '') {
                throw ValidationException::withMessages([
                    'body' => __('dictt.news_block_body_required'),
                ]);
            }

            return [
                ...$attributes,
                'body' => $body,
                'media_asset_id' => null,
                'external_url' => null,
                'internal_destination' => null,
            ];
        }

        if ($type === NewsContentBlock::TYPE_EXTERNAL_LINK) {
            return [
                ...$attributes,
                'media_asset_id' => null,
                'external_url' => $this->validatedHttpsUrl($request),
                'internal_destination' => null,
            ];
        }

        if ($type === NewsContentBlock::TYPE_INTERNAL_LINK) {
            $internalDestination = $this->nullableTrimmedValue($validated['internal_destination'] ?? null);

            if (! Campaign::isAllowedInternalDestination($internalDestination)) {
                throw ValidationException::withMessages([
                    'internal_destination' => __('validation.in', [
                        'attribute' => __('dictt.campaign_internal_destination'),
                    ]),
                ]);
            }

            return [
                ...$attributes,
                'media_asset_id' => null,
                'external_url' => null,
                'internal_destination' => $internalDestination,
            ];
        }

        $sourceMode = $request->validate([
            'source_mode' => ['required', Rule::in([self::SOURCE_UPLOAD, self::SOURCE_EXTERNAL])],
        ])['source_mode'];

        if ($sourceMode === self::SOURCE_EXTERNAL) {
            $request->validate([
                'media_file' => ['prohibited'],
            ]);

            return [
                ...$attributes,
                'media_asset_id' => null,
                'external_url' => $this->validatedHttpsUrl($request),
                'internal_destination' => null,
            ];
        }

        $needsNewUpload = $currentBlock === null || $currentBlock->media_asset_id === null;
        $mediaFile = $this->validatedMediaFile($request, $type, $needsNewUpload);

        if ($mediaFile !== null) {
            $mediaAssetId = $this->storeMediaAsset(
                $mediaFile,
                $type,
                (int) $request->user()->id,
            )->id;
        } else {
            $mediaAssetId = $currentBlock?->media_asset_id;
        }

        if ($mediaAssetId === null) {
            throw ValidationException::withMessages([
                'media_file' => __('dictt.news_block_media_required'),
            ]);
        }

        return [
            ...$attributes,
            'media_asset_id' => $mediaAssetId,
            'external_url' => null,
            'internal_destination' => null,
        ];
    }

    private function validatedHttpsUrl(Request $request): string
    {
        $validated = $request->validate([
            'external_url' => [
                'required',
                'string',
                'max:2048',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    $url = trim((string) $value);
                    $parts = parse_url($url);

                    if (
                        filter_var($url, FILTER_VALIDATE_URL) === false
                        || strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
                        || ! isset($parts['host'])
                    ) {
                        $fail(__('dictt.news_external_url_https'));
                    }
                },
            ],
        ]);

        return trim($validated['external_url']);
    }

    private function validatedMediaFile(Request $request, string $type, bool $required): ?UploadedFile
    {
        $validated = $request->validate([
            'media_file' => [
                $required ? 'required' : 'nullable',
                ...$this->mediaRules($type),
            ],
        ], $this->mediaMessages());

        return $validated['media_file'] ?? null;
    }

    /**
     * @return list<string>
     */
    private function mediaRules(string $type): array
    {
        return match ($type) {
            NewsContentBlock::TYPE_IMAGE => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            NewsContentBlock::TYPE_AUDIO => ['file', 'mimes:mp3,wav,ogg,m4a,aac', 'max:10240'],
            NewsContentBlock::TYPE_VIDEO => ['file', 'mimes:mp4,webm,ogv', 'max:10240'],
            NewsContentBlock::TYPE_FILE => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv', 'max:10240'],
            default => throw new RuntimeException('Geçersiz medya içerik türü.'),
        };
    }

    /**
     * @return array<string, string>
     */
    private function mediaMessages(): array
    {
        return [
            'media_file.required' => __('dictt.news_block_media_required'),
            'media_file.file' => __('dictt.news_media_file_invalid'),
            'media_file.image' => __('dictt.news_media_file_invalid'),
            'media_file.mimes' => __('dictt.news_media_file_invalid'),
            'media_file.max' => __('dictt.news_upload_max'),
            'media_file.uploaded' => __('dictt.news_media_upload_failed'),
        ];
    }

    private function storeMediaAsset(UploadedFile $file, string $kind, int $uploadedBy): MediaAsset
    {
        $path = $file->store(self::MEDIA_PATH_PREFIX.$kind, self::MEDIA_DISK);

        if ($path === false) {
            throw new RuntimeException('Haber medyası sunucuya kaydedilemedi.');
        }

        $realPath = $file->getRealPath();
        $dimensions = $kind === MediaAsset::KIND_IMAGE && is_string($realPath)
            ? @getimagesize($realPath)
            : false;
        $checksum = is_string($realPath) ? hash_file('sha256', $realPath) : false;

        return MediaAsset::create([
            'disk' => self::MEDIA_DISK,
            'path' => $path,
            'kind' => $kind,
            'visibility' => MediaAsset::VISIBILITY_PUBLIC,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize() ?: 0,
            'width' => is_array($dimensions) ? $dimensions[0] : null,
            'height' => is_array($dimensions) ? $dimensions[1] : null,
            'duration_seconds' => null,
            'checksum' => is_string($checksum) ? $checksum : null,
            'metadata' => null,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    private function ensureBlockBelongsToNews(News $news, NewsContentBlock $newsContentBlock): void
    {
        if ((int) $newsContentBlock->news_id !== (int) $news->id) {
            abort(404);
        }
    }

    private function nextBlockPosition(News $news): int
    {
        return ((int) $news->contentBlocks()->max('position')) + 1;
    }

    /**
     * @return Builder<News>
     */
    private function orderedNews(): Builder
    {
        return News::query()
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return array<int, array{up: bool, down: bool}>
     */
    private function newsMoveAvailability(): array
    {
        $newsIds = $this->orderedNews()
            ->pluck('id')
            ->all();
        $lastIndex = count($newsIds) - 1;
        $availability = [];

        foreach ($newsIds as $index => $newsId) {
            $availability[(int) $newsId] = [
                'up' => $index > 0,
                'down' => $index < $lastIndex,
            ];
        }

        return $availability;
    }

    /**
     * @param  EloquentCollection<int, News>  $newsItems
     */
    private function ensureNewsSortOrdersCanBeSwapped(EloquentCollection $newsItems): void
    {
        $seenSortOrders = [];
        $needsNormalization = false;

        foreach ($newsItems as $news) {
            $sortOrder = $news->sort_order;

            if (
                $sortOrder === null
                || ! is_numeric($sortOrder)
                || (int) $sortOrder < 1
                || isset($seenSortOrders[(int) $sortOrder])
            ) {
                $needsNormalization = true;

                break;
            }

            $seenSortOrders[(int) $sortOrder] = true;
        }

        if (! $needsNormalization) {
            return;
        }

        $temporaryStart = max(1, (int) $newsItems->max('sort_order')) + $newsItems->count() + 1;

        foreach ($newsItems->values() as $index => $news) {
            $news->update(['sort_order' => $temporaryStart + $index]);
        }

        foreach ($newsItems->values() as $index => $news) {
            $news->update(['sort_order' => $index + 1]);
        }
    }

    private function nullableTrimmedValue(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

}
