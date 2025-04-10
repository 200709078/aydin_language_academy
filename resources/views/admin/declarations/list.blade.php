<x-app-layout>
    <x-slot name="header">DECLARATIONS OF {{ Str::limit($theme->name, 20) }} THEME</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary" title="Back to Themes List"><i
                    class="fa fa-arrow-left"></i> Back to Themes List</a>
            <a href="{{ route('declaration_create',$theme->id) }}" class="btn btn-sm btn-primary float-right"
                title="Add New Level"><i class="fa fa-plus">
                </i> Add New Declarations</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">TITLE</th>
                        <th scope="col">CONTENTS</th>
                        <th scope="col">IMAGE</th>
                        <th scope="col">PDF</th>
                        <th scope="col">VIDEO</th>
                        <th scope="col">VOICE</th>
                        <th scope="col">OPERATIONS</th>
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
                                    <a href="{{ asset('photos/noimage.jpg') }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                            style="width:120px" class="img-responsive">
                                    </a>
                                @endif
                            </th>
                            <th class="col-md-3" scope="row">{{ $declaration->pdf }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->video }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->voice }}</th>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary" title="Edit Declaration"><i
                                        class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('declaration_destroy', $declaration->id) }}" class="btn btn-sm btn-danger"
                                    title="Delete Declaration"><i class="fa fa-trash w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>