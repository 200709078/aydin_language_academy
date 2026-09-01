<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_entry_add') }}</x-slot>

    @include('admin.achievements.entries._form', [
        'achievementYear' => $achievementYear,
        'action' => route('admin.achievements.entries.store', $achievementYear),
        'method' => 'POST',
        'pageTitle' => __('dictt.achievement_entry_add'),
        'submitLabel' => __('dictt.add'),
    ])
</x-app-layout>
