<x-app-layout>
    <x-slot name="header">{{ __('dictt.course') }} {{ __('dictt.edit') }} - {{ $course->title_tr }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('courses_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('course_update', $course->id)}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.title') }} (tr)</label>
                            <textarea name="title_tr" class="form-control" rows="2">{{ $course->title_tr }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.title') }} (en)</label>
                            <textarea name="title_en" class="form-control" rows="2">{{ $course->title_en}}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.slogan') }} (tr)</label>
                            <textarea name="slogan_tr" class="form-control" rows="2">{{ $course->slogan_tr }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.slogan') }} (en)</label>
                            <textarea name="slogan_en" class="form-control" rows="2">{{ $course->slogan_en }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.description') }} (tr)</label>
                            <textarea name="description_tr" class="form-control"
                                rows="2">{{ $course->description_tr }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.description') }} (en)</label>
                            <textarea name="description_en" class="form-control"
                                rows="2">{{ $course->description_en }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label>{{ __('dictt.image') }}</label>
                    @if($course->image)
                        <a href="{{ asset('photos/' . $course->image) }}" target="_blank">
                            <img class="img-fluid rounded align-self-end" src="{{ asset('photos/' . $course->image) }}"
                                style="width:120px" class="img-responsive">
                        </a>
                    @else
                        <img class="img-fluid rounded align-self-end" src="{{ asset('photos/noimage.jpg') }}"
                            style="width:120px" class="img-responsive">
                    @endif
                    <input type="file" name="image" class="form-control">
                </div>
        </div>
        <div class="form-group ml-3 mb-3">
            <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.update') }}</button>
        </div>
        </form>
    </div>
    </div>
</x-app-layout>