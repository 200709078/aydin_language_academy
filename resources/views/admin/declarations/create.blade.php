<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewdeclaration') }} - {{Str::upper(Str::limit($theme->name, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('declarations_list', $theme->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}</a>
                        <button type="submit" form="declaration-create-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.addnewdeclaration') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form id="declaration-create-form" method="POST" action="{{ route('declaration_store', $theme->id)}}" enctype="multipart/form-data">
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
            </form>
        </div>
    </div>
</x-app-layout>
