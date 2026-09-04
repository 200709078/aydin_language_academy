<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit') }} - {{Str::upper(Str::limit($exercise->title, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('exercises_list', $exercise->theme_id) }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}</a>
                        <button type="submit" form="exercise-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.exercise') }} {{ __('dictt.edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form id="exercise-form" method="POST" action="{{ route('exercise_update', $exercise->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.title') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ $exercise->title }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.textofquestions') }}</label>
                    <textarea name="qtext" class="form-control" rows="4">{{ $exercise->qtext }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    @php($imageUrl = $exercise->privateImageUrl())
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
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.videolink') }}</label>
                    <input type="text" name="video" class="form-control" value="{{ $exercise->video }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.voicelink') }}</label>
                    <input type="text" name="voice" class="form-control" value="{{ $exercise->voice }}">
                </div>
                
            </form>
        </div>
    </div>
</x-app-layout>
