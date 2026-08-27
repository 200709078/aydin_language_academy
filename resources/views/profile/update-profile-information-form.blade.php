<x-form-section class="frontend-profile-form-section" submit="updateProfileInformation" :inline-actions="true">
    <x-slot name="title">
        {{ __('dictt.profileinformation') }}
    </x-slot>

    <x-slot name="description">
        {{ __('dictt.upprfofiledesc') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                @php
                    $profilePhotoUrl = $this->user->profile_photo_path
                        ? $this->user->profile_photo_url
                        : 'https://ui-avatars.com/api/?' . http_build_query([
                            'name' => $this->user->name,
                            'color' => '1B2C51',
                            'background' => 'BFD7FF',
                        ]);
                @endphp

                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('dictt.photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $profilePhotoUrl }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover frontend-profile-avatar">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <button type="button" class="btn btn-outline-primary py-2 px-3 frontend-profile-primary-action mt-2 me-2" x-on:click.prevent="$refs.photo.click()">
                    {{ __('dictt.selectphoto') }}
                </button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('dictt.delphoto') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-6">
            <x-label for="name" value="{{ __('dictt.fullname') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-6">
            <x-label for="email" value="{{ __('dictt.email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <button type="submit" class="btn btn-outline-primary py-3 px-5 frontend-profile-primary-action" wire:loading.attr="disabled" wire:target="photo">
            {{ __('dictt.save') }}
        </button>

        <x-action-message class="ms-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>
    </x-slot>
</x-form-section>
