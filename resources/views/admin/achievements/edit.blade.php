<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_year_edit') }}</x-slot>


    @include('admin.achievements._year-form', [
        'achievementYear' => $achievementYear,
        'action' => route('admin.achievements.update', $achievementYear),
        'method' => 'PUT',
        'pageTitle' => __('dictt.achievement_year_edit'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
