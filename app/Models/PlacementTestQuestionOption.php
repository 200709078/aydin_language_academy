<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class PlacementTestQuestionOption extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $option): void {
            $option->option_text = trim((string) $option->option_text);

            if ($option->option_text === '') {
                throw new LogicException('Şık metni boş olamaz.');
            }

            if (
                $option->display_position === null
                || ! is_numeric($option->display_position)
                || (int) $option->display_position < 1
            ) {
                throw new LogicException('Şık sıra numarası pozitif olmalıdır.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'placement_test_question_id',
        'option_text',
        'display_position',
        'is_correct',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'display_position' => 'integer',
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PlacementTestQuestion::class, 'placement_test_question_id');
    }
}
