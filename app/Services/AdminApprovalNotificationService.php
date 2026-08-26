<?php

namespace App\Services;

use App\Models\PlacementTest;
use App\Models\Review;
use App\Notifications\AdminApprovalRequiredNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class AdminApprovalNotificationService
{
    public function placementTestSubmitted(PlacementTest $attempt): void
    {
        $attempt->loadMissing(['user', 'resultLevel']);

        $this->send(
            subject: 'Seviye tespit sınavı onay bekliyor',
            lines: [
                'Öğrenci: ' . ($attempt->user?->name ?: ('#' . $attempt->user_id)),
                'Sınav kaydı: #' . $attempt->id,
                'Belirlenen seviye: ' . ($attempt->resultLevel?->code ?: '-'),
                'Gönderim zamanı: ' . ($attempt->submitted_at?->format('d.m.Y H:i') ?: '-'),
            ],
            actionLabel: 'Sınavı İncele',
            actionUrl: route('placement_test_attempts_show', $attempt),
        );
    }

    public function reviewCreated(Review $review): void
    {
        $this->reviewPendingApproval($review, 'Yeni yorum onay bekliyor', 'İşlem: Yeni yorum gönderildi');
    }

    public function reviewUpdated(Review $review, bool $wasRejected): void
    {
        $this->reviewPendingApproval(
            $review,
            $wasRejected ? 'Yorum tekrar onaya gönderildi' : 'Onay bekleyen yorum güncellendi',
            $wasRejected ? 'İşlem: Reddedilmiş yorum tekrar gönderildi' : 'İşlem: Onay bekleyen yorum güncellendi',
        );
    }

    private function reviewPendingApproval(Review $review, string $subject, string $eventLine): void
    {
        $review->loadMissing('user');

        $this->send(
            subject: $subject,
            lines: [
                $eventLine,
                'Üye: ' . ($review->user?->name ?: ('#' . $review->user_id)),
                'Yorum kaydı: #' . $review->id,
                'Puan: ' . $review->rating . '/5',
                'Şube: ' . $review->branchLabel(),
                'Gönderim zamanı: ' . $review->updated_at?->format('d.m.Y H:i'),
            ],
            actionLabel: 'Yorumları İncele',
            actionUrl: route('reviews_list'),
        );
    }

    /**
     * @param  list<string>  $lines
     */
    private function send(
        string $subject,
        array $lines,
        string $actionLabel,
        string $actionUrl,
    ): void {
        $recipient = trim((string) config('admin_notifications.recipient'));

        if (
            filter_var($recipient, FILTER_VALIDATE_EMAIL) === false
            || ! str_ends_with(strtolower($recipient), '@gmail.com')
        ) {
            Log::warning('Admin e-posta bildirimi gönderilmedi: Gmail alıcısı yapılandırılmamış.', [
                'subject' => $subject,
            ]);

            return;
        }

        try {
            Notification::route('mail', $recipient)->notify(
                new AdminApprovalRequiredNotification($subject, $lines, $actionLabel, $actionUrl),
            );
        } catch (Throwable $exception) {
            Log::error('Admin e-posta bildirimi kuyruklanamadı.', [
                'subject' => $subject,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
