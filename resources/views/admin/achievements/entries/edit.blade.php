<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_entry_edit') }}</x-slot>


    @include('admin.achievements.entries._form', [
        'achievementYear' => $achievementYear,
        'achievementEntry' => $achievementEntry,
        'action' => route('admin.achievements.entries.update', [$achievementYear, $achievementEntry]),
        'method' => 'PUT',
        'pageTitle' => __('dictt.achievement_entry_edit'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
