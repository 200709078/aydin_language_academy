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
    protected $appends = ['details'];

    public function getDetailsAttribute()
    {
        return [
            'questions_count' => $this->questions()->count(),
        ];
    }
    public function questions()
    {
        return $this->hasMany(model_questions::class, 'exercise_id');
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
