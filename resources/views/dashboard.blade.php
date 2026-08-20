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

                    @if (Auth::user()->type === 'admin')
                        <div class="mt-6 flex flex-wrap gap-4">
                            <a href="{{ route('courses_list') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('dictt.courses') }}
                            </a>
                            <a href="{{ route('levels_list') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('dictt.levels') }}
                            </a>
                            <a href="{{ route('themes_list') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('dictt.themes') }}
                            </a>
                            <a href="{{ route('profile.show') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('dictt.profile') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>