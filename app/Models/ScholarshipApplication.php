<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipApplication extends Model
{
    protected $fillable = [
        'application_number',
        'user_id',
        'period_id',
        'session_id',
        'school_id',
        'student_level_id',
        'student_name_snapshot',
        'school_name_snapshot',
        'student_level_name_snapshot',
        'status',
        'application_published',
        'application_contact_status',
        'attendance_status',
        'score',
        'scholarship_percentage',
        'result_published',
        'result_contact_status',
    ];

    protected $attributes = [
        'status' => 'pending',
        'application_published' => false,
        'application_contact_status' => 'unreached',
        'attendance_status' => 'unmarked',
        'result_published' => false,
        'result_contact_status' => 'unreached',
    ];

    // Member output must use the publication-aware presentation service.
    protected $hidden = [
        'status',
        'application_contact_status',
        'result_contact_status',
        'score',
        'scholarship_percentage',
        'notifications',
        'user',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'period_id' => 'integer',
            'session_id' => 'integer',
            'school_id' => 'integer',
            'student_level_id' => 'integer',
            'application_published' => 'boolean',
            'score' => 'integer',
            'scholarship_percentage' => 'integer',
            'result_published' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(ScholarshipExamPeriod::class, 'period_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ScholarshipExamSession::class, 'session_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(ScholarshipSchool::class, 'school_id');
    }

    public function studentLevel(): BelongsTo
    {
        return $this->belongsTo(ScholarshipStudentLevel::class, 'student_level_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ScholarshipNotification::class, 'application_id');
    }
}
