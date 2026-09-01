<div>
    <!-- Archive / permanent delete modal start -->
    @if ($modalConfirmContent && $modalConfirmTitle)
        <x-modal wire:model="confirmingAction">
            <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 w-full relative">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-orange-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {!! $modalConfirmTitle !!}
                    </h2>
                    <button wire:click="$set('confirmingAction', false)"
                        class="text-gray-400 hover:text-red-500 transition" title="{{ __('dictt.close') }}">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="text-gray-700">
                    {!! $modalConfirmContent !!}
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="$set('confirmingAction', false)"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        <i class="fa fa-ban mr-1"></i> {{ __('dictt.cancel') }}
                    </button>
                    <button wire:click="executePendingAction"
                        class="px-4 py-2 text-white rounded-md transition {{ $pendingAction === 'force-delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-secondary hover:bg-gray-700' }}">
                        <i class="fa {{ $pendingAction === 'force-delete' ? 'fa-trash-alt' : 'fa-archive' }} mr-1"></i>
                        {{ $pendingAction === 'force-delete' ? __('dictt.review_permanently_delete') : __('dictt.review_archive_action') }}
                    </button>
                </div>
            </div>
        </x-modal>
    @endif
    <!-- Archive / permanent delete modal end -->
    <!-- Success Start -->
    @if ((session('modalSuccessTitle') && session('modalSuccessContent')) || ($modalSuccessTitle && $modalSuccessContent))
        <div class="relative bg-green-100 text-green-800 px-6 py-4 rounded-lg shadow mb-6 w-full">
            <div
                class="absolute bottom-[-10px] left-10 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-t-[10px] border-t-green-100">
            </div>
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {!! session('modalSuccessTitle') ?? $modalSuccessTitle !!}
                </h2>
                <button type="button" wire:click="dismissSuccess" class="text-gray-500 hover:text-red-600 ml-4" title="{{ __('dictt.close') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 text-sm">
                {!! session('modalSuccessContent') ?? $modalSuccessContent !!}
            </div>
        </div>
    @endif
    <!-- Success End  -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.reviews') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.reviews_note') }}</p>
                </div>
            </div>

            <div class="btn-group btn-group-sm mb-3" role="group" aria-label="{{ __('dictt.status') }}">
                <button type="button" wire:click="$set('statusFilter', 'default')"
                    class="btn {{ $statusFilter === 'default' ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('dictt.filter_all') }}</button>
                <button type="button" wire:click="$set('statusFilter', 'pending')"
                    class="btn {{ $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('dictt.status_pending') }}</button>
                <button type="button" wire:click="$set('statusFilter', 'approved')"
                    class="btn {{ $statusFilter === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('dictt.status_approved') }}</button>
                <button type="button" wire:click="$set('statusFilter', 'rejected')"
                    class="btn {{ $statusFilter === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('dictt.status_rejected') }}</button>
                <button type="button" wire:click="$set('statusFilter', 'archived')"
                    class="btn {{ $statusFilter === 'archived' ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('dictt.review_archive') }}</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.member') }}</th>
                            <th scope="col">{{ __('dictt.branch') }}</th>
                            <th scope="col">{{ __('dictt.rating') }}</th>
                            <th scope="col">{{ __('dictt.content') }}</th>
                            <th scope="col">{{ __('dictt.status') }}</th>
                            <th scope="col">{{ __('dictt.date') }}</th>
                            <th scope="col" class="text-end">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="text-break">{{ $review->user?->name ?? ('#' . $review->id) }}</td>
                                <td>{{ $review->branchLabel() }}</td>
                                <td class="text-nowrap">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </td>
                                <td class="admin-review-content-cell">
                                    <span class="admin-table-cell-ellipsis">{{ \Illuminate\Support\Str::limit($review->content, 120) }}</span>
                                </td>
                                <td>
                                    @if ($review->status === \App\Models\Review::STATUS_ARCHIVED || $review->trashed())
                                        <span class="badge text-bg-secondary">{{ __('dictt.status_archived') }}</span>
                                    @elseif ($review->status === \App\Models\Review::STATUS_APPROVED)
                                        <span class="badge text-bg-success">{{ __('dictt.status_approved') }}</span>
                                    @elseif ($review->status === \App\Models\Review::STATUS_REJECTED)
                                        <span class="badge text-bg-danger">{{ __('dictt.status_rejected') }}</span>
                                    @else
                                        <span class="badge text-bg-warning">{{ __('dictt.status_pending') }}</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $review->created_at?->format('d.m.Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if ($review->status === \App\Models\Review::STATUS_ARCHIVED || $review->trashed())
                                            <button type="button" wire:click="confirmForceDelete({{ $review->id }})"
                                                class="btn btn-sm btn-outline-primary admin-danger-action" title="{{ __('dictt.review_permanently_delete') }}">
                                                <i class="fa fa-trash w-4" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('dictt.review_permanently_delete') }}</span>
                                            </button>
                                        @else
                                            @if ($review->status !== \App\Models\Review::STATUS_APPROVED)
                                            <button type="button" wire:click="approve({{ $review->id }})"
                                                class="btn btn-sm btn-success" title="{{ __('dictt.approve') }}">
                                                <i class="fa fa-check w-4"></i>
                                            </button>
                                            @endif
                                            @if ($review->status !== \App\Models\Review::STATUS_REJECTED)
                                            <button type="button" wire:click="reject({{ $review->id }})"
                                                class="btn btn-sm btn-warning" title="{{ __('dictt.reject') }}">
                                                <i class="fa fa-ban w-4"></i>
                                            </button>
                                            @endif
                                            <a href="{{ route('review_edit', $review->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                                <i class="fa fa-pen w-4"></i>
                                            </a>
                                            <button type="button" wire:click="confirmArchive({{ $review->id }})"
                                                class="btn btn-sm btn-outline-secondary" title="{{ __('dictt.review_archive_action') }}">
                                                <i class="fa fa-archive w-4" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('dictt.review_archive_action') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('dictt.reviews_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
