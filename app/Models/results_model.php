<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class results_model extends Model
{
    use HasFactory;
    public $table = "user_results";
    protected $fillable = [
        'user_id',
        'exam_id',
        'point',
        'correct_number',
        'wrong_number'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function exam()
    {
        return $this->belongsTo(exams_model::class, 'exam_id');
    }
}
