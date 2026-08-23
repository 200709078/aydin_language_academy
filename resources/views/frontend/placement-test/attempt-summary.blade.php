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
            $durationParts[] = "{$days} " . __('dictt.placement_test_duration_days');
        }

        if ($hours > 0) {
            $durationParts[] = "{$hours} " . __('dictt.placement_test_duration_hours');
        }

        if ($minutes > 0) {
            $durationParts[] = "{$minutes} " . __('dictt.placement_test_duration_minutes');
        }

        $durationParts[] = "{$seconds} " . __('dictt.placement_test_duration_seconds');

        $duration = implode(' ', $durationParts);
    }
@endphp

<dl class="row text-start mb-4">
    <dt class="col-sm-5 mb-2">{{ __('dictt.placement_test_started_at') }}</dt>
    <dd class="col-sm-7 mb-2">{{ $startedAt?->format('d.m.Y H:i:s') ?? '-' }}</dd>
    <dt class="col-sm-5 mb-2">{{ __('dictt.placement_test_finished_at') }}</dt>
    <dd class="col-sm-7 mb-2">{{ $finishedAt?->format('d.m.Y H:i:s') ?? '-' }}</dd>
    <dt class="col-sm-5 mb-0">{{ __('dictt.placement_test_duration') }}</dt>
    <dd class="col-sm-7 mb-0">{{ $duration }}</dd>
</dl>

@if ($placementTest->status === 'approved')
    <p class="mb-0 text-success fw-bold">{{ __('dictt.placement_test_approved') }}</p>
@else
    <p class="mb-0 text-primary fw-bold">{{ __('dictt.placement_test_awaiting_approval') }}</p>
@endif
