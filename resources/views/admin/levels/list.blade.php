<x-app-layout>
    <x-slot name="header">LEVELS</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="#" class="btn btn-sm btn-primary float-right" title="Add New Level"><i class="fa fa-plus"></i> Add
                New Level</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">LEVEL NAME</th>
                        <th scope="col">OPERATIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($levels as $level)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $level->name }}</th>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary"
                                    title="Edit {{ucfirst(Str::lower($level->name))  }} Level"><i
                                        class="fa fa-pen w-4"></i></a>
                                <a href="#" class="btn btn-sm btn-danger"
                                    title="Delete {{ucfirst(Str::lower($level->name))  }} Level"><i
                                        class="fa fa-trash w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>