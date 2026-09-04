<?php

namespace App\Http\Controllers;

use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Models\model_themes;
use App\Support\LegacyExerciseMedia;
use Illuminate\Support\Facades\Storage;

class LegacyExerciseMediaController extends Controller
{
    public function themeImage(model_themes $theme)
    {
        return $this->response($theme->image, LegacyExerciseMedia::THEME_IMAGES);
    }

    public function exerciseImage(model_exercises $exercise)
    {
        return $this->response($exercise->image, LegacyExerciseMedia::EXERCISE_IMAGES);
    }

    public function questionImage(model_questions $question)
    {
        return $this->response($question->image, LegacyExerciseMedia::QUESTION_IMAGES);
    }

    public function declarationImage(model_declarations $declaration)
    {
        return $this->response($declaration->image, LegacyExerciseMedia::DECLARATION_IMAGES);
    }

    public function declarationDocument(model_declarations $declaration, string $document)
    {
        $path = match ($document) {
            'pdf' => $declaration->pdf,
            'answerkey' => $declaration->answerkey,
            default => null,
        };

        return $this->response($path, LegacyExerciseMedia::DECLARATION_DOCUMENTS);
    }

    private function response(?string $path, string $directory)
    {
        if (! LegacyExerciseMedia::isSafePathForDirectory($path, $directory)) {
            abort(404);
        }

        $disk = Storage::disk(LegacyExerciseMedia::DISK);

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
