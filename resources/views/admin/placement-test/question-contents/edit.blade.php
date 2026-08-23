<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit_shared_content') }}</x-slot>

    @php
        $typeLabels = [
            'text' => __('dictt.content_type_text'),
            'audio' => __('dictt.content_type_audio'),
            'image' => __('dictt.content_type_image'),
            'video' => __('dictt.content_type_video'),
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('placement_test_question_contents_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('dictt.back') }}
                </a>
            </h5>

            <form method="POST" action="{{ route('placement_test_question_contents_update', $placementTestQuestionContent) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">{{ __('dictt.level') }}</label>
                        <input id="level" type="text" class="form-control" value="{{ $placementTestQuestionContent->level->code }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">{{ __('dictt.content_type') }}</label>
                        <input id="type" type="text" class="form-control" value="{{ $typeLabels[$placementTestQuestionContent->type] }}" readonly>
                    </div>
                </div>

                @if ($placementTestQuestionContent->type === 'text')
                    <div class="form-group mb-3">
                        <label for="text_content" class="form-label">{{ __('dictt.shared_content_text') }}</label>
                        <textarea id="text_content" name="text_content" rows="8"
                            class="form-control @error('text_content') is-invalid @enderror" required>{{ old('text_content', $placementTestQuestionContent->text_content) }}</textarea>
                        @error('text_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="form-group mb-3">
                        <label for="media_file" class="form-label">{{ __('dictt.new_media_file') }}</label>
                        <input id="media_file" name="media_file" type="file"
                            class="form-control @error('media_file') is-invalid @enderror"
                            accept="{{ match ($placementTestQuestionContent->type) { 'audio' => '.mp3,.wav,.ogg,.m4a,.aac', 'image' => '.jpg,.jpeg,.png,.webp', 'video' => '.mp4,.webm,.ogv' } }}">
                        @error('media_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('dictt.pt_media_replace_help') }}
                        </div>
                        <a href="{{ route('placement_test_question_contents_media', $placementTestQuestionContent) }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fa fa-up-right-from-square"></i> {{ __('dictt.open_current_media') }}
                        </a>
                    </div>
                @endif

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1"
                        @checked(old('is_active', $placementTestQuestionContent->is_active))>
                    <label for="is_active" class="form-check-label">{{ __('dictt.pt_content_active_label') }}</label>
                    @error('is_active')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('dictt.save_settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
