<x-action-section class="frontend-profile-action-section">
    <x-slot name="title">
        {{ __('dictt.deleteaccount') }}
    </x-slot>

    <x-slot name="description">
        {{ __('dictt.deleteaccountdesc') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('dictt.deleteaccountonce') }}
        </div>

        <div class="mt-5">
            <button type="button" class="btn btn-outline-primary frontend-profile-primary-action frontend-profile-danger-action py-3 px-5"
                wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('dictt.delete_my_account') }}
            </button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('dictt.deleteaccount') }}
            </x-slot>

            <x-slot name="content">
                {{ __('dictt.deleteaccountsure') }}

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('dictt.password') }}"
                                x-ref="password"
                                wire:model="password"
                                wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('dictt.cancel') }}
                </x-secondary-button>

                <button type="button" class="btn btn-outline-primary frontend-profile-primary-action frontend-profile-danger-action py-2 px-3 ms-3"
                    wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('dictt.delete_my_account') }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
