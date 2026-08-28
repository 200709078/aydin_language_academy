<?php

namespace App\Jobs;

use App\Models\MessageReply;
use App\Models\model_messages;
use App\Notifications\ContactMessageReplyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Throwable;

class SendContactMessageReply implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 50;

    public function __construct(public readonly int $replyId)
    {
        $this->afterCommit();
    }

    public function handle(): void
    {
        $reply = MessageReply::query()
            ->with('message')
            ->find($this->replyId);

        if ($reply === null || $reply->delivery_status !== MessageReply::STATUS_QUEUED) {
            return;
        }

        Notification::route('mail', $reply->recipient_email)->notify(
            new ContactMessageReplyNotification(
                $reply->message?->fullname ?? '',
                $reply->subject,
                $reply->body,
            ),
        );

        $sentAt = now();

        DB::transaction(function () use ($reply, $sentAt): void {
            $reply->forceFill([
                'delivery_status' => MessageReply::STATUS_SENT,
                'sent_at' => $sentAt,
                'failed_at' => null,
                'failure_reason' => null,
            ])->save();

            model_messages::query()
                ->whereKey($reply->message_id)
                ->update([
                    'last_replied_at' => $sentAt,
                    'last_replied_by' => $reply->sent_by,
                    'updated_at' => $sentAt,
                ]);
        });
    }

    public function failed(?Throwable $exception): void
    {
        $reply = MessageReply::query()->find($this->replyId);

        if ($reply === null || $reply->delivery_status !== MessageReply::STATUS_QUEUED) {
            return;
        }

        $failedAt = now();

        $reply->forceFill([
            'delivery_status' => MessageReply::STATUS_FAILED,
            'failed_at' => $failedAt,
            'failure_reason' => Str::limit((string) $exception?->getMessage(), 1000),
        ])->save();

        Log::error('İletişim mesajı yanıtı gönderilemedi.', [
            'message_reply_id' => $reply->id,
            'message_id' => $reply->message_id,
        ]);
    }
}
