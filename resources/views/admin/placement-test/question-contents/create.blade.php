<x-app-layout>
    <x-slot name="header">{{ __('dictt.add_shared_content') }}</x-slot>

    <div class="card">
        <div class="card-body" x-data="{ type: @js(old('type', 'text')) }">
            <h5 class="card-title">
                <a href="{{ route('placement_test_question_contents_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.back') }}
                </a>
            </h5>

            <form method="POST" action="{{ route('placement_test_question_contents_store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="placement_test_level_id" class="form-label">{{ __('dictt.level') }}</label>
                        <select id="placement_test_level_id" name="placement_test_level_id"
                            class="form-control @error('placement_test_level_id') is-invalid @enderror" required>
                            <option value="">{{ __('dictt.select_level') }}</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" @selected((string) old('placement_test_level_id') === (string) $level->id)>
                                    {{ $level->code }}
                                </option>
                            @endforeach
                        </select>
                        @error('placement_test_level_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">{{ __('dictt.content_type') }}</label>
                        <select id="type" name="type" x-model="type"
                            class="form-control @error('type') is-invalid @enderror" required>
                            <option value="text">{{ __('dictt.content_type_text') }}</option>
                            <option value="audio">{{ __('dictt.content_type_audio') }}</option>
                            <option value="image">{{ __('dictt.content_type_image') }}</option>
                            <option value="video">{{ __('dictt.content_type_video') }}</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div x-show="type === 'text'">
                    <div class="form-group mb-3">
                        <label for="text_content" class="form-label">{{ __('dictt.shared_content_text') }}</label>
                        <textarea id="text_content" name="text_content" rows="8"
                            class="form-control @error('text_content') is-invalid @enderror"
                            x-bind:required="type === 'text'">{{ old('text_content') }}</textarea>
                        @error('text_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('dictt.pt_shared_text_help') }}</div>
                    </div>
                </div>

                <div x-show="type !== 'text'" style="display: none;">
                    <div class="form-group mb-3">
                        <label for="media_file" class="form-label">{{ __('dictt.media_file_label') }}</label>
                        <input id="media_file" name="media_file" type="file"
                            class="form-control @error('media_file') is-invalid @enderror"
                            x-bind:required="type !== 'text'"
                            x-bind:accept="{ audio: '.mp3,.wav,.ogg,.m4a,.aac', image: '.jpg,.jpeg,.png,.webp', video: '.mp4,.webm,.ogv' }[type]">
                        @error('media_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('dictt.pt_media_formats_help') }}
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1"
                        @checked(old('is_active', true))>
                    <label for="is_active" class="form-check-label">{{ __('dictt.pt_content_active_label') }}</label>
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('dictt.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
