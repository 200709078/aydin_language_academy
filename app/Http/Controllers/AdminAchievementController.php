<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementEntry;
use App\Models\AchievementPageSetting;
use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class AdminAchievementController extends Controller
{
    private const MEDIA_DISK = 'local';

    private const MEDIA_PATH_PREFIX = 'achievements/media-assets/';

    private const SHARED_CAMPAIGN_MEDIA_PATH_PREFIX = 'campaigns/media-assets/';

    private const HERO_MEDIA_PATH_PREFIX = 'achievements/media-assets/image';

    /**
     * List annual Başarı Tablosu records for administrators.
     */
    public function index(): View
    {
        $years = $this->orderedAchievements()
            ->withCount('entries')
            ->paginate(20);
        $moveAvailability = $this->achievementMoveAvailability();

        return view('admin.achievements.index', compact('years', 'moveAvailability'));
    }

    /**
     * Show the singleton settings for the public achievements-page heading and hero image.
     */
    public function settings(): View
    {
        $achievementPageSetting = AchievementPageSetting::query()
            ->with('heroMediaAsset')
            ->orderBy('id')
            ->first() ?? new AchievementPageSetting;

        return view('admin.achievements.settings', compact('achievementPageSetting'));
    }

    /**
     * Update page copy and optionally replace the right-side hero image.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $attributes = $this->validatedAchievementPageSettings($request);
        $heroImage = $request->file('hero_image');
        $shouldRemoveHeroImage = $request->boolean('remove_hero_image');

        DB::transaction(function () use ($attributes, $heroImage, $shouldRemoveHeroImage, $request): void {
            $achievementPageSetting = AchievementPageSetting::query()
                ->lockForUpdate()
                ->orderBy('id')
                ->first() ?? new AchievementPageSetting;

            if ($heroImage instanceof UploadedFile) {
                $attributes['hero_media_asset_id'] = $this->storeAchievementHeroMediaAsset(
                    $heroImage,
                    (int) $request->user()->id,
                )->id;
            } elseif ($shouldRemoveHeroImage) {
                $attributes['hero_media_asset_id'] = null;
            }

            $achievementPageSetting->fill($attributes);
            $achievementPageSetting->save();
        });

        return redirect()
            ->route('admin.achievements.settings')
            ->with('success', __('dictt.achievement_page_settings_updated'));
    }

    /**
     * Stream only the private hero image assigned to the current page setting.
     */
    public function media(MediaAsset $mediaAsset)
    {
        $achievementPageSetting = AchievementPageSetting::query()
            ->orderBy('id')
            ->first();
        $path = trim((string) $mediaAsset->path);

        if (
            $achievementPageSetting === null
            || (int) $achievementPageSetting->hero_media_asset_id !== (int) $mediaAsset->getKey()
            || $mediaAsset->kind !== MediaAsset::KIND_IMAGE
            || $mediaAsset->visibility !== MediaAsset::VISIBILITY_PRIVATE
            || $mediaAsset->disk !== self::MEDIA_DISK
            || ! $this->isSafeAchievementMediaPath($path)
        ) {
            abort(404);
        }

        $disk = Storage::disk(self::MEDIA_DISK);

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, $mediaAsset->original_filename, [
            'Content-Type' => $mediaAsset->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Show the form for a new annual achievement.
     */
    public function create(): View
    {
        return view('admin.achievements.create');
    }

    /**
     * Store a new annual achievement.
     */
    public function store(Request $request): RedirectResponse
    {
        $attributes = $this->validatedAchievement($request);
        $attributes['status'] = Achievement::STATUS_DRAFT;
        $attributes['sort_order'] = ((int) Achievement::query()->max('sort_order')) + 1;
        $achievement = Achievement::create($attributes);

        return redirect()
            ->route('admin.achievements.edit', $achievement)
            ->with('success', __('dictt.achievement_year_created'));
    }

    /**
     * Show the form for an annual achievement.
     */
    public function edit(Achievement $achievementYear): View
    {
        return view('admin.achievements.edit', compact('achievementYear'));
    }

    /**
     * List student records belonging to one achievement.
     */
    public function entriesIndex(Request $request, Achievement $achievementYear): View
    {
        $entryFilter = $this->entryFilter($request);
        $entries = $this->orderedAchievementEntries($achievementYear);

        if ($entryFilter !== 'all') {
            $entries->where('status', $entryFilter);
        }

        $entries = $entries
            ->paginate(30, ['*'], 'entries_page')
            ->withQueryString();
        $moveAvailability = $this->achievementEntryMoveAvailability($achievementYear, $entryFilter);

        return view('admin.achievements.entries.index', compact(
            'achievementYear',
            'entries',
            'entryFilter',
            'moveAvailability',
        ));
    }

    /**
     * Update an annual achievement.
     */
    public function update(Request $request, Achievement $achievementYear): RedirectResponse
    {
        $achievementYear->update($this->validatedAchievement($request, $achievementYear));

        return redirect()
            ->route('admin.achievements.edit', $achievementYear)
            ->with('success', __('dictt.achievement_year_updated'));
    }

    /**
     * Update the publication status directly from the achievement list.
     */
    public function updateStatus(Request $request, Achievement $achievementYear): RedirectResponse
    {
        $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $achievementYear->update([
            'status' => $request->boolean('is_published')
                ? Achievement::STATUS_PUBLISHED
                : Achievement::STATUS_DRAFT,
        ]);

        return redirect()->back();
    }

    /**
     * Move one achievement one position earlier or later in the global sort order.
     */
    public function move(Request $request, Achievement $achievementYear): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ])['direction'];

        DB::transaction(function () use ($direction, $achievementYear): void {
            $orderedAchievements = $this->orderedAchievements()
                ->lockForUpdate()
                ->get();
            $currentIndex = $orderedAchievements->search(
                fn (Achievement $achievement): bool => (int) $achievement->id === (int) $achievementYear->id,
            );

            if ($currentIndex === false) {
                return;
            }

            $neighbor = $orderedAchievements->get(
                $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1,
            );

            if ($neighbor === null) {
                return;
            }

            $this->ensureAchievementSortOrdersCanBeSwapped($orderedAchievements);

            /** @var Achievement $current */
            $current = $orderedAchievements->get($currentIndex);
            $currentSortOrder = (int) $current->sort_order;
            $neighborSortOrder = (int) $neighbor->sort_order;
            $temporarySortOrder = ((int) $orderedAchievements->max('sort_order')) + 1;

            $current->update(['sort_order' => $temporarySortOrder]);
            $neighbor->update(['sort_order' => $currentSortOrder]);
            $current->update(['sort_order' => $neighborSortOrder]);
        });

        return redirect()->back();
    }

    /**
     * Show the form for one new entry belonging to an achievement.
     */
    public function createEntry(Achievement $achievementYear): View
    {
        return view('admin.achievements.entries.create', compact('achievementYear'));
    }

    /**
     * Store one student record for an achievement.
     */
    public function storeEntry(Request $request, Achievement $achievementYear): RedirectResponse
    {
        $attributes = $this->validatedEntry($request);
        $attributes['achievements_id'] = $achievementYear->id;
        $attributes['status'] = AchievementEntry::STATUS_DRAFT;
        $attributes['sort_order'] = ((int) $achievementYear->entries()->max('sort_order')) + 1;
        $achievementEntry = AchievementEntry::create($attributes);

        return redirect()
            ->route('admin.achievements.entries.edit', [$achievementYear, $achievementEntry])
            ->with('success', __('dictt.achievement_entry_created'));
    }

    /**
     * Show one entry for editing.
     */
    public function editEntry(
        Achievement $achievementYear,
        AchievementEntry $achievementEntry,
    ): View {
        $this->ensureEntryBelongsToAchievement($achievementYear, $achievementEntry);

        return view('admin.achievements.entries.edit', compact('achievementYear', 'achievementEntry'));
    }

    /**
     * Update one entry in an achievement.
     */
    public function updateEntry(
        Request $request,
        Achievement $achievementYear,
        AchievementEntry $achievementEntry,
    ): RedirectResponse {
        $this->ensureEntryBelongsToAchievement($achievementYear, $achievementEntry);
        $achievementEntry->update($this->validatedEntry($request));

        return redirect()
            ->route('admin.achievements.entries.edit', [$achievementYear, $achievementEntry])
            ->with('success', __('dictt.achievement_entry_updated'));
    }

    /**
     * Update one student record's publication status directly from its list.
     */
    public function updateEntryStatus(
        Request $request,
        Achievement $achievementYear,
        AchievementEntry $achievementEntry,
    ): RedirectResponse {
        $this->ensureEntryBelongsToAchievement($achievementYear, $achievementEntry);

        $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $achievementEntry->update([
            'status' => $request->boolean('is_published')
                ? AchievementEntry::STATUS_PUBLISHED
                : AchievementEntry::STATUS_DRAFT,
        ]);

        return redirect()->back();
    }

    /**
     * Update one student record's name publication permission directly from its list.
     */
    public function updateEntryNamePermission(
        Request $request,
        Achievement $achievementYear,
        AchievementEntry $achievementEntry,
    ): RedirectResponse {
        $this->ensureEntryBelongsToAchievement($achievementYear, $achievementEntry);

        $request->validate([
            'name_permission_granted' => ['required', 'boolean'],
        ]);

        $achievementEntry->update([
            'name_permission_status' => $request->boolean('name_permission_granted')
                ? AchievementEntry::NAME_PERMISSION_GRANTED
                : AchievementEntry::NAME_PERMISSION_DENIED,
        ]);

        return redirect()->back();
    }

    /**
     * Move one student record one position earlier or later in its visible list.
     */
    public function moveEntry(
        Request $request,
        Achievement $achievementYear,
        AchievementEntry $achievementEntry,
    ): RedirectResponse {
        $this->ensureEntryBelongsToAchievement($achievementYear, $achievementEntry);

        $direction = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ])['direction'];
        $entryFilter = $this->entryFilter($request);

        DB::transaction(function () use ($direction, $entryFilter, $achievementYear, $achievementEntry): void {
            $orderedEntries = $this->orderedAchievementEntries($achievementYear)
                ->lockForUpdate()
                ->get();
            $this->ensureAchievementEntrySortOrdersCanBeSwapped($orderedEntries);

            $visibleEntries = $entryFilter === 'all'
                ? $orderedEntries
                : $orderedEntries->where('status', $entryFilter)->values();
            $currentIndex = $visibleEntries->search(
                fn (AchievementEntry $entry): bool => (int) $entry->id === (int) $achievementEntry->id,
            );

            if ($currentIndex === false) {
                return;
            }

            $neighbor = $visibleEntries->get(
                $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1,
            );

            if ($neighbor === null) {
                return;
            }

            /** @var AchievementEntry $current */
            $current = $visibleEntries->get($currentIndex);
            $currentSortOrder = (int) $current->sort_order;
            $neighborSortOrder = (int) $neighbor->sort_order;
            $temporarySortOrder = ((int) $orderedEntries->max('sort_order')) + 1;

            $current->update(['sort_order' => $temporarySortOrder]);
            $neighbor->update(['sort_order' => $currentSortOrder]);
            $current->update(['sort_order' => $neighborSortOrder]);
        });

        return redirect()->back();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedAchievement(Request $request, ?Achievement $achievement = null): array
    {
        return $request->validate([
            'year' => [
                'required',
                'integer',
                'between:1900,9999',
                Rule::unique('achievements', 'year')->ignore($achievement?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    /**
     * @return array<string, string|null>
     */
    private function validatedAchievementPageSettings(Request $request): array
    {
        $validated = $request->validate([
            'title_tr' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_tr' => ['required', 'string', 'max:5000'],
            'description_en' => ['required', 'string', 'max:5000'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'hero_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ], [
            'hero_image.required' => __('dictt.achievement_page_hero_image_required'),
            'hero_image.image' => __('dictt.achievement_page_hero_image_invalid'),
            'hero_image.mimes' => __('dictt.achievement_page_hero_image_invalid'),
            'hero_image.max' => __('dictt.achievement_page_hero_image_invalid'),
            'hero_image.uploaded' => __('dictt.achievement_page_hero_image_invalid'),
        ]);

        return [
            'title_tr' => $this->requiredTrimmedValue($validated, 'title_tr', 'achievement_page_title_tr'),
            'title_en' => $this->requiredTrimmedValue($validated, 'title_en', 'achievement_page_title_en'),
            'description_tr' => $this->requiredTrimmedValue($validated, 'description_tr', 'achievement_page_description_tr'),
            'description_en' => $this->requiredTrimmedValue($validated, 'description_en', 'achievement_page_description_en'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedEntry(Request $request): array
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'university_name' => ['nullable', 'string', 'max:255'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'branch' => ['nullable', Rule::in(AchievementEntry::BRANCHES)],
            'card_sub_title' => ['nullable', 'string', 'max:100'],
            'name_permission_granted' => ['required', 'boolean'],
        ]);

        $hasNamePublicationPermission = $request->boolean('name_permission_granted');
        unset($validated['name_permission_granted']);

        $validated['name_permission_status'] = $hasNamePublicationPermission
            ? AchievementEntry::NAME_PERMISSION_GRANTED
            : AchievementEntry::NAME_PERMISSION_DENIED;

        return $validated;
    }

    private function ensureEntryBelongsToAchievement(
        Achievement $achievement,
        AchievementEntry $achievementEntry,
    ): void {
        if ((int) $achievementEntry->achievements_id !== (int) $achievement->id) {
            abort(404);
        }
    }

    /**
     * @return Builder<Achievement>
     */
    private function orderedAchievements(): Builder
    {
        return Achievement::query()
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderByDesc('year')
            ->orderByDesc('id');
    }

    /**
     * @return array<int, array{up: bool, down: bool}>
     */
    private function achievementMoveAvailability(): array
    {
        return $this->moveAvailabilityForIds($this->orderedAchievements()->pluck('id')->all());
    }

    /**
     * Give legacy null or duplicate values a stable sequence before swapping two rows.
     *
     * @param  EloquentCollection<int, Achievement>  $achievements
     */
    private function ensureAchievementSortOrdersCanBeSwapped(EloquentCollection $achievements): void
    {
        $seenSortOrders = [];

        foreach ($achievements as $achievement) {
            $sortOrder = $achievement->sort_order;

            if ($sortOrder === null || isset($seenSortOrders[$sortOrder])) {
                foreach ($achievements->values() as $index => $sortableAchievement) {
                    $sortableAchievement->update(['sort_order' => $index + 1]);
                }

                return;
            }

            $seenSortOrders[$sortOrder] = true;
        }
    }

    /**
     * @return Builder<AchievementEntry>
     */
    private function orderedAchievementEntries(Achievement $achievementYear): Builder
    {
        return AchievementEntry::query()
            ->where('achievements_id', $achievementYear->id)
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return array<int, array{up: bool, down: bool}>
     */
    private function achievementEntryMoveAvailability(Achievement $achievementYear, string $entryFilter): array
    {
        $entries = $this->orderedAchievementEntries($achievementYear);

        if ($entryFilter !== 'all') {
            $entries->where('status', $entryFilter);
        }

        return $this->moveAvailabilityForIds($entries->pluck('id')->all());
    }

    /**
     * @param  list<int>  $recordIds
     * @return array<int, array{up: bool, down: bool}>
     */
    private function moveAvailabilityForIds(array $recordIds): array
    {
        $lastIndex = count($recordIds) - 1;
        $availability = [];

        foreach ($recordIds as $index => $recordId) {
            $availability[(int) $recordId] = [
                'up' => $index > 0,
                'down' => $index < $lastIndex,
            ];
        }

        return $availability;
    }

    /**
     * Give legacy null or duplicate values a stable sequence before swapping two rows.
     *
     * @param  EloquentCollection<int, AchievementEntry>  $entries
     */
    private function ensureAchievementEntrySortOrdersCanBeSwapped(EloquentCollection $entries): void
    {
        $seenSortOrders = [];

        foreach ($entries as $entry) {
            $sortOrder = $entry->sort_order;

            if ($sortOrder === null || isset($seenSortOrders[$sortOrder])) {
                foreach ($entries->values() as $index => $sortableEntry) {
                    $sortableEntry->update(['sort_order' => $index + 1]);
                }

                return;
            }

            $seenSortOrders[$sortOrder] = true;
        }
    }

    private function entryFilter(Request $request): string
    {
        $filter = (string) $request->query('entry_filter', 'all');
        $allowedFilters = [
            'all',
            AchievementEntry::STATUS_DRAFT,
            AchievementEntry::STATUS_PUBLISHED,
        ];

        return in_array($filter, $allowedFilters, true) ? $filter : 'all';
    }

    private function storeAchievementHeroMediaAsset(UploadedFile $file, int $uploadedBy): MediaAsset
    {
        $path = $file->store(self::HERO_MEDIA_PATH_PREFIX, self::MEDIA_DISK);

        if ($path === false) {
            throw new RuntimeException('Başarılarımız sayfası görseli sunucuya kaydedilemedi.');
        }

        $realPath = $file->getRealPath();
        $dimensions = is_string($realPath) ? @getimagesize($realPath) : false;
        $checksum = is_string($realPath) ? hash_file('sha256', $realPath) : false;

        return MediaAsset::create([
            'disk' => self::MEDIA_DISK,
            'path' => $path,
            'kind' => MediaAsset::KIND_IMAGE,
            'visibility' => MediaAsset::VISIBILITY_PRIVATE,
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

    /**
     * The page may temporarily share the campaign hero, then switch to its
     * own achievement-specific upload without exposing unrelated private media.
     */
    private function isSafeAchievementMediaPath(string $path): bool
    {
        if (
            $path === ''
            || (! str_starts_with($path, self::MEDIA_PATH_PREFIX)
                && ! str_starts_with($path, self::SHARED_CAMPAIGN_MEDIA_PATH_PREFIX))
            || str_starts_with($path, '/')
            || str_contains($path, '\\')
            || str_contains(strtolower($path), '://')
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function requiredTrimmedValue(array $validated, string $attribute, string $label): string
    {
        $value = trim((string) ($validated[$attribute] ?? ''));

        if ($value === '') {
            throw ValidationException::withMessages([
                $attribute => __('dictt.required_item', ['name' => __('dictt.'.$label)]),
            ]);
        }

        return $value;
    }
}
