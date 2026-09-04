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
        'context',
        'image',
        'pdf',
        'video',
        'voice',
        'answerkey'
    ];

    public function privateImageUrl(): ?string
    {
        if ($this->image === null || trim($this->image) === '' || $this->image === 'noimage.jpg') {
            return null;
        }

        return route('legacy.media.declarations.image', ['declaration' => $this]);
    }

    public function privatePdfUrl(): ?string
    {
        if ($this->pdf === null || trim($this->pdf) === '') {
            return null;
        }

        return route('legacy.media.declarations.document', [
            'declaration' => $this,
            'document' => 'pdf',
        ]);
    }

    public function privateAnswerkeyUrl(): ?string
    {
        if ($this->answerkey === null || trim($this->answerkey) === '') {
            return null;
        }

        return route('legacy.media.declarations.document', [
            'declaration' => $this,
            'document' => 'answerkey',
        ]);
    }
}
