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
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i>
                    {{ __('dictt.backtothemeslist') }}</a>
                <h5 class="card-title mb-0 text-center flex-grow-1">{{ __('dictt.declarationslist') }}</h5>
                <a href="{{ route('declaration_create', $theme_id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-plus"></i> {{ __('dictt.addnewdeclaration') }}</a>
            </div>
            <div class="admin-table-scroll">
                <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.title') }}</th>
                        <th scope="col">{{ __('dictt.content') }}</th>
                        <th scope="col">{{ __('dictt.image') }}</th>
                        <th scope="col">{{ __('dictt.pdffile') }}</th>
                        <th scope="col">{{ __('dictt.videolink') }}</th>
                        <th scope="col">{{ __('dictt.voicelink') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($declarations as $declaration)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $declaration->title }}</th>
                            <th class="col-md-3" scope="row">{{ Str::limit($declaration->context, 20) }}</th>
                            <th class="col-md-3" scope="row">
                                @php($imageUrl = $declaration->privateImageUrl())
                                @if($imageUrl)
                                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ $imageUrl }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                    <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                        style="width:120px" class="img-responsive">
                                @endif
                            </th>
                            <th class="col-md-3" scope="row">
                                @if ($pdfUrl = $declaration->privatePdfUrl())
                                    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener">{{ $declaration->pdf }}</a>
                                @endif
                            </th>
                            <th class="col-md-3" scope="row">{{ $declaration->video }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->voice }}</th>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('declaration_edit', $declaration->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="{{ __('dictt.edit') }}">
                                        <i class="fa fa-pen w-4"></i></a>
                                    <!--  -->
                                    <button wire:click="confirmDelete({{ $declaration->id }})" class="btn btn-sm btn-outline-primary admin-danger-action"
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
</div>
