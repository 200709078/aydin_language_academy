<x-app-layout>
    <x-slot name="header">CREATE EXERCISES</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-secondary" title="CANCEL"><i
                        class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('exercises.store') }}">
                @csrf
                <div class="form-group">
                    <label>EXERCISES TITLE</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label>EXERCISES DESCRIPTION</label>
                    <textarea name="description" class="form-control"rows="4">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm btn-block">EXERCISES CREATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>