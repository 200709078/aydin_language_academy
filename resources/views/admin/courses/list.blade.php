<x-app-layout>
    <x-slot name="header">{{ __('dictt.courses') }}</x-slot>
    @livewire('course-list')
</x-app-layout>