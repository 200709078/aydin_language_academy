<x-app-layout>
    <x-slot name="header">{{ __('scholarship.'.$meta['title']) }}</x-slot>

    @include('admin.scholarship.definitions._form', [
        'action' => route('admin.scholarship.definitions.store', ['type' => $type]),
        'method' => 'POST',
        'pageTitle' => __('scholarship.definition_add'),
    ])
</x-app-layout>
