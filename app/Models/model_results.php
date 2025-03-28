<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_results extends Model
{
    use HasFactory;
    public $table = "user_results";
    protected $fillable = [
        'user_id',
        'exercises_id',
        'point',
        'correct_number',
        'wrong_number'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function exercises()
    {
        return $this->belongsTo(model_exercises::class, 'exercises_id');
    }
}
