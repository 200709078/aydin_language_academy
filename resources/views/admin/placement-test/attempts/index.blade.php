<x-app-layout>
    <x-slot name="header">{{ __('dictt.placement_test_results') }}</x-slot>

    @if (session('modalSuccessTitle') && session('modalSuccessContent'))
        <div class="relative bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow mb-6 w-full">
            <div
                class="absolute bottom-[-10px] left-10 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-t-[10px] border-t-green-100">
            </div>
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {!! session('modalSuccessTitle') !!}
                </h2>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-red-600 ml-4" title="{{ __('dictt.close') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 text-sm">
                {!! session('modalSuccessContent') !!}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()"
                aria-label="{{ __('dictt.close') }}"></button>
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
                            <th scope="col">{{ __('dictt.placement_test_english_level') }}</th>
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
                                <td>{{ $placementTest->resultLevel?->englishLevelCode() ?? '—' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <form method="POST"
                                            action="{{ route('placement_test_attempts_approve', $placementTest) }}"
                                            class="d-inline-block">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="return_to_list" value="1">
                                            <div class="form-check form-switch admin-list-switch mb-0">
                                                <input id="placement-test-status-{{ $placementTest->id }}" type="checkbox"
                                                    @class([
                                                        'form-check-input',
                                                        'admin-placement-result-status-switch' => $placementTest->status === 'approved',
                                                    ])
                                                    role="switch"
                                                    @checked($placementTest->status === 'approved')
                                                    @disabled($placementTest->status === 'approved')
                                                    onchange="this.form.submit()"
                                                    aria-label="{{ __('dictt.placement_test_result_status') }}"
                                                    title="{{ __('dictt.placement_test_result_status') }}">
                                            </div>
                                            <noscript>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary mt-1"
                                                    title="{{ __('dictt.approve') }}">{{ __('dictt.approve') }}</button>
                                            </noscript>
                                        </form>
                                        @if ($placementTest->status === 'pending_approval')
                                            <span class="badge text-bg-warning">{{ __('dictt.status_pending') }}</span>
                                        @else
                                            <span class="badge text-bg-success">{{ __('dictt.status_approved') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('placement_test_attempts_show', $placementTest) }}"
                                        class="btn btn-sm btn-outline-primary" title="{{ __('dictt.placement_test_review') }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('dictt.placement_test_review') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('dictt.placement_test_attempts_empty') }}</td>
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
