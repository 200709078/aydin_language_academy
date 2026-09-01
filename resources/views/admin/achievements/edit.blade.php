<x-app-layout>
    <x-slot name="header">{{ __('dictt.achievement_year_edit') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    @include('admin.achievements._year-form', [
        'achievementYear' => $achievementYear,
        'action' => route('admin.achievements.update', $achievementYear),
        'method' => 'PUT',
        'pageTitle' => __('dictt.achievement_year_edit'),
        'submitLabel' => __('dictt.update'),
    ])
</x-app-layout>
