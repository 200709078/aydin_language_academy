@php
    $initialLevelId = old('placement_test_level_id', $defaultLevelId);
@endphp

<x-app-layout>
    <x-slot name="header">{{ __('dictt.add_shared_content') }}</x-slot>

    <div class="card">
        <div class="card-body" x-data="{
            type: @js(old('type', 'text')),
            mediaFormatHints: {
                audio: @js(__('dictt.pt_audio_formats_help')),
                image: @js(__('dictt.pt_image_formats_help')),
                video: @js(__('dictt.pt_video_formats_help')),
            },
        }">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('placement_test_question_contents_list') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}
                        </a>
                        <button type="submit" form="pt-question-content-create-form" class="btn btn-success btn-sm">{{ __('dictt.save') }}</button>
                    </div>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.add_shared_content') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>

            <form id="pt-question-content-create-form" method="POST" action="{{ route('placement_test_question_contents_store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="placement_test_level_id" class="form-label">{{ __('dictt.level') }}</label>
                        <select id="placement_test_level_id" name="placement_test_level_id"
                            class="form-control @error('placement_test_level_id') is-invalid @enderror" required>
                            <option value="">{{ __('dictt.select_level') }}</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" @selected((string) $initialLevelId === (string) $level->id)>
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
                        <label for="text_content_editor" class="form-label">{{ __('dictt.shared_content_text') }}</label>
                        <div id="text_content_editor" class="quill-editor @error('text_content') is-invalid @enderror"
                            data-quill-editor data-quill-input="text_content"></div>
                        <textarea id="text_content" name="text_content" rows="8" class="d-none">{{ old('text_content') }}</textarea>
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
                        <div class="form-text" x-text="mediaFormatHints[type]"></div>
                    </div>
                </div>

                <input type="hidden" name="is_active" value="1">

            </form>
        </div>
    </div>
</x-app-layout>
