<x-app-layout>
    <x-slot name="header">{{ __('dictt.slogan_edit') }}</x-slot>

    @include('admin.slogans._form', [
        'slogan' => $slogan,
        'action' => route('admin.slogans.update', $slogan),
        'method' => 'PUT',
        'pageTitle' => __('dictt.slogan_edit'),
        'submitLabel' => __('dictt.update'),
    ])
</x-app-layout>
