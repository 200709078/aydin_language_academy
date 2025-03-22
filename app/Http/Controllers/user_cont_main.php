<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Http\Request;

class user_cont_main extends Controller
{
    public function __construct()
    {
        $data['levels'] = model_levels::all();
        $data['sub_levels'] = model_sub_levels::all();
        view()->share($data);
    }
    public function theme_detail($theme_id)
    {
        $theme = model_themes::whereId($theme_id)->first() ?? abort(404, "THEME NOT FOUND.");
        return view("front.theme_detail", compact('theme'));
    }
    public function index()
    {
        return view('front.home');
    }

    public function levels($level_id, $sub_level_id)
    {
        $themes = model_themes::with('levels', 'sub_levels', 'declarations', 'exercises.questions')->where('level_id', '=', $level_id)->where('sub_level_id', '=', $sub_level_id)->get('*');
        return view('front.themes', compact('themes'));
    }
    public function about()
    {
        return view('front.about');
    }
    public function contact()
    {
        return view('front.contact');
    }

    public function tab1($theme_id)
    {
        $theme = model_exercises::whereId($theme_id)->first() ?? abort(404, 'THEME NOT FOUND');
        return view('front.theme_detail', compact('theme'));
    }
    public function tab2($theme_id)
    {
        $theme = model_exercises::whereId($theme_id)->first() ?? abort(404, 'THEME NOT FOUND');
        return view('front.theme_detail', compact('theme'));
    }

}
