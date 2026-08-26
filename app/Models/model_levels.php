<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class model_levels extends Model
{
    use HasFactory;
    public $table = "levels";
    protected $fillable = [
        'name',
        'slug'
    ];

    public function themes(): HasMany
    {
        return $this->hasMany(model_themes::class, 'level_id');
    }
}
