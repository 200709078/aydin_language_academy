<x-app-layout>
    <x-slot name="header">{{ __('dictt.contact_messages') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="{{ __('dictt.placement_test_close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <h5 class="card-title mb-0">{{ __('dictt.contact_messages') }}</h5>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $filters = [
                            'all' => __('dictt.filter_all'),
                            \App\Models\model_messages::STATUS_UNREAD => __('dictt.message_status_unread'),
                            \App\Models\model_messages::STATUS_READ => __('dictt.message_status_read'),
                            'replied' => __('dictt.filter_replied'),
                            \App\Models\model_messages::STATUS_ARCHIVED => __('dictt.message_status_archived'),
                        ];
                    @endphp
                    @foreach ($filters as $filterKey => $filterLabel)
                        <a href="{{ route('admin.messages.index', ['filter' => $filterKey]) }}"
                            class="btn btn-sm {{ $filter === $filterKey ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $filterLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.fullname') }}</th>
                            <th scope="col">{{ __('dictt.email') }}</th>
                            <th scope="col">{{ __('dictt.branch') }}</th>
                            <th scope="col">{{ __('dictt.subject') }}</th>
                            <th scope="col">{{ __('dictt.message_sent_at') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
                            <th scope="col">{{ __('dictt.message_delivery_status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $message)
                            @php
                                $statusClass = match ($message->status) {
                                    \App\Models\model_messages::STATUS_UNREAD => 'text-bg-warning',
                                    \App\Models\model_messages::STATUS_READ => 'text-bg-primary',
                                    default => 'text-bg-secondary',
                                };
                                $deliveryStatusClass = match ($message->latestReply?->delivery_status) {
                                    \App\Models\MessageReply::STATUS_SENT => 'text-bg-success',
                                    \App\Models\MessageReply::STATUS_FAILED => 'text-bg-danger',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="text-break">{{ $message->fullname }}</td>
                                <td class="text-break">{{ $message->email }}</td>
                                <td>{{ $message->branchLabel() }}</td>
                                <td class="text-break">{{ $message->subject }}</td>
                                <td class="text-nowrap">{{ $message->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $message->statusLabel() }}</span></td>
                                <td>
                                    @if ($message->latestReply)
                                        <span class="badge {{ $deliveryStatusClass }}">{{ $message->latestReply->deliveryStatusLabel() }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-primary" title="{{ __('dictt.details') }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('dictt.details') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('dictt.contact_messages_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($messages->hasPages())
                <div class="mt-3">{{ $messages->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
