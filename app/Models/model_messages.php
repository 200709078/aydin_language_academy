<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_messages extends Model
{
    use HasFactory;
    protected $table = "messages";
    protected $fillable = [
        "fullname",
        "email",
        "telephone",
        "subject",
        "message"
    ];
}
