<?php

namespace App\Jobs;

use App\Services\ScholarshipNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendScholarshipNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 50;

    public bool $failOnTimeout = true;

    public function __construct(public readonly int $notificationId)
    {
        $this->afterCommit();
    }

    public function handle(ScholarshipNotificationService $notifications): void
    {
        $notifications->deliver($this->notificationId);
    }

    public function failed(?Throwable $exception): void
    {
        app(ScholarshipNotificationService::class)->failDelivery($this->notificationId);
    }
}
