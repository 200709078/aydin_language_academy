<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageReplyNotification extends Notification
{
    public function __construct(
        private readonly string $recipientName,
        private readonly string $subjectLine,
        private readonly string $body,
        private readonly string $messageLocale,
    ) {
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
            ->greeting(__('dictt.contact_reply_greeting', ['name' => $this->recipientName], $this->messageLocale));

        foreach (preg_split('/\R{2,}/u', trim($this->body)) ?: [] as $paragraph) {
            $message->line($paragraph);
        }

        $replyAddress = trim((string) config('mail.from.address'));

        if (filter_var($replyAddress, FILTER_VALIDATE_EMAIL) !== false) {
            $message->replyTo($replyAddress, (string) config('mail.from.name'));
        }

        return $message->salutation('ALA');
    }
}
