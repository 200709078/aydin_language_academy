<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class model_messages extends Model
{
    use HasFactory;

    public const STATUS_UNREAD = 'unread';

    public const STATUS_READ = 'read';

    public const STATUS_ARCHIVED = 'archived';

    public const BRANCHES = ['ortaca', 'dalaman', 'koycegiz'];

    protected $table = 'messages';

    protected $attributes = [
        'status' => self::STATUS_UNREAD,
    ];

    protected $fillable = [
        'fullname',
        'email',
        'telephone',
        'branch',
        'locale',
        'subject',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'last_replied_at' => 'datetime',
        ];
    }

    public function readBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function lastRepliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_replied_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class, 'message_id');
    }

    public function latestReply(): HasOne
    {
        return $this->hasOne(MessageReply::class, 'message_id')->latestOfMany();
    }

    public function branchLabel(): string
    {
        return match ($this->branch) {
            'ortaca' => __('dictt.branch_ortaca'),
            'dalaman' => __('dictt.branch_dalaman'),
            'koycegiz' => __('dictt.branch_koycegiz'),
            default => __('dictt.none'),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status ?? self::STATUS_UNREAD) {
            self::STATUS_UNREAD => __('dictt.message_status_unread'),
            self::STATUS_READ => __('dictt.message_status_read'),
            self::STATUS_ARCHIVED => __('dictt.message_status_archived'),
            default => $this->status,
        };
    }
}
