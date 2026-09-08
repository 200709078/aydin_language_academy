<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipNotification extends Model
{
    protected $fillable = [
        'application_id',
        'phase',
        'channel',
        'status',
        'idempotency_key',
        'recipient',
        'subject',
        'body',
        'provider',
        'provider_message_id',
        'attempts',
        'queued_at',
        'sent_at',
        'failed_at',
        'failure_reason',
    ];

    protected $attributes = [
        'status' => 'queued',
        'attempts' => 0,
    ];

    protected function casts(): array
    {
        return [
            'application_id' => 'integer',
            'attempts' => 'integer',
            'queued_at' => 'immutable_datetime',
            'sent_at' => 'immutable_datetime',
            'failed_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(ScholarshipApplication::class, 'application_id');
    }
}
