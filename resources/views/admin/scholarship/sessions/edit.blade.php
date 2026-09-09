<x-app-layout>
    <x-slot name="header">{{ __('scholarship.session_edit') }}</x-slot>

    @include('admin.scholarship.sessions._form', [
        'session' => $session,
        'action' => route('admin.scholarship.sessions.update', $session),
        'method' => 'PUT',
        'pageTitle' => __('scholarship.session_edit'),
    ])
</x-app-layout>
