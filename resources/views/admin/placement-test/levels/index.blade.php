<x-app-layout>
    <x-slot name="header">Levels</x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Kapat"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="card-title mb-1">Seviye Tespit Sınavı Seviyeleri</h5>
                    <p class="text-muted small mb-0">
                        A1–C2 seviyeleri sabittir. Buradaki ayarlar yalnızca bundan sonraki sınavları etkiler.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Seviye</th>
                            <th scope="col">Sıra</th>
                            <th scope="col">Sınav</th>
                            <th scope="col">Aktif Soru</th>
                            <th scope="col">Hedef Soru Sayısı</th>
                            <th scope="col">Geçme Yüzdesi</th>
                            <th scope="col">Durum</th>
                            <th scope="col">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($levels as $level)
                            <tr>
                                <th scope="row">{{ $level->code }}</th>
                                <td>{{ $level->sequence }}</td>
                                <td>
                                    @if ($level->has_exam)
                                        <span class="badge text-bg-primary">Var</span>
                                    @else
                                        <span class="badge text-bg-secondary">Yok</span>
                                    @endif
                                </td>
                                <td>{{ $level->has_exam ? $level->active_questions_count : '—' }}</td>
                                <td>
                                    @if (! $level->has_exam)
                                        <span class="text-muted">0</span>
                                    @elseif ($level->question_count === null)
                                        <span class="text-muted">Belirlenmedi</span>
                                    @elseif ($level->question_count === $level->active_questions_count)
                                        <span class="badge text-bg-success">{{ $level->question_count }}</span>
                                    @else
                                        <span class="badge text-bg-warning">{{ $level->question_count }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($level->has_exam)
                                        %{{ (int) $level->pass_percentage }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($level->is_active)
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('placement_test_levels_edit', $level) }}" class="btn btn-sm btn-primary"
                                        title="Düzenle">
                                        <i class="fa fa-pen w-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Henüz seviye bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-muted small mb-0 mt-3">
                Aktif olan tüm sorular sınava atanır. Hedef soru sayısı tanımlanmışsa, sınav başlatılmadan önce aktif soru sayısıyla eşitliği denetlenir.
            </p>
        </div>
    </div>
</x-app-layout>
