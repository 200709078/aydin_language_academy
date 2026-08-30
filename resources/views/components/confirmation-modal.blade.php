@props(['id' => null, 'maxWidth' => null])

<x-review-action-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="content">{{ $content }}</x-slot>
    <x-slot name="footer">{{ $footer }}</x-slot>
</x-review-action-modal>
