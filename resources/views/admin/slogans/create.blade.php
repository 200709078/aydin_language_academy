<x-app-layout>
    <x-slot name="header">{{ __('dictt.slogan_add') }}</x-slot>

    @include('admin.slogans._form', [
        'action' => route('admin.slogans.store'),
        'method' => 'POST',
        'pageTitle' => __('dictt.slogan_add'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
