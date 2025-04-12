<x-app-layout>
    <x-slot name="header">EDIT THEME</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('themes_list') }}" class="btn btn-sm btn-secondary" title="CANCEL">
                    <i class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('theme_update',$theme->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>LEVEL</label>
                    <select name="level_id" class="form-control">
                        @foreach ($levels as $level)
                            <option @if ($theme->level_id===$level->id) selected @endif value="{{ $level->id}}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>SUB LEVEL</label>
                    <select name="sub_level_id" class="form-control">
                        @foreach ($sub_levels as $sub_level)
                            <option @if ($theme->sub_level_id===$sub_level->id) selected @endif value="{{ $sub_level->id}}">{{ $sub_level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>NAME</label>
                    <input type="text" name="name" class="form-control" value="{{ $theme->name }}">
                </div>
                <div class="form-group">
                <label>IMAGE</label>
                                @if($theme->image)
                                    <a href="{{ asset('photos/' . $theme->image) }}" target="_blank">
                                        <img class="img-fluid rounded align-self-end"
                                            src="{{ asset('photos/' . $theme->image) }}" style="width:120px"
                                            class="img-responsive">
                                    </a>
                                @else
                                        <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                                            style="width:120px" class="img-responsive">
                                @endif
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">THEME UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>