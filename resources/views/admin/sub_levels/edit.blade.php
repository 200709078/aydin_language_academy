<x-app-layout>
    <x-slot name="header">{{ __('dictt.sublevel') }} {{ __('dictt.edit') }} - {{ $sub_level->name }}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('sub_levels_list') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}</a>
                        <button type="submit" form="sub-level-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.sublevel') }} {{ __('dictt.edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form id="sub-level-form" method="POST" action="{{ route('sub_level_update', $sub_level->id)}}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.sublevelname') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ $sub_level->name }}">
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
