<x-app-layout>
    <x-slot name="header">THEMES</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('themes.create') }}" class="btn btn-sm btn-primary float-right" title="Add New Theme">
                <i class="fa fa-plus"></i> Add New Theme</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">LEVEL</th>
                        <th scope="col">SUB LEVEL</th>
                        <th scope="col">NAME</th>
                        <th scope="col">IMAGE</th>
                        <th scope="col">OPERATIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($themes as $theme)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $theme->levels->name }}</th>
                            <td>{{ $theme->sub_levels->name}}</td>
                            <td>{{ Str::limit($theme->name, 20) }}</td>
                            <td>
                                @if($theme->image)
                                    <a href="{{ asset('photos/' . $theme->image) }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ asset('photos/' . $theme->image) }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                    <a href="{{ asset('photos/noimage.jpg') }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                            style="width:120px" class="img-responsive">
                                    </a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('declarations_list', $theme->id) }}" class="btn btn-sm btn-primary"
                                    title="Declarations List">
                                    <i class="fa fa-list w-4"></i></a>
                                <a href="{{ route('exercises_list', $theme->id) }}" class="btn btn-sm btn-warning"
                                    title="Exercises List">
                                    <i class="fa fa-list w-4"></i></a>
                                <a href="#" class="btn btn-sm btn-primary" title="Edit Theme">
                                    <i class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('theme_destroy', $theme->id) }}" class="btn btn-sm btn-danger"
                                    title="Delete Theme">
                                    <i class="fa fa-trash w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $themes->withQueryString()->links() }}
            <!-- <div class="mt-2"> {{ $themes->links('pagination::bootstrap-4') }}</div> -->
        </div>
    </div>
</x-app-layout>