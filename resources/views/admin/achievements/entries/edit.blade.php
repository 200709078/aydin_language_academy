<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_entry_edit') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    @include('admin.achievements.entries._form', [
        'achievementYear' => $achievementYear,
        'achievementEntry' => $achievementEntry,
        'action' => route('admin.achievements.entries.update', [$achievementYear, $achievementEntry]),
        'method' => 'PUT',
        'pageTitle' => __('dictt.achievement_entry_edit'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
