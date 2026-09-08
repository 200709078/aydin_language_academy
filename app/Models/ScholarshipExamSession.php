<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipExamSession extends Model
{
    protected $fillable = [
        'period_id',
        'branch_id',
        'exam_group_id',
        'exam_title',
        'exam_date',
        'starts_at',
        'ends_at',
        'capacity',
        'is_active',
        'archived_at',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'period_id' => 'integer',
            'branch_id' => 'integer',
            'exam_group_id' => 'integer',
            'exam_date' => 'immutable_date',
            'capacity' => 'integer',
            'is_active' => 'boolean',
            'archived_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(ScholarshipExamPeriod::class, 'period_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(ScholarshipBranch::class, 'branch_id');
    }

    public function examGroup(): BelongsTo
    {
        return $this->belongsTo(ScholarshipExamGroup::class, 'exam_group_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ScholarshipApplication::class, 'session_id');
    }

    public function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::parse(
            $this->exam_date->format('Y-m-d').' '.$this->starts_at,
            config('app.timezone')
        );
    }
}
