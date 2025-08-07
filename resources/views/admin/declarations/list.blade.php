<x-app-layout>
    <x-slot name="header">{{ __('dictt.declarations') }}</x-slot>
    <livewire:declaration-list :theme_id="$theme_id" />
</x-app-layout>