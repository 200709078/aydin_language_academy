<x-app-layout>
    <x-slot name="header">{{ __('dictt.campaign_edit') }}</x-slot>


    @include('admin.campaigns._form', [
        'campaign' => $campaign,
        'action' => route('admin.campaigns.update', $campaign),
        'method' => 'PUT',
        'pageTitle' => __('dictt.campaign_edit'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
