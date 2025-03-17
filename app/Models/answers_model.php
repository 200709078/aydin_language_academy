<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class answers_model extends Model
{
    use HasFactory;
    public $table = "user_answers";
    protected $fillable = [
        'user_id',
        'question_id',
        'user_select'
    ];
}
