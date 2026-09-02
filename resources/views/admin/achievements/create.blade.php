<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_year_add') }}</x-slot>

    @include('admin.achievements._year-form', [
        'action' => route('admin.achievements.store'),
        'method' => 'POST',
        'pageTitle' => __('dictt.achievement_year_add'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
