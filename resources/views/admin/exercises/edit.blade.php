<x-app-layout>
    <x-slot name="header">EDIT EXERCISES {{$exercises->title }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-secondary" title="CANCEL"><i
                        class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('exercises.update',$exercises->id) }}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label style="font-weight: bold;">EXERCISES TITLE</label>
                    <input type="text" name="title" class="form-control" value="{{ $exercises->title }}">
                </div>
                <div class="form-group">
                    <label style="font-weight: bold;">EXERCISES DESCRIPTION</label>
                    <textarea name="description" class="form-control"rows="4">{{ $exercises->description }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm" style="width:100%; font-weight: bold;">EXERCISES UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>