<x-app-layout>
    <x-slot name="header">CREATE LEVEL</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('levels_list') }}" class="btn btn-sm btn-secondary" title="CANCEL">
                    <i class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('level_store')}}">
                @csrf
                <div class="form-group">
                    <label>LEVEL NAME</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">LEVEL CREATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>