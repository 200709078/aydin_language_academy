<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class questions_model extends Model
{
    public $table = "questions";
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'question',
        'image',
        'select1',
        'select2',
        'select3',
        'select4',
        'select5',
        'correct_answer'
    ];

    protected $appends = ['percent_true'];

    public function getPercentTrueAttribute()
    {
        $answer_count = $this->answers()->count();
        $true_answer_count = $this->answers()->where('user_select', $this->correct_answer)->count();
        return round((100 / $answer_count) * $true_answer_count,2);
    }

    public function answers()
    {
        return $this->hasMany(answers_model::class, 'question_id');
    }

    public function my_answer()
    {
        return $this->hasOne(answers_model::class, 'question_id')->where('user_id', auth()->user()->id);
    }
}
