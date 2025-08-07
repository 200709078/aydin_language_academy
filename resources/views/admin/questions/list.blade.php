<x-app-layout>
    <x-slot name="header">{{ __('dictt.questions') }}</x-slot>
    <livewire:question-list :exercise_id="$exercise_id" :theme_id="$theme_id" />
</x-app-layout>