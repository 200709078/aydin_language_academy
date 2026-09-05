<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slogan extends Model
{
    protected $fillable = [
        'title_tr',
        'title_en',
    ];
}