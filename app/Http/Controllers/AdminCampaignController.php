<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignPageSetting;
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

class AdminCampaignController extends Controller
{
    private const MEDIA_DISK = 'local';

    /**
     * The broad prefix permits the one-time imported legacy hero as well as
     * subsequently uploaded hero images.
     */
    private const MEDIA_PATH_PREFIX = 'campaigns/media-assets/';

    private const HERO_MEDIA_PATH_PREFIX = 'campaigns/media-assets/image';

    /**
     * List all campaigns in their explicit display order.
     */
    public function index(): View
    {
        $campaigns = $this->orderedCampaigns()
            ->paginate(20);
        $moveAvailability = $this->campaignMoveAvailability();

        return view('admin.campaigns.index', compact('campaigns', 'moveAvailability'));
    }

    /**
     * Show the form for a new campaign.
     */
    public function create(): View
    {
        return view('admin.campaigns.create');
    }

    /**
     * Store a draft campaign at the end of the current global order.
     */
    public function store(Request $request): RedirectResponse
    {
        $attributes = $this->validatedCampaign($request);

        $campaign = DB::transaction(function () use ($attributes): Campaign {
            $campaigns = Campaign::query()
                ->lockForUpdate()
                ->get(['id', 'sort_order']);

            return Campaign::create([
                ...$attributes,
                'status' => Campaign::STATUS_DRAFT,
                'sort_order' => ((int) $campaigns->max('sort_order')) + 1,
            ]);
        });

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('success', __('dictt.campaign_created'));
    }

