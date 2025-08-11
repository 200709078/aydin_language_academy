<x-app-layout>
    <x-slot name="header">{{ __('dictt.addnewcourse') }}</x-slot>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('courses_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.cancel') }}</a>
            </h5>
            <form method="POST" action="{{ route('course_store')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.title') }} (tr)</label>
                            <textarea name="title_tr" class="form-control" rows="2">{{ old('title_tr') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.title') }} (en)</label>
                            <textarea name="title_en" class="form-control" rows="2">{{ old('title_en') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.slogan') }} (tr)</label>
                            <textarea name="slogan_tr" class="form-control" rows="2">{{ old('slogan_tr') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.slogan') }} (en)</label>
                            <textarea name="slogan_en" class="form-control" rows="2">{{ old('slogan_en') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.description') }} (tr)</label>
                            <textarea name="description_tr" class="form-control"
                                rows="2">{{ old('description_tr') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label>{{ __('dictt.description') }} (en)</label>
                            <textarea name="description_en" class="form-control"
                                rows="2">{{ old('description_en') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label>{{ __('dictt.image') }}</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm btn-block">{{ __('dictt.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>