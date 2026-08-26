<x-app-layout>
    <x-slot name="header">{{ __('dictt.sublevel') }} {{ __('dictt.edit') }} - {{ $sub_level->name }}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <a href="{{ route('sub_levels_list') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.sublevel') }} {{ __('dictt.edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
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
