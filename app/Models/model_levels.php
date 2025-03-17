<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_levels extends Model
{
    use HasFactory;
    public $table = "levels";
    protected $fillable = [
        'name',
        'slug'
    ];
}
