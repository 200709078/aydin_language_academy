<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_exercises extends Model
{
    use HasFactory;
    public $table = "exercises";
    protected $fillable = [
        'title',
        'slug',
        'description',
        'theme_id'
    ];
    public function questions()
    {
        return $this->hasMany(model_questions::class,'exercises_id');
    }
}
