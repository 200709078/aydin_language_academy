<x-app-layout>
    <x-slot name="header">{{ __('dictt.slogans') }}</x-slot>


    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ __('dictt.slogans') }}</h5>
                    <p class="text-muted small mb-0">{{ __('dictt.slogans_admin_help') }}</p>
                </div>
                <a href="{{ route('admin.slogans.create') }}" class="btn btn-sm btn-outline-primary"
                    title="{{ __('dictt.slogan_add') }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('dictt.slogan_add') }}
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('dictt.slogan_title_tr') }}</th>
                            <th scope="col">{{ __('dictt.slogan_title_en') }}</th>
                            <th scope="col">{{ __('dictt.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($slogans as $slogan)
                            <tr>
                                <td class="text-break">{{ $slogan->title_tr }}</td>
                                <td class="text-break">{{ $slogan->title_en }}</td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <a href="{{ route('admin.slogans.edit', $slogan) }}"
                                            class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('dictt.edit') }}</span>
                                        </a>
                                        <form id="slogan-delete-{{ $slogan->id }}" method="POST"
                                            action="{{ route('admin.slogans.destroy', $slogan) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger admin-danger-action"
                                                title="{{ __('dictt.slogan_delete') }}"
                                                data-action-confirmation
                                                data-confirm-form="slogan-delete-{{ $slogan->id }}"
                                                data-confirm-title="{{ __('dictt.slogan_delete') }}"
                                                data-confirm-content="{{ __('dictt.slogan_delete_confirm', ['slogan' => $slogan->title_tr]) }}"
                                                data-confirm-action="{{ __('dictt.slogan_delete') }}"
                                                data-confirm-icon="fa-trash-alt"
                                                data-confirm-tone="danger">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('dictt.slogan_delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">{{ __('dictt.slogans_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($slogans->hasPages())
                <div class="mt-3">{{ $slogans->links() }}</div>
            @endif
        </div>
    </div>

    <x-action-confirmation-modal />
</x-app-layout>
