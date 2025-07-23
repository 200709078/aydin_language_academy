<x-app-layout>
    <x-slot name="header">{{ __('dictt.themes') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('theme_create') }}" class="btn btn-sm btn-primary float-right" title="{{ __('dictt.addnewtheme') }}">
                <i class="fa fa-plus"></i> {{ __('dictt.addnewtheme') }}</a>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">{{ __('dictt.levelname') }}</th>
                        <th scope="col">{{ __('dictt.sublevelname') }}</th>
                        <th scope="col">{{ __('dictt.title') }}</th>
                        <th scope="col">{{ __('dictt.image') }}</th>
                        <th scope="col">{{ __('dictt.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($themes as $theme)
                        <tr class="align-middle">
                            <td class="col-md-3" scope="row">{{ $theme->levels->name }}</td>
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
                                    <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                        style="width:120px" class="img-responsive">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('declarations_list', $theme->id) }}" class="btn btn-sm btn-primary"
                                    title="{{ __('dictt.declarationslist') }}">
                                    <i class="fa fa-list w-4"></i></a>
                                <a href="{{ route('exercises_list', $theme->id) }}" class="btn btn-sm btn-warning"
                                    title="{{ __('dictt.exerciseslist') }}">
                                    <i class="fa fa-list w-4"></i></a>
                                <a href="{{ route('theme_edit', $theme->id) }}" class="btn btn-sm btn-primary"
                                    title="{{ __('dictt.edit') }}">
                                    <i class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('theme_destroy', $theme->id) }}" class="btn btn-sm btn-danger"
                                    title="{{ __('dictt.delete') }}">
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