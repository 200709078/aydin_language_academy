<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReply extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $table = 'contact_message_replies';

    protected $fillable = [
        'message_id',
        'sent_by',
        'recipient_email',
        'subject',
        'body',
        'delivery_status',
        'queued_at',
    ];

    protected function casts(): array
    {
        return [
            'queued_at' => 'datetime',
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(model_messages::class, 'message_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function deliveryStatusLabel(): string
    {
        return match ($this->delivery_status) {
            self::STATUS_QUEUED => __('dictt.message_delivery_queued'),
            self::STATUS_SENT => __('dictt.message_delivery_sent'),
            self::STATUS_FAILED => __('dictt.message_delivery_failed'),
            default => $this->delivery_status,
        };
    }
}
