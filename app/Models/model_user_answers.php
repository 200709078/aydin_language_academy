<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_user_answers extends Model
{
    use HasFactory;
    public $table = "user_answers";
    protected $fillable = [
        'user_id',
        'question_id',
        'user_select'
    ];
}
