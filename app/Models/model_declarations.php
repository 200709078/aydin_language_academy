<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class model_declarations extends Model
{
    use HasFactory;
    public $table = "declarations";
    protected $fillable = [
        'theme_id',
        'title',
        'slug',
        'content'
    ];
}
