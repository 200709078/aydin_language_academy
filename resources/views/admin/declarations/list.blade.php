<x-app-layout>
    <x-slot name="header">{{ __('dictt.declarations') }} - {{ Str::limit($theme->name, 20) }}</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary" title="{{ __('dictt.backtothemeslist') }}"><i
                    class="fa fa-arrow-left"></i> {{ __('dictt.backtothemeslist') }}</a>
            <a href="{{ route('declaration_create', $theme->id) }}" class="btn btn-sm btn-primary float-right"
                title="{{ __('dictt.addnewdeclaration') }}"><i class="fa fa-plus">
                </i> {{ __('dictt.addnewdeclaration') }}</a>
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
                    @foreach ($theme->declarations as $declaration)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $declaration->title }}</th>
                            <th class="col-md-3" scope="row">{{ Str::limit($declaration->contents, 20) }}</th>
                            <th class="col-md-3" scope="row">
                                @if($declaration->image)
                                    <a href="{{ asset('photos/' . $declaration->image) }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ asset('photos/' . $declaration->image) }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                    <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                        style="width:120px" class="img-responsive">
                                @endif
                            </th>
                            <th class="col-md-3" scope="row">{{ $declaration->pdf }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->video }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->voice }}</th>
                            <td>
                                <a href="{{ route('declaration_edit', $declaration->id) }}" class="btn btn-sm btn-primary"
                                    title="{{ __('dictt.edit') }}"><i class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('declaration_destroy', $declaration->id) }}" class="btn btn-sm btn-danger"
                                    title="{{ __('dictt.delete') }}"><i class="fa fa-trash w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>