<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipExamPeriod extends Model
{
    protected $fillable = [
        'title',
        'description',
        'applications_open_at',
        'applications_close_at',
        'exam_starts_on',
        'exam_ends_on',
        'is_active',
        'applications_open',
    ];

    protected $attributes = [
        'is_active' => false,
        'applications_open' => false,
    ];

    protected function casts(): array
    {
        return [
            'applications_open_at' => 'immutable_datetime',
            'applications_close_at' => 'immutable_datetime',
            'exam_starts_on' => 'immutable_date',
            'exam_ends_on' => 'immutable_date',
            'is_active' => 'boolean',
            'applications_open' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ScholarshipExamSession::class, 'period_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ScholarshipApplication::class, 'period_id');
    }

    public function acceptsApplications(?CarbonImmutable $at = null): bool
    {
        $at ??= CarbonImmutable::now(config('app.timezone'));

        return $this->is_active
            && $this->applications_open
            && $this->applications_open_at !== null
            && $this->applications_close_at !== null
            && $at->betweenIncluded($this->applications_open_at, $this->applications_close_at);
    }
}
