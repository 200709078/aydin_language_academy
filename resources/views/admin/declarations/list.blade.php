<x-app-layout>
    <x-slot name="header">DECLARATIONS OF {{ Str::limit($theme->name,20) }} THEME</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary" title="Back to Themes List"><i
                    class="fa fa-arrow-left"></i> Back to Themes List</a>
            <a href="#" class="btn btn-sm btn-primary float-right" title="Add New Level"><i class="fa fa-plus"></i> Add
                New declarations</a>
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
                            <th class="col-md-3" scope="row">{{ $declaration->image }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->pdf }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->video }}</th>
                            <th class="col-md-3" scope="row">{{ $declaration->voice }}</th>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary" title="Edit Declaration"><i
                                        class="fa fa-pen w-4"></i></a>
                                <a href="#" class="btn btn-sm btn-danger" title="Delete Declaration"><i
                                        class="fa fa-trash w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>