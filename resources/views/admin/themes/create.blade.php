<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewtheme') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}</a>
                        <button type="submit" form="theme-create-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.addnewtheme') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form id="theme-create-form" method="POST" action="{{ route('theme_store')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.levelname') }}</label>
                    <select name="level_id" class="form-control">
                        @foreach ($levels as $level)
                            <option value="{{ $level->id}}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.sublevelname') }}</label>
                    <select name="sub_level_id" class="form-control">
                        @foreach ($sub_levels as $sub_level)
                            <option value="{{ $sub_level->id}}">{{ $sub_level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.themename') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
