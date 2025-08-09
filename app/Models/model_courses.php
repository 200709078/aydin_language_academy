<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class model_courses extends Model
{
    use HasFactory;
    public $table = "courses";
    protected $fillable = [
        'title_tr',
        'title_en',
        'slogan_tr',
        'slogan_en',
        'description_tr',
        'description_en',
        'image',
    ];
}
