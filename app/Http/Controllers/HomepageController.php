<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function __construct()
    {
        $data['levels'] = model_levels::all();
        $data['sub_levels'] = model_sub_levels::all();
        view()->share($data);
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
}
