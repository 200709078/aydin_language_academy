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
        <x-review-action-modal wire:model.live="confirmingUserDeletion">
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
                <button type="button" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                    <i class="fa fa-ban mr-1"></i> {{ __('dictt.cancel') }}
                </button>

                <button type="button" wire:click="deleteUser" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                    <i class="fa fa-trash-alt mr-1"></i> {{ __('dictt.delete_my_account') }}
                </button>
            </x-slot>
        </x-review-action-modal>
    </x-slot>
</x-action-section>
