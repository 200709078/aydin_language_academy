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
                        class="text-gray-400 hover:text-red-500 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="text-gray-700">
                    {!!$modalConfirmContent!!}
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        <i class="fas fa-ban mr-1"></i> {{ __('dictt.cancel') }}
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
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-red-600 ml-4">
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
            <a href="{{ route('sub_level_create') }}" class="btn btn-sm btn-primary float-right">
                <i class="fa fa-plus"></i> {{ __('dictt.addnewsublevel') }}</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.sublevelname') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sublevels as $sublevel)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $sublevel->name }}</th>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('sub_level_edit', $sublevel->id) }}" class="btn btn-sm btn-primary"
                                        title="{{ __('dictt.edit') }}">
                                        <i class="fa fa-pen w-4"></i>
                                    </a>
                                    <!--  -->
                                    <button wire:click="confirmDelete({{ $sublevel->id }})" class="btn btn-sm btn-danger"
                                        title="{{ __('dictt.delete') }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <!--  -->
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>