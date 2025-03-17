<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_sub_levels extends Model
{
    use HasFactory;
    public $table = "sub_levels";
    protected $fillable = [
        'name',
        'slug'
    ];
}
