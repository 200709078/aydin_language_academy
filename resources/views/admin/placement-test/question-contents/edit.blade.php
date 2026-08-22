<x-app-layout>
    <x-slot name="header">Ortak İçerik Düzenle</x-slot>

    @php
        $typeLabels = [
            'text' => 'Metin',
            'audio' => 'Ses',
            'image' => 'Görsel',
            'video' => 'Video',
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ route('placement_test_question_contents_list') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> Geri dön
                </a>
            </h5>

            <form method="POST" action="{{ route('placement_test_question_contents_update', $placementTestQuestionContent) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">Seviye</label>
                        <input id="level" type="text" class="form-control" value="{{ $placementTestQuestionContent->level->code }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">İçerik Türü</label>
                        <input id="type" type="text" class="form-control" value="{{ $typeLabels[$placementTestQuestionContent->type] }}" readonly>
                    </div>
                </div>

                @if ($placementTestQuestionContent->type === 'text')
                    <div class="form-group mb-3">
                        <label for="text_content" class="form-label">Ortak İçerik Metni</label>
                        <textarea id="text_content" name="text_content" rows="8"
                            class="form-control @error('text_content') is-invalid @enderror" required>{{ old('text_content', $placementTestQuestionContent->text_content) }}</textarea>
                        @error('text_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="form-group mb-3">
                        <label for="media_file" class="form-label">Yeni Medya Dosyası</label>
                        <input id="media_file" name="media_file" type="file"
                            class="form-control @error('media_file') is-invalid @enderror"
                            accept="{{ match ($placementTestQuestionContent->type) { 'audio' => '.mp3,.wav,.ogg,.m4a,.aac', 'image' => '.jpg,.jpeg,.png,.webp', 'video' => '.mp4,.webm,.ogv' } }}">
                        @error('media_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Boş bırakırsanız mevcut dosya korunur. Yeni dosya benzersiz bir yola kaydedilir; eski dosya geçmiş sınav kayıtları için silinmez.
                        </div>
                        <a href="{{ route('placement_test_question_contents_media', $placementTestQuestionContent) }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fa fa-up-right-from-square"></i> Mevcut Medyayı Aç
                        </a>
                    </div>
                @endif

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1"
                        @checked(old('is_active', $placementTestQuestionContent->is_active))>
                    <label for="is_active" class="form-check-label">Bu ortak içerik aktif</label>
                    @error('is_active')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-sm">Ayarları Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
