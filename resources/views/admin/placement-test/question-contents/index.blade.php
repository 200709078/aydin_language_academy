<x-app-layout>
    <x-slot name="header">Ortak İçerikler</x-slot>

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
            <a href="{{ route('placement_test_question_contents_create') }}" class="btn btn-sm btn-primary float-right">
                <i class="fa fa-plus"></i> Yeni Ortak İçerik Ekle
            </a>

            <h5 class="card-title mb-3">Ortak İçerikler</h5>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Seviye</th>
                            <th scope="col">Tür</th>
                            <th scope="col">İçerik</th>
                            <th scope="col">Bağlı Soru</th>
                            <th scope="col">Geçmiş Snapshot</th>
                            <th scope="col">Durum</th>
                            <th scope="col">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contents as $content)
                            <tr>
                                <th scope="row">{{ $content->level->code }}</th>
                                <td>{{ $typeLabels[$content->type] }}</td>
                                <td class="text-break">
                                    @if ($content->type === 'text')
                                        {{ \Illuminate\Support\Str::limit($content->text_content, 120) }}
                                    @else
                                        <a href="{{ route('placement_test_question_contents_media', $content) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-up-right-from-square"></i> Medyayı Aç
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $content->questions_count }}</td>
                                <td>{{ $content->result_content_snapshots_count }}</td>
                                <td>
                                    @if ($content->is_active)
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <a href="{{ route('placement_test_question_contents_edit', $content) }}"
                                            class="btn btn-sm btn-primary" title="Düzenle">
                                            <i class="fa fa-pen w-4"></i>
                                        </a>

                                        @if ($content->questions_count === 0)
                                            <form method="POST"
                                                action="{{ route('placement_test_question_contents_destroy', $content) }}"
                                                onsubmit="return confirm('Bu ortak içerik silinsin mi? Varsa medya dosyası geçmiş kayıtları korumak için sunucuda kalacaktır.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled
                                                title="Bağlı sorular varken silinemez">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Henüz ortak içerik bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contents->hasPages())
                <div class="mt-3">
                    {{ $contents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
