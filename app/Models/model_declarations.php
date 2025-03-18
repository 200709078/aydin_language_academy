<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class model_declarations extends Model
{
    public $table = "declarations";
    protected $fillable = [
        'title',
        'slug',
        'content',
        'theme_id'
    ];
}
