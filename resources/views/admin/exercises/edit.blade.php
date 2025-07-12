<x-app-layout>
    <x-slot name="header">EDIT EXERCISES FOR {{Str::upper(Str::limit($exercise->title, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('exercises_list', $exercise->theme_id) }}" class="btn btn-sm btn-secondary" title="CANCEL">
                    <i class="fa fa-arrow-left"></i> CANCEL</a>
            </h5>
            <form method="POST" action="{{ route('exercise_update', $exercise->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>TITLE</label>
                    <input type="text" name="title" class="form-control" value="{{ $exercise->title }}">
                </div>
                <div class="form-group">
                    <label>TEXT OF QUESTIONS</label>
                    <textarea name="qtext" class="form-control" rows="4">{{ $exercise->qtext }}</textarea>
                </div>

                <div class="form-group">
                    <label>PHOTO</label>
                    @if($exercise->image)
                            <a href="{{ asset('photos/' . $exercise->image) }}" target="_blank">
                            <img class="img-fluid rounded align-self-end"
                            src="{{ asset('photos/' . $exercise->image) }}" style="width:120px"
                            class="img-responsive">
                        </a>
                    @else
                            <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                            style="width:120px" class="img-responsive">
                    @endif
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>VIDEO LINK</label>
                    <input type="text" name="video" class="form-control" value="{{ $exercise->video }}">
                </div>
                <div class="form-group">
                    <label>VOICE LINK</label>
                    <input type="text" name="voice" class="form-control" value="{{ $exercise->voice }}">
                </div>
                
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">EXERCISE UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>