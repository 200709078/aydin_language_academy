<x-app-layout>
    <x-slot name="header">{{ __('dictt.sublevel') }} {{ __('dictt.edit') }} - {{ $sub_level->name }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('sub_levels_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('sub_level_update', $sub_level->id)}}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.sublevelname') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ $sub_level->name }}">
                </div>
        </div>
        <div class="form-group ml-3 mb-3">
            <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.update') }}</button>
        </div>
        </form>
    </div>
    </div>
</x-app-layout>