<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_ARCHIVED = 'archived';

    public const BRANCHES = ['ortaca', 'dalaman', 'koycegiz'];

    protected $fillable = [
        'user_id',
        'branch',
        'content',
        'rating',
        'status',
        'approved_by',
        'approved_at',
        'display_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (Review $review) {
            if ($review->display_order === null) {
                $review->display_order = static::withTrashed()->count() + 1;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function branchLabel(): string
    {
        return match ($this->branch) {
            'ortaca' => __('dictt.branch_ortaca'),
            'dalaman' => __('dictt.branch_dalaman'),
            'koycegiz' => __('dictt.branch_koycegiz'),
            default => __('dictt.branch_general'),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('dictt.status_pending'),
            self::STATUS_APPROVED => __('dictt.status_approved'),
            self::STATUS_REJECTED => __('dictt.status_rejected'),
            self::STATUS_ARCHIVED => __('dictt.status_archived'),
            default => $this->status,
        };
    }
}
