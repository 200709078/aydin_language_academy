<x-app-layout>
    <x-slot name="header">{{ __('dictt.campaigns') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
        </div>
    @endif

    @php
        $statusLabels = [
            \App\Models\Campaign::STATUS_DRAFT => __('dictt.campaign_status_draft'),
            \App\Models\Campaign::STATUS_PUBLISHED => __('dictt.campaign_status_published'),
        ];
        $linkTypeLabels = [
            \App\Models\Campaign::LINK_TYPE_NONE => __('dictt.campaign_link_none'),
            \App\Models\Campaign::LINK_TYPE_INTERNAL => __('dictt.campaign_link_internal'),
            \App\Models\Campaign::LINK_TYPE_EXTERNAL => __('dictt.campaign_link_external'),
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.campaigns') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.campaign_link_help') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.campaigns.settings') }}" class="btn btn-sm btn-outline-secondary"
                        title="{{ __('dictt.campaign_page_settings') }}"
                        aria-label="{{ __('dictt.campaign_page_settings') }}">
                        <i class="fa fa-gear" aria-hidden="true"></i>
                        <span class="visually-hidden">{{ __('dictt.campaign_page_settings') }}</span>
                    </a>
                    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.campaign_add') }}
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.campaign_title_tr') }}</th>
                            <th scope="col">{{ __('dictt.campaign_title_en') }}</th>
                            <th scope="col">{{ __('dictt.campaign_link_type') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campaigns as $campaign)
                            @php
                                $statusClass = $campaign->status === \App\Models\Campaign::STATUS_PUBLISHED
                                    ? 'text-bg-success'
                                    : 'text-bg-secondary';
                                $canMoveUp = $moveAvailability[$campaign->id]['up'] ?? false;
                                $canMoveDown = $moveAvailability[$campaign->id]['down'] ?? false;
                            @endphp
                            <tr>
                                <td class="text-break">{{ $campaign->title_tr }}</td>
                                <td class="text-break">{{ $campaign->title_en }}</td>
                                <td>{{ $linkTypeLabels[$campaign->link_type] ?? $campaign->link_type }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabels[$campaign->status] ?? $campaign->status }}</span></td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-primary"
                                            title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.campaigns.status.update', $campaign) }}" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="0">
                                            <div class="form-check form-switch mb-0">
                                                <input id="campaign-status-{{ $campaign->id }}" type="checkbox"
                                                    class="form-check-input" name="is_published" value="1" role="switch"
                                                    @checked($campaign->status === \App\Models\Campaign::STATUS_PUBLISHED)
                                                    onchange="this.form.submit()"
                                                    aria-label="{{ __('dictt.campaign_publication_status') }}">
                                            </div>
                                            <noscript>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary mt-1">{{ __('dictt.update') }}</button>
                                            </noscript>
                                        </form>
                                        <div class="d-flex flex-column gap-1">
                                            <form method="POST" action="{{ route('admin.campaigns.move', $campaign) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    @disabled(! $canMoveUp) title="{{ __('dictt.move_up') }}">
                                                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_up') }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.campaigns.move', $campaign) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    @disabled(! $canMoveDown) title="{{ __('dictt.move_down') }}">
                                                    <i class="fa fa-arrow-down" aria-hidden="true"></i>
                                                    <span class="visually-hidden">{{ __('dictt.move_down') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dictt.campaign_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($campaigns->hasPages())
                <div class="mt-3">{{ $campaigns->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
