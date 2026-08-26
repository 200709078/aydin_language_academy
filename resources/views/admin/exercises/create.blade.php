<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewexercises') }} - {{Str::upper(Str::limit($theme->name, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <a href="{{ route('exercises_list', $theme->id) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.addnewexercises') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form method="POST" action="{{ route('exercise_store', $theme->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.title') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.textofquestions') }}</label>
                    <textarea name="qtext" class="form-control" rows="4">{{ old('qtext') }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="form-group">
                    <label>{{ __('dictt.videolink') }}</label>
                    <input type="text" name="video" class="form-control" value="{{ old('video') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.voicelink') }}</label>
                    <input type="text" name="voice" class="form-control" value="{{ old('voice') }}">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
