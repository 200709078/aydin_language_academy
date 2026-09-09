<x-app-layout>
    <x-slot name="header">{{ __('scholarship.session_add') }}</x-slot>

    @include('admin.scholarship.sessions._form', [
        'action' => route('admin.scholarship.sessions.store'),
        'method' => 'POST',
        'pageTitle' => __('scholarship.session_add'),
    ])
</x-app-layout>
