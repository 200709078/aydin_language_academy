<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class model_exercises extends Model
{
    use HasFactory, Sluggable;
    public $table = "exercises";
    protected $fillable = [
        'theme_id',
        'title',
        'slug',
        'image',
        'voice',
        'video',
        'qtext'
    ];

    public function questions()
    {
        return $this->hasMany(model_questions::class, 'exercise_id');
    }
    public function attempts()
    {
        return $this->hasMany(ExerciseAttempt::class, 'exercise_id');
    }
    public function theme()
    {
        return $this->belongsTo(model_themes::class, 'theme_id');
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function privateImageUrl(): ?string
    {
        if ($this->image === null || trim($this->image) === '' || $this->image === 'noimage.jpg') {
            return null;
        }

        return route('legacy.media.exercises.image', ['exercise' => $this]);
    }
}
