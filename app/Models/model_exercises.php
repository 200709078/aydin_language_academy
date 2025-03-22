<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_exercises extends Model
{
    use HasFactory;
    public $table = "exercises";
    protected $fillable = [
        'theme_id',
        'title',
        'slug',
        'description'
    ];
    public function questions()
    {
        return $this->hasMany(model_questions::class, 'exercises_id');
    }
}
