<x-app-layout>
    <x-slot name="header">
        {{ __('dictt.home') }}
    </x-slot>

    <div>
        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ __('Hoş geldiniz,') }} {{ Auth::user()->name }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Hesabınıza giriş yaptınız.
                    </p>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>