    /**
     * Show the form for one campaign.
     */
    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    /**
     * Update campaign copy and its optional destination without changing state or order.
     */
    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($this->validatedCampaign($request));

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('success', __('dictt.campaign_updated'));
    }

    /**
     * Toggle only the allowed draft/published states from the list screen.
     */
    public function updateStatus(Request $request, Campaign $campaign): RedirectResponse
    {
        $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $campaign->update([
            'status' => $request->boolean('is_published')
                ? Campaign::STATUS_PUBLISHED
                : Campaign::STATUS_DRAFT,
        ]);

        return redirect()->back();
    }

    /**
     * Permanently remove a campaign from the admin list.
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', __('dictt.campaign_deleted'));
    }

    /**
     * Swap a campaign with its immediately adjacent global neighbour.
     */
    public function move(Request $request, Campaign $campaign): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ])['direction'];

        DB::transaction(function () use ($direction, $campaign): void {
            $campaigns = $this->orderedCampaigns()
                ->lockForUpdate()
                ->get();
            $this->ensureCampaignSortOrdersCanBeSwapped($campaigns);

            $currentIndex = $campaigns->search(
                fn (Campaign $orderedCampaign): bool => (int) $orderedCampaign->id === (int) $campaign->id,
            );

            if ($currentIndex === false) {
                return;
            }

            $neighbor = $campaigns->get(
                $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1,
            );

            if ($neighbor === null) {
                return;
            }

            /** @var Campaign $current */
            $current = $campaigns->get($currentIndex);
            $currentSortOrder = (int) $current->sort_order;
            $neighborSortOrder = (int) $neighbor->sort_order;
            $temporarySortOrder = ((int) $campaigns->max('sort_order')) + 1;

            // The temporary unused value makes this safe even if a unique
            // sort-order constraint is introduced later.
            $current->update(['sort_order' => $temporarySortOrder]);
            $neighbor->update(['sort_order' => $currentSortOrder]);
            $current->update(['sort_order' => $neighborSortOrder]);
        });

        return redirect()->back();
    }

    /**
     * Show the singleton settings form for the campaign-page heading and hero image.
     */
    public function settings(): View
    {
        $campaignPageSetting = CampaignPageSetting::query()
            ->with('heroMediaAsset')
            ->orderBy('id')
            ->first() ?? new CampaignPageSetting;

        return view('admin.campaigns.settings', compact('campaignPageSetting'));
    }

    /**
     * Update page titles and optionally replace the right-side hero image.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $attributes = $this->validatedPageSettings($request);
        $heroImage = $request->file('hero_image');
        $shouldRemoveHeroImage = $request->boolean('remove_hero_image');

        DB::transaction(function () use ($attributes, $heroImage, $shouldRemoveHeroImage, $request): void {
            $campaignPageSetting = CampaignPageSetting::query()
                ->lockForUpdate()
                ->orderBy('id')
                ->first() ?? new CampaignPageSetting;

            if ($heroImage instanceof UploadedFile) {
                $attributes['hero_media_asset_id'] = $this->storeHeroMediaAsset(
                    $heroImage,
                    (int) $request->user()->id,
                )->id;
            } elseif ($shouldRemoveHeroImage) {
                $attributes['hero_media_asset_id'] = null;
            }

            $campaignPageSetting->fill($attributes);
            $campaignPageSetting->save();
        });

        return redirect()
            ->route('admin.campaigns.settings')
            ->with('success', __('dictt.campaign_page_settings_updated'));
    }

    /**
     * Stream only the private hero image referenced by the current singleton setting.
     */
    public function media(MediaAsset $mediaAsset)
    {
        $campaignPageSetting = CampaignPageSetting::query()
            ->orderBy('id')
            ->first();
        $path = trim((string) $mediaAsset->path);

        if (
            $campaignPageSetting === null
            || (int) $campaignPageSetting->hero_media_asset_id !== (int) $mediaAsset->getKey()
            || $mediaAsset->kind !== MediaAsset::KIND_IMAGE
            || $mediaAsset->visibility !== MediaAsset::VISIBILITY_PRIVATE
            || $mediaAsset->disk !== self::MEDIA_DISK
            || ! $this->isSafeCampaignMediaPath($path)
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
     * @return array<string, string>
     */
    private function validatedCampaign(Request $request): array
    {
        $validated = $request->validate([
            'title_tr' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_tr' => ['required', 'string', 'max:5000'],
            'description_en' => ['required', 'string', 'max:5000'],
            'link_type' => ['required', Rule::in(Campaign::linkTypes())],
            'internal_destination' => ['nullable', 'string', 'max:100'],
            'external_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $attributes = [
            'title_tr' => $this->requiredTrimmedValue($validated, 'title_tr', 'campaign_title_tr'),
            'title_en' => $this->requiredTrimmedValue($validated, 'title_en', 'campaign_title_en'),
            'description_tr' => $this->requiredTrimmedValue($validated, 'description_tr', 'campaign_description_tr'),
            'description_en' => $this->requiredTrimmedValue($validated, 'description_en', 'campaign_description_en'),
            'link_type' => $validated['link_type'],
        ];
        $internalDestination = $this->nullableTrimmedValue($validated['internal_destination'] ?? null);
        $externalUrl = $this->nullableTrimmedValue($validated['external_url'] ?? null);

        if ($attributes['link_type'] === Campaign::LINK_TYPE_NONE) {
            return [
                ...$attributes,
                'internal_destination' => null,
                'external_url' => null,
            ];
        }

        if ($attributes['link_type'] === Campaign::LINK_TYPE_INTERNAL) {
            if (! Campaign::isAllowedInternalDestination($internalDestination)) {
                throw ValidationException::withMessages([
                    'internal_destination' => __('validation.in', [
                        'attribute' => __('dictt.campaign_internal_destination'),
                    ]),
                ]);
            }

            return [
                ...$attributes,
                'internal_destination' => $internalDestination,
                'external_url' => null,
            ];
        }

        if (! $this->isSecureExternalUrl($externalUrl)) {
            throw ValidationException::withMessages([
                'external_url' => __('dictt.campaign_external_url_https'),
            ]);
        }

        return [
            ...$attributes,
            'internal_destination' => null,
            'external_url' => $externalUrl,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function validatedPageSettings(Request $request): array
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
            'hero_image.required' => __('dictt.campaign_page_hero_image_required'),
            'hero_image.image' => __('dictt.campaign_page_hero_image_invalid'),
            'hero_image.mimes' => __('dictt.campaign_page_hero_image_invalid'),
            'hero_image.max' => __('dictt.campaign_page_hero_image_invalid'),
            'hero_image.uploaded' => __('dictt.campaign_page_hero_image_invalid'),
        ]);

        return [
            'title_tr' => $this->requiredTrimmedValue($validated, 'title_tr', 'campaign_page_title_tr'),
            'title_en' => $this->requiredTrimmedValue($validated, 'title_en', 'campaign_page_title_en'),
            'description_tr' => $this->requiredTrimmedValue($validated, 'description_tr', 'campaign_page_description_tr'),
            'description_en' => $this->requiredTrimmedValue($validated, 'description_en', 'campaign_page_description_en'),
        ];
    }

    private function storeHeroMediaAsset(UploadedFile $file, int $uploadedBy): MediaAsset
    {
        $path = $file->store(self::HERO_MEDIA_PATH_PREFIX, self::MEDIA_DISK);

        if ($path === false) {
            throw new RuntimeException('Kampanya sayfası görseli sunucuya kaydedilemedi.');
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
     * @return Builder<Campaign>
     */
    private function orderedCampaigns(): Builder
    {
        return Campaign::query()
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return array<int, array{up: bool, down: bool}>
     */
    private function campaignMoveAvailability(): array
    {
        $campaignIds = $this->orderedCampaigns()
            ->pluck('id')
            ->all();
        $lastIndex = count($campaignIds) - 1;
        $availability = [];

        foreach ($campaignIds as $index => $campaignId) {
            $availability[(int) $campaignId] = [
                'up' => $index > 0,
                'down' => $index < $lastIndex,
            ];
        }

        return $availability;
    }

    /**
     * Give any pre-existing null, invalid, or duplicate values a stable order
     * before attempting a three-step swap.
     *
     * @param  EloquentCollection<int, Campaign>  $campaigns
     */
    private function ensureCampaignSortOrdersCanBeSwapped(EloquentCollection $campaigns): void
    {
        $seenSortOrders = [];
        $needsNormalization = false;

        foreach ($campaigns as $campaign) {
            $sortOrder = $campaign->sort_order;

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

        $temporaryStart = max(1, (int) $campaigns->max('sort_order')) + $campaigns->count() + 1;

        foreach ($campaigns->values() as $index => $campaign) {
            $campaign->update(['sort_order' => $temporaryStart + $index]);
        }

        foreach ($campaigns->values() as $index => $campaign) {
            $campaign->update(['sort_order' => $index + 1]);
        }
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

    private function nullableTrimmedValue(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function isSecureExternalUrl(?string $url): bool
    {
        if ($url === null) {
            return false;
        }

        $parts = parse_url($url);

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && strtolower((string) ($parts['scheme'] ?? '')) === 'https'
            && isset($parts['host']);
    }

    private function isSafeCampaignMediaPath(string $path): bool
    {
        if (
            $path === ''
            || ! str_starts_with($path, self::MEDIA_PATH_PREFIX)
            || str_starts_with($path, '/')
            || str_contains($path, '\\')
            || str_contains(strtolower($path), '://')
        ) {
            return false;
        }

        return ! in_array('..', explode('/', $path), true);
    }
}
