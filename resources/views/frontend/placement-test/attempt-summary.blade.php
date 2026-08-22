@php
    $startedAt = $placementTest->started_at;
    $finishedAt = $placementTest->submitted_at;
    $duration = '-';

    if ($startedAt && $finishedAt) {
        $totalSeconds = (int) $startedAt->diffInSeconds($finishedAt);
        $days = intdiv($totalSeconds, 86400);
        $hours = intdiv($totalSeconds % 86400, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;
        $durationParts = [];

        if ($days > 0) {
            $durationParts[] = "{$days} gün";
        }

        if ($hours > 0) {
            $durationParts[] = "{$hours} saat";
        }

        if ($minutes > 0) {
            $durationParts[] = "{$minutes} dakika";
        }

        $durationParts[] = "{$seconds} saniye";

        $duration = implode(' ', $durationParts);
    }
@endphp

<dl class="row text-start mb-4">
    <dt class="col-sm-5 mb-2">Başlama Tarihi/Saati:</dt>
    <dd class="col-sm-7 mb-2">{{ $startedAt?->format('d.m.Y H:i:s') ?? '-' }}</dd>
    <dt class="col-sm-5 mb-2">Bitirme Tarihi/Saati:</dt>
    <dd class="col-sm-7 mb-2">{{ $finishedAt?->format('d.m.Y H:i:s') ?? '-' }}</dd>
    <dt class="col-sm-5 mb-0">Sınavda Geçen Süre:</dt>
    <dd class="col-sm-7 mb-0">{{ $duration }}</dd>
</dl>

@if ($placementTest->status === 'approved')
    <p class="mb-0 text-success fw-bold">Sınavınızın sonucu yönetici tarafından onaylandı.</p>
@else
    <p class="mb-0 text-primary fw-bold">Sınavınız tamamlandı. Yönetici onayı bekleniyor.</p>
@endif
