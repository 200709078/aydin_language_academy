<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewtheme') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('theme_store')}}" enctype="multipart/form-data">
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
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>