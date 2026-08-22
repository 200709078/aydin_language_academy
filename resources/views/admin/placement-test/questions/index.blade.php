<x-app-layout>
    <x-slot name="header">Sorular</x-slot>

    @if (session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Kapat"></button>
        </div>
    @endif

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
            <a href="{{ route('placement_test_questions_create') }}" class="btn btn-sm btn-primary float-right">
                <i class="fa fa-plus"></i> Yeni Soru Ekle
            </a>

            <h5 class="card-title mb-1">Seviye Tespit Sınavı Soruları</h5>
            <p class="text-muted small mb-3">
                Aktif soruların tamamı sınava atanır. Aynı ortak içeriğe bağlı sorular grup içi sıraya göre birlikte gösterilir.
            </p>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Seviye</th>
                            <th scope="col">Soru</th>
                            <th scope="col">Ortak İçerik</th>
                            <th scope="col">Grup Sırası</th>
                            <th scope="col">Puan</th>
                            <th scope="col">Şık</th>
                            <th scope="col">Geçmiş Snapshot</th>
                            <th scope="col">Durum</th>
                            <th scope="col">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $question)
                            <tr>
                                <th scope="row">{{ $question->level?->code ?? '—' }}</th>
                                <td class="text-break">{{ \Illuminate\Support\Str::limit($question->question_text, 110) }}</td>
                                <td>
                                    @if ($question->questionContent)
                                        <a href="{{ route('placement_test_question_contents_edit', $question->questionContent) }}"
                                            class="text-decoration-none">
                                            {{ $typeLabels[$question->questionContent->type] }} #{{ $question->questionContent->id }}
                                        </a>
                                    @else
                                        <span class="text-muted">Bağımsız</span>
                                    @endif
                                </td>
                                <td>{{ $question->content_position ?? '—' }}</td>
                                <td>{{ number_format((float) $question->points, 2, ',', '.') }}</td>
                                <td>{{ $question->options_count }}</td>
                                <td>{{ $question->level_question_snapshots_count }}</td>
                                <td>
                                    @if ($question->is_active)
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $deletePrompt = $question->level_question_snapshots_count > 0
                                            ? 'Bu soru silinsin mi? Geçmiş sınav snapshot kayıtları korunacak, ancak kaynak soru bağlantısı kaldırılacaktır.'
                                            : 'Bu soru silinsin mi?';
                                    @endphp

                                    <div class="flex gap-1">
                                        <a href="{{ route('placement_test_questions_edit', $question) }}" class="btn btn-sm btn-primary"
                                            title="Düzenle">
                                            <i class="fa fa-pen w-4"></i>
                                        </a>
                                        <form method="POST" action="{{ route('placement_test_questions_destroy', $question) }}"
                                            onsubmit="return confirm(@js($deletePrompt));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Henüz soru bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($questions->hasPages())
                <div class="mt-3">
                    {{ $questions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
