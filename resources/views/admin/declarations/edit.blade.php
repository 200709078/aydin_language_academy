<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit') }} - {{Str::upper(Str::limit($declaration->title, 20))}}</x-slot>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}</a>
                        <button type="submit" form="declaration-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.declaration') }} {{ __('dictt.edit') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>
            <form id="declaration-form" method="POST" action="{{ route('declaration_update', $declaration->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label>{{ __('dictt.title') }}</label>
                    <input type="text" name="title" class="form-control" value="{{ $declaration->title }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.content') }}</label>
                    <input type="text" name="context" class="form-control" value="{{ $declaration->context }}">
                </div>
                <div class="form-group">
                    <label>{{ __('dictt.image') }}</label>
                    @php($imageUrl = $declaration->privateImageUrl())
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
                    <label>{{ __('dictt.pdffile') }}</label>
                    @if ($pdfUrl = $declaration->privatePdfUrl())
                    <br><a href="{{ $pdfUrl }}" target="_blank" rel="noopener">{{ $declaration->pdf }}</a>
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
            </form>
        </div>
    </div>
</x-app-layout>
