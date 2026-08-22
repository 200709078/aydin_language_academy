<x-app-layout>
    <x-slot name="header">Yeni Ortak İçerik Ekle</x-slot>

    <div class="card">
        <div class="card-body" x-data="{ type: @js(old('type', 'text')) }">
            <h5 class="card-title">
                <a href="{{ route('placement_test_question_contents_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> Geri dön
                </a>
            </h5>

            <form method="POST" action="{{ route('placement_test_question_contents_store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="placement_test_level_id" class="form-label">Seviye</label>
                        <select id="placement_test_level_id" name="placement_test_level_id"
                            class="form-control @error('placement_test_level_id') is-invalid @enderror" required>
                            <option value="">Seviye seçin</option>
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
                        <label for="type" class="form-label">İçerik Türü</label>
                        <select id="type" name="type" x-model="type"
                            class="form-control @error('type') is-invalid @enderror" required>
                            <option value="text">Metin</option>
                            <option value="audio">Ses</option>
                            <option value="image">Görsel</option>
                            <option value="video">Video</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div x-show="type === 'text'">
                    <div class="form-group mb-3">
                        <label for="text_content" class="form-label">Ortak İçerik Metni</label>
                        <textarea id="text_content" name="text_content" rows="8"
                            class="form-control @error('text_content') is-invalid @enderror"
                            x-bind:required="type === 'text'">{{ old('text_content') }}</textarea>
                        @error('text_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Reading parçası gibi, aynı gruptaki birden fazla soruda gösterilecek metni girin.</div>
                    </div>
                </div>

                <div x-show="type !== 'text'" style="display: none;">
                    <div class="form-group mb-3">
                        <label for="media_file" class="form-label">Medya Dosyası</label>
                        <input id="media_file" name="media_file" type="file"
                            class="form-control @error('media_file') is-invalid @enderror"
                            x-bind:required="type !== 'text'"
                            x-bind:accept="{ audio: '.mp3,.wav,.ogg,.m4a,.aac', image: '.jpg,.jpeg,.png,.webp', video: '.mp4,.webm,.ogv' }[type]">
                        @error('media_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Ses: MP3, WAV, OGG, M4A veya AAC. Görsel: JPG, PNG veya WebP. Video: MP4, WebM veya OGV. En fazla 10 MB.
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1"
                        @checked(old('is_active', true))>
                    <label for="is_active" class="form-check-label">Bu ortak içerik aktif</label>
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
