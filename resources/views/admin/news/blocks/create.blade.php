<x-app-layout>
    <x-slot name="header">{{ __('dictt.news_block_add') }}</x-slot>

    @include('admin.news.blocks._form', [
        'news' => $news,
        'nextPosition' => $nextPosition,
        'action' => route('admin.news.blocks.store', $news),
        'method' => 'POST',
        'pageTitle' => __('dictt.news_block_add'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
