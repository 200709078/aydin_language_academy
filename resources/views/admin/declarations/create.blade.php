<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewdeclaration') }} - {{Str::upper(Str::limit($theme->name, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('declarations_list', $theme->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('declaration_store', $theme->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.title') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.content') }}</label>
                    <input type="text" name="context" class="form-control" value="{{ old('context') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.pdffile') }}</label>
                    <input type="file" name="pdf" class="form-control">
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