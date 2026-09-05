<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementTestLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'sequence',
        'has_exam',
        'is_active',
        'question_count',
        'pass_percentage',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_exam' => 'boolean',
            'is_active' => 'boolean',
            'question_count' => 'integer',
            'pass_percentage' => 'decimal:2',
        ];
    }

    public function englishLevelCode(): ?string
    {
        return match (strtoupper((string) $this->code)) {
            'A1' => 'A0',
            'A2' => 'A1',
            'B1' => 'A2',
            'B2' => 'B1',
            'C1' => 'B2',
            'C2' => 'C1',
            default => null,
        };
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PlacementTestQuestion::class);
    }

    public function questionContents(): HasMany
    {
        return $this->hasMany(PlacementTestQuestionContent::class);
    }

    public function levelResults(): HasMany
    {
        return $this->hasMany(PlacementTestLevelResult::class);
    }
}
