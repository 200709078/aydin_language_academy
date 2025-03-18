<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_questions extends Model
{
    public $table = "questions";
    use HasFactory;
    protected $fillable = [
        'question',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'answer5',
        'correct_answer',
        'exercises_id'
    ];
}
