@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 w-full relative">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-orange-500 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $title }}
            </h2>
            <button type="button" x-on:click="$dispatch('close')"
                class="text-gray-400 hover:text-red-500 transition" aria-label="{{ __('dictt.close') }}">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="text-gray-700">
            {{ $content }}
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            {{ $footer }}
        </div>
    </div>
</x-modal>
