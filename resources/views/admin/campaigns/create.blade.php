<x-app-layout>
    <x-slot name="header">{{ __('dictt.campaign_add') }}</x-slot>

    @include('admin.campaigns._form', [
        'action' => route('admin.campaigns.store'),
        'method' => 'POST',
        'pageTitle' => __('dictt.campaign_add'),
        'submitLabel' => __('dictt.add'),
    ])
</x-app-layout>
