<x-app-layout>
    <x-slot name="header">{{ __('scholarship.'.$meta['title']) }}</x-slot>

    @include('admin.scholarship.definitions._form', [
        'definition' => $definition,
        'action' => route('admin.scholarship.definitions.update', ['type' => $type, 'definition' => $definition->id]),
        'method' => 'PUT',
        'pageTitle' => __('scholarship.definition_edit'),
    ])
</x-app-layout>
