<x-app-layout>
    <x-slot name="header">{{ __('dictt.scholarship_period_edit') }}</x-slot>

    @include('admin.scholarship.periods._form', [
        'period' => $period,
        'action' => route('admin.scholarship.periods.update', $period),
        'method' => 'PUT',
        'pageTitle' => __('dictt.scholarship_period_edit'),
    ])
</x-app-layout>
