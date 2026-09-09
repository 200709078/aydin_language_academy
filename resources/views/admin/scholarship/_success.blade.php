@if (session('modalSuccessTitle') && session('modalSuccessContent'))
    <div class="relative bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow mb-6 w-full" role="status">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                {{ session('modalSuccessTitle') }}
            </h2>
            <button type="button" onclick="this.parentElement.parentElement.remove()"
                class="text-gray-500 hover:text-red-600 ml-4" aria-label="{{ __('dictt.close') }}">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <div class="mt-2 text-sm">{{ session('modalSuccessContent') }}</div>
    </div>
@endif
