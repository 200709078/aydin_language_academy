<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewlevel') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('levels_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('level_store')}}">
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.levelname') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>