<x-app-layout>
    <x-slot name="header">EDIT EXERCISES FOR {{Str::upper(Str::limit($theme->name, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exercises_list', $theme->id) }}" class="btn btn-sm btn-secondary" title="CANCEL">
                    <i class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('exercise_update', $exercise->id)}}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>TITLE</label>
                    <input type="text" name="title" class="form-control">
                </div>
                <div class="form-group">
                    <label>DESCRIPTION</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">EXERCISE UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>