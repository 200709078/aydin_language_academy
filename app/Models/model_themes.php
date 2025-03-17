<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_themes extends Model
{
    use HasFactory;
    public $table = "themes";
    protected $fillable = [
        'name',
        'slug'
    ];
}
