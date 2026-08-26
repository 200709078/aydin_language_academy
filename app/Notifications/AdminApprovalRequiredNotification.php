<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<string>  $lines
     */
    public function __construct(
        private readonly string $subjectLine,
        private readonly array $lines,
        private readonly string $actionLabel,
        private readonly string $actionUrl,
    ) {
        $this->afterCommit();
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->subjectLine)
            ->greeting('Merhaba,');

        foreach ($this->lines as $line) {
            $message->line($line);
        }

        return $message
            ->action($this->actionLabel, $this->actionUrl)
            ->line('Bu otomatik bir ALA yönetim bildirimidir.');
    }
}
