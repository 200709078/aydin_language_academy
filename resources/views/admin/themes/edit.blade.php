<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit') }} - {{ __('dictt.theme') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}</a>
                        <button type="submit" form="theme-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.theme') }} {{ __('dictt.edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form id="theme-form" method="POST" action="{{ route('theme_update',$theme->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.level') }}</label>
                    <select name="level_id" class="form-control">
                        @foreach ($levels as $level)
                            <option @if ($theme->level_id===$level->id) selected @endif value="{{ $level->id}}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.sublevel') }}</label>
                    <select name="sub_level_id" class="form-control">
                        @foreach ($sub_levels as $sub_level)
                            <option @if ($theme->sub_level_id===$sub_level->id) selected @endif value="{{ $sub_level->id}}">{{ $sub_level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.themename') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ $theme->name }}">
                </div>
                <div class="form-group">
                <label>{{ __('dictt.image') }}</label>
                                @php($imageUrl = $theme->privateImageUrl())
                                @if($imageUrl)
                                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ $imageUrl }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                        <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                            style="width:120px" class="img-responsive">
                                @endif
                    <input type="file" name="image" class="form-control mt-2">
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
