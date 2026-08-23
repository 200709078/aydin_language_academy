<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;

class ThemeListController extends Controller
{
    public function index(string $levelSlug, string $subLevelSlug)
    {
        $level = model_levels::where('slug', $levelSlug)->firstOrFail();
        $subLevel = model_sub_levels::where('slug', $subLevelSlug)->firstOrFail();

        $themes = model_themes::query()
            ->where('level_id', $level->id)
            ->where('sub_level_id', $subLevel->id)
            ->with(['levels', 'sub_levels'])
            ->orderBy('name')
            ->get();

        return view('frontend.themes.themes', compact('level', 'subLevel', 'themes'));
    }

    public function show(string $themeId)
    {
        $theme = model_themes::query()
            ->where('id', $themeId)
            ->with(['levels', 'sub_levels'])
            ->first();

        abort_unless($theme !== null, 404);

        $declarations = model_declarations::query()
            ->where('theme_id', $theme->id)
            ->latest()
            ->get();

        $exercises = model_exercises::query()
            ->where('theme_id', $theme->id)
            ->with('questions')
            ->get();

        return view('frontend.themes.detail', [
            'theme' => $theme,
            'declarations' => $declarations,
            'exercises' => $exercises,
        ]);
    }
}
