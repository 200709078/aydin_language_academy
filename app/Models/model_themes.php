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
        'slug',
        'level_id',
        'sub_level_id'
    ];
    public function levels()
    {
        return $this->belongsTo(model_levels::class, 'level_id');
    }
    public function sub_levels()
    {
        return $this->belongsTo(model_sub_levels::class, 'sub_level_id');
    }
    public function exercises()
    {
        return $this->hasMany(model_exercises::class, 'theme_id');
    }
    public function declarations()
    {
        return $this->hasOne(model_declarations::class, 'theme_id');
    }
}
