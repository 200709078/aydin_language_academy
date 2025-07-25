<x-app-layout>
    <x-slot name="header">{{ __('dictt.levels') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('level_create') }}" class="btn btn-sm btn-primary float-right">
                <i class="fa fa-plus"></i> {{ __('dictt.addnewlevel') }}</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.levelname') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($levels as $level)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $level->name }}</th>
                            <td>
                                <a href="{{ route('level_edit',$level->id) }}" class="btn btn-sm btn-primary"
                                    title="{{ __('dictt.edit') }}">
                                    <i class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('level_destroy', $level->id) }}" class="btn btn-sm btn-danger"
                                    title="{{ __('dictt.delete') }}">
                                    <i class="fa fa-trash w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>