<x-app-layout>
    <x-slot name="header">{{ __('dictt.news_block_edit') }}</x-slot>

    @include('admin.news.blocks._form', [
        'news' => $news,
        'newsContentBlock' => $newsContentBlock,
        'nextPosition' => $newsContentBlock->position,
        'action' => route('admin.news.blocks.update', [$news, $newsContentBlock]),
        'method' => 'PUT',
        'pageTitle' => __('dictt.news_block_edit'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
