<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit') }} - {{Str::upper(Str::limit($declaration->title, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('declaration_update', $declaration->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.title') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ $declaration->title }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.content') }}</label>
                    <input type="text" name="contents" class="form-control" value="{{ $declaration->contents }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    @if($declaration->image)
                            <a href="{{ asset('photos/' . $declaration->image) }}" target="_blank">
                            <img class="img-fluid rounded align-self-end"
                            src="{{ asset('photos/' . $declaration->image) }}" style="width:120px"
                            class="img-responsive">
                        </a>
                    @else
                            <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                            style="width:120px" class="img-responsive">
                    @endif
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.pdffile') }}</label>
                    @if ($declaration->pdf)
                    <br><label>{{ $declaration->pdf }}</label>
                    @endif
                    <input type="file" name="pdf" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.videolink') }}</label>
                    <input type="text" name="video" class="form-control" value="{{ $declaration->video }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.voicelink') }}</label>
                    <input type="text" name="voice" class="form-control" value="{{ $declaration->voice }}">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>