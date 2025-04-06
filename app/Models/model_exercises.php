<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use function Laravel\Prompts\alert;

class model_exercises extends Model
{
    use HasFactory, Sluggable;
    public $table = "exercises";
    protected $fillable = [
        'theme_id',
        'title',
        'slug',
        'description'
    ];
     protected $appends = ['details'];
 /*   public function getMyRankAttribute()
    {
        $rank = 0;
        foreach ($this->results()->orderByDesc('point')->get() as $result) {
            $rank += 1;
            if (auth()->user()->id === $result->user_id) {
                return $rank;
            }
        }
    } */

     public function getDetailsAttribute()
    {
        /* if ($this->results()->count() > 0) { */
            return [
/*                 'average' => round($this->results()->avg('point'), 2),
                'join_count' => $this->results()->count(), */
                'questions_count' => $this->questions()->count()
            ];
       /*  }
        return null; */
    } 
    public function questions()
    {
        return $this->hasMany(model_questions::class, 'exercises_id');
    }
/*     public function results()
    {
        return $this->hasMany(model_results::class, 'exercises_id');
    }
    public function topTen()
    {
        return $this->results()->orderByDesc('point')->take(10);
    }
    public function my_result()
    {
        return $this->hasOne(model_results::class, 'exercises_id')->where('user_id', auth()->user()->id);
    }
    public function getFinishedAtAttribute($date)
    {
        return $date ? Carbon::parse($date) : null;
    } */

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
