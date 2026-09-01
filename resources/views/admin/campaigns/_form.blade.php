@php
    $currentCampaign = $campaign ?? null;
    $initialLinkType = old('link_type', $currentCampaign?->link_type ?? \App\Models\Campaign::LINK_TYPE_NONE);
    $linkTypes = [
        \App\Models\Campaign::LINK_TYPE_NONE => __('dictt.campaign_link_none'),
        \App\Models\Campaign::LINK_TYPE_INTERNAL => __('dictt.campaign_link_internal'),
        \App\Models\Campaign::LINK_TYPE_EXTERNAL => __('dictt.campaign_link_external'),
    ];
@endphp

<div class="card" x-data="{ linkType: @js($initialLinkType) }">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-sm-4 mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back') }}
                    </a>
                    <button type="submit" form="campaign-form" class="btn btn-success btn-sm">{{ $submitLabel }}</button>
                </div>
            </div>
            <h5 class="col-sm-4 card-title text-center mb-0">{{ $pageTitle }}</h5>
            <div class="d-none d-sm-block col-sm-4"></div>
        </div>

        <form id="campaign-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title_tr" class="form-label">{{ __('dictt.campaign_title_tr') }}</label>
                    <input id="title_tr" type="text" name="title_tr" value="{{ old('title_tr', $currentCampaign?->title_tr) }}"
                        class="form-control @error('title_tr') is-invalid @enderror" maxlength="255" required>
                    @error('title_tr')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="title_en" class="form-label">{{ __('dictt.campaign_title_en') }}</label>
                    <input id="title_en" type="text" name="title_en" value="{{ old('title_en', $currentCampaign?->title_en) }}"
                        class="form-control @error('title_en') is-invalid @enderror" maxlength="255" required>
                    @error('title_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="description_tr" class="form-label">{{ __('dictt.campaign_description_tr') }}</label>
                    <textarea id="description_tr" name="description_tr" rows="5" class="form-control @error('description_tr') is-invalid @enderror" required>{{ old('description_tr', $currentCampaign?->description_tr) }}</textarea>
                    @error('description_tr')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="description_en" class="form-label">{{ __('dictt.campaign_description_en') }}</label>
                    <textarea id="description_en" name="description_en" rows="5" class="form-control @error('description_en') is-invalid @enderror" required>{{ old('description_en', $currentCampaign?->description_en) }}</textarea>
                    @error('description_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <h6 class="mb-3">{{ __('dictt.campaign_link_type') }}</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="link_type" class="form-label">{{ __('dictt.campaign_link_type') }}</label>
                        <select id="link_type" name="link_type" x-model="linkType"
                            class="form-select @error('link_type') is-invalid @enderror" required>
                            @foreach ($linkTypes as $linkType => $linkTypeLabel)
                                <option value="{{ $linkType }}" @selected($initialLinkType === $linkType)>{{ $linkTypeLabel }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ __('dictt.campaign_link_help') }}</div>
                        @error('link_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3" x-show="linkType === @js(\App\Models\Campaign::LINK_TYPE_INTERNAL)" style="display: none;">
                        <label for="internal_destination" class="form-label">{{ __('dictt.campaign_internal_destination') }}</label>
                        <select id="internal_destination" name="internal_destination"
                            class="form-select @error('internal_destination') is-invalid @enderror">
                            <option value="">{{ __('dictt.none') }}</option>
                            @foreach (\App\Models\Campaign::internalDestinations() as $destination => $destinationLabel)
                                <option value="{{ $destination }}" @selected(old('internal_destination', $currentCampaign?->internal_destination) === $destination)>
                                    {{ $destinationLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('internal_destination')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3" x-show="linkType === @js(\App\Models\Campaign::LINK_TYPE_EXTERNAL)" style="display: none;">
                        <label for="external_url" class="form-label">{{ __('dictt.campaign_external_url') }}</label>
                        <input id="external_url" type="url" name="external_url"
                            value="{{ old('external_url', $currentCampaign?->external_url) }}"
                            class="form-control @error('external_url') is-invalid @enderror" maxlength="2048">
                        @error('external_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
