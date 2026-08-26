<x-app-layout>
    <x-slot name="header">{{ __('dictt.placement_test_results') }}</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="{{ __('dictt.placement_test_close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-1">{{ __('dictt.placement_test_results') }}</h5>
            <p class="text-muted small mb-3">{{ __('dictt.placement_test_attempts_note') }}</p>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.member') }}</th>
                            <th scope="col">{{ __('dictt.email') }}</th>
                            <th scope="col">{{ __('dictt.placement_test_submitted_at') }}</th>
                            <th scope="col">{{ __('dictt.placement_test_result_level') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attempts as $placementTest)
                            <tr>
                                <td class="text-break">{{ $placementTest->user?->name ?? ('#' . $placementTest->user_id) }}</td>
                                <td class="text-break">{{ $placementTest->user?->email ?? '—' }}</td>
                                <td class="text-nowrap">{{ $placementTest->submitted_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td>{{ $placementTest->resultLevel?->code ?? '—' }}</td>
                                <td>
                                    @if ($placementTest->status === 'pending_approval')
                                        <span class="badge text-bg-warning">{{ __('dictt.status_pending') }}</span>
                                    @else
                                        <span class="badge text-bg-success">{{ __('dictt.status_approved') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('placement_test_attempts_show', $placementTest) }}"
                                        class="btn btn-sm btn-primary" title="{{ __('dictt.placement_test_review') }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('dictt.placement_test_review') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dictt.placement_test_attempts_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attempts->hasPages())
                <div class="mt-3">
                    {{ $attempts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
