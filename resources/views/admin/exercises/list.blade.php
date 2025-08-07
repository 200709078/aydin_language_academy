<x-app-layout>
    <x-slot name="header">{{ __('dictt.exercises') }}</x-slot>
    <livewire:exercise-list :theme_id="$theme_id"/>
</x-app-layout>