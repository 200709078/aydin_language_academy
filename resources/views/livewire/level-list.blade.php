<div>
    <!-- Delete Modal Start-->
    @if ($modalConfirmContent && $modalConfirmTitle)
        <x-modal wire:model="confirmingDelete">
            <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 w-full relative">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-orange-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {!! $modalConfirmTitle !!}
                    </h2>
                    <button wire:click="$set('confirmingDelete', false)"
                        class="text-gray-400 hover:text-red-500 transition" title="{{ __('dictt.close') }}">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="text-gray-700">
                    {!!$modalConfirmContent!!}
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        <i class="fas fa-ban mr-1"></i> {{ __('dictt.back_short') }}
                    </button>
                    <button wire:click="deleteItem"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        <i class="fas fa-trash-alt mr-1"></i> {{ __('dictt.delete') }}
                    </button>
                </div>
            </div>
        </x-modal>
    @endif
    <!-- Delete Modal End -->

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <h5 class="card-title mb-1">{{ __('dictt.levels_title') }}</h5>
                <a href="{{ route('level_create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-plus"></i> {{ __('dictt.addnewlevel') }}</a>
            </div>
            <div class="admin-table-scroll">
                <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.levelname') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($levels as $level)
                        <tr wire:key="level-row-{{ $level->id }}" class="align-middle">
                            <th class="col-md-3" scope="row">{{ $level->name }}</th>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('level_edit', $level->id) }}" class="btn btn-sm btn-outline-primary"
                                        title="{{ __('dictt.edit') }}">
                                        <i class="fa fa-pen w-4"></i>
                                    </a>
                                    @if ($level->themes_count > 0)
                                        <button type="button" class="btn btn-sm btn-secondary opacity-50 cursor-not-allowed" disabled
                                            title="{{ __('dictt.level_delete_themes_blocked') }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <button wire:click="confirmDelete({{ $level->id }})" class="btn btn-sm btn-outline-primary admin-danger-action"
                                            title="{{ __('dictt.delete') }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
