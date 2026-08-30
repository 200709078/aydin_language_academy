<x-app-layout>
    <x-slot name="header">{{ __('dictt.news_add') }}</x-slot>

    @include('admin.news._form', [
        'action' => route('admin.news.store'),
        'method' => 'POST',
        'pageTitle' => __('dictt.news_add'),
        'submitLabel' => __('dictt.add'),
    ])
</x-app-layout>
