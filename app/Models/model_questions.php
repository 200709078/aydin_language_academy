<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_questions extends Model
{
    use HasFactory;
    public $table = "questions";
    
    protected $fillable = [
        'exercises_id',
        'question',
        'image',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'answer5',
        'correct_answer'
    ];

/*     protected $appends = ['percent_true'];

    public function getPercentTrueAttribute()
    {
        $answer_count = $this->answers()->count();
        $true_answer_count = $this->answers()->where('user_answers', $this->correct_answer)->count();
        return round((100 / $answer_count) * $true_answer_count,2);
    } */

/*     public function answers()
    {
        return $this->hasMany(model_user_answers::class, 'question_id');
    } */

/*     public function my_answer()
    {
        return $this->hasOne(model_user_answers::class, 'question_id')->where('user_id', auth()->user()->id);
    } */
}
