<x-app-layout>
    <x-slot name="header">LIST OF EXERCISES</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title float-right">
                <a href="{{ route('exercises.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Create Exercises</a>
            </h5>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">EXERCISES TITLE</th>
                        <th scope="col">EXERCISES DESCRIPTION</th>
                        <th scope="col">NUMBER OF QUESTION</th>
                        <th scope="col">OPERATIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exercises as $exer)
                        <tr class="align-middle">
                            <th class="col-md-3" scope="row">{{ $exer->title }}</th>
                            <td class="col-md-3">{{\Illuminate\Support\Str::limit($exer->description, 50, $end='...')}}</td>
                            <td class="col-md-2">{{ $exer->questions_count}}</td>
                            <td>
                                <a href="{{ route('exercises.details',$exer->id) }}" @if($exer->questions_count==0) class="btn btn-sm btn-secondary disabled" @else class="btn btn-sm btn-secondary" @endif title="Show Detail">
                                    <i class="fa fa-info w-4"></i>
                                </a>
                                <a href="{{ route('questions.index', $exer->id) }}" class="btn btn-sm btn-warning" title="List Questions"><i
                                        class="fa fa-list w-4"></i></a>
                                <a href="{{ route('exercises.edit', $exer->id) }}" class="btn btn-sm btn-primary" title="Edit Exercises"><i
                                        class="fa fa-pen w-4"></i></a>
                                <a href="{{ route('exercises.destroy', $exer->id) }}" class="btn btn-sm btn-danger" title="Delete Exercises"><i
                                        class="fa fa-times w-4"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $exercises->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>