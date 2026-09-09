<x-app-layout>
    <x-slot name="header">{{ __('dictt.scholarship_period_add') }}</x-slot>

    @include('admin.scholarship.periods._form', [
        'action' => route('admin.scholarship.periods.store'),
        'method' => 'POST',
        'pageTitle' => __('dictt.scholarship_period_add'),
    ])
</x-app-layout>
