<x-app-layout>
    <x-slot name="header">{{ __('dictt.campaign_edit') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    @include('admin.campaigns._form', [
        'campaign' => $campaign,
        'action' => route('admin.campaigns.update', $campaign),
        'method' => 'PUT',
        'pageTitle' => __('dictt.campaign_edit'),
        'submitLabel' => __('dictt.save'),
    ])
</x-app-layout>
