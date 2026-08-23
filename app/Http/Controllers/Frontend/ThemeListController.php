<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
}
