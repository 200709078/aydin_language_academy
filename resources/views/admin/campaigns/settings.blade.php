<x-app-layout>
    <x-slot name="header">{{ __('dictt.campaign_page_settings') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('dictt.back_short') }}
                        </a>
                        <button type="submit" form="campaign-settings-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.campaign_page_settings_edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>

            <form id="campaign-settings-form" method="POST" action="{{ route('admin.campaigns.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title_tr" class="form-label">{{ __('dictt.campaign_page_title_tr') }}</label>
                        <input id="title_tr" type="text" name="title_tr"
                            value="{{ old('title_tr', $campaignPageSetting?->title_tr) }}"
                            class="form-control @error('title_tr') is-invalid @enderror" maxlength="255" required>
                        @error('title_tr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="title_en" class="form-label">{{ __('dictt.campaign_page_title_en') }}</label>
                        <input id="title_en" type="text" name="title_en"
                            value="{{ old('title_en', $campaignPageSetting?->title_en) }}"
                            class="form-control @error('title_en') is-invalid @enderror" maxlength="255" required>
                        @error('title_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="description_tr" class="form-label">{{ __('dictt.campaign_page_description_tr') }}</label>
                        <textarea id="description_tr" name="description_tr" rows="4"
                            class="form-control @error('description_tr') is-invalid @enderror" required>{{ old('description_tr', $campaignPageSetting?->description_tr) }}</textarea>
                        @error('description_tr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="description_en" class="form-label">{{ __('dictt.campaign_page_description_en') }}</label>
                        <textarea id="description_en" name="description_en" rows="4"
                            class="form-control @error('description_en') is-invalid @enderror" required>{{ old('description_en', $campaignPageSetting?->description_en) }}</textarea>
                        @error('description_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-4">
                        @if ($campaignPageSetting?->heroMediaAsset)
                            <div class="mb-3">
                                <p class="form-label mb-2">{{ __('dictt.page_image_preview') }}</p>
                                <img src="{{ route('admin.campaigns.media.show', $campaignPageSetting->heroMediaAsset) }}"
                                    alt="{{ $campaignPageSetting->localized_title }}" class="img-thumbnail" style="max-width: 16rem; max-height: 16rem;">
                                <div class="mt-2">
                                    <a href="{{ route('admin.campaigns.media.show', $campaignPageSetting->heroMediaAsset) }}" target="_blank"
                                        rel="noopener" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-up-right-from-square" aria-hidden="true"></i> {{ __('dictt.campaign_media_open') }}
                                    </a>
                                </div>
                                <div class="form-check mt-3">
                                    <input id="remove_hero_image" type="checkbox" name="remove_hero_image" value="1"
                                        class="form-check-input" @checked(old('remove_hero_image'))>
                                    <label for="remove_hero_image" class="form-check-label">
                                        {{ __('dictt.campaign_page_remove_hero_image') }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <label for="hero_image" class="form-label">{{ __('dictt.page_image') }}</label>
                        <input id="hero_image" type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp"
                            class="form-control @error('hero_image') is-invalid @enderror">
                        <div class="form-text">{{ __('dictt.campaign_page_hero_image_help') }}</div>
                        @error('hero_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
