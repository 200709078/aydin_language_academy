<x-app-layout>
    <x-slot name="header">{{ __('dictt.edit_shared_content') }}</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 mb-2 mb-sm-0">
                    <a href="{{ route('placement_test_question_contents_list') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('dictt.back_short') }}
                    </a>
                </div>
                <h5 class="col-sm-4 card-title text-center mb-0">{{ __('dictt.edit_shared_content') }}</h5>
                <div class="d-none d-sm-block col-sm-4"></div>
            </div>

            <form method="POST" action="{{ route('placement_test_question_contents_update', $placementTestQuestionContent) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="level" class="form-label">{{ __('dictt.level') }}</label>
                        <input id="level" type="text" class="form-control" value="{{ $placementTestQuestionContent->level->code }}" readonly>
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
                        <div class="form-text">{{ __('dictt.pt_shared_text_help') }}</div>
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

                <input type="hidden" name="is_active"
                    value="{{ old('is_active', $placementTestQuestionContent->is_active) ? '1' : '0' }}">

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('dictt.save_settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
