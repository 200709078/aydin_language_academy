<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_courses;
use App\Models\model_exercises;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class cont_user_main extends Controller
{
    public function __construct()
    {
        $data['levels'] = model_levels::all();
        $data['sub_levels'] = model_sub_levels::all();

        view()->share($data);
    }

    public function exercises_result(Request $request, $theme_id)
    {
    $exercises = model_exercises::with('questions')->whereId($theme_id)->first() ?? abort(404, Lang::get('dictt.exercisesnotfound'));
    return Lang::get('dictt.commingsoon');
    }

    public function courses_list()
    {
        $courses = model_courses::get();
        return view("admin.courses.list", compact('courses'));
    }
    public function levels_list()
    {
        $levels = model_levels::get();
        return view("admin.levels.list", compact('levels'));
    }
    public function sub_levels_list()
    {
        $sub_levels = model_sub_levels::get();
        return view("admin.sub_levels.list", compact('sub_levels'));
    }
    public function themes_list()
    {
        $themes = model_themes::with(['levels', 'sub_levels'])->orderBy('updated_at', 'desc')->paginate(5);
        return view("admin.themes.list", compact('themes'));
    }
    public function declarations_list($theme_id)
    {
        $theme = model_themes::whereId($theme_id)->with(
            [
                'declarations' => function ($query) {
                    $query->orderBy('updated_at', 'desc');
                }
            ]
        )->first();

        return view("admin.declarations.list", compact('theme_id'))->with('theme', $theme);
    }
    public function exercises_list($theme_id)
    {
        $theme = model_themes::whereId($theme_id)->with(
            [
                'exercises' => function ($query) {
                    $query->orderBy('updated_at', 'desc');
                }
            ]
        )->first();

        return view("admin.exercises.list", compact('theme_id'))
            ->with('theme', $theme);
    }
    public function questions_list($exercise_id)
    {

        $exercise = model_exercises::with([
            'questions' => function ($query) {
                $query->orderBy('updated_at', 'desc');
            }
        ])->findOrFail($exercise_id);
        $theme_id = $exercise->theme_id;

        return view("admin.questions.list", compact('exercise_id', 'exercise', 'theme_id'));
    }

    public function exercises_join($slug)
    {
        $exercises = model_exercises::whereSlug($slug)->with('questions.my_answer', 'my_result')->first() ?? abort(404, Lang::get('dictt.exercisesnotfound'));
        if ($exercises->my_result != null) {
            return view('exercises_result', compact('exercises'));
        }
        return view('exercises_join', compact('exercises'));
    }
    public function exercises_detail($slug)
    {
        $exercises = model_exercises::whereSlug($slug)->with('my_result', 'topTen.user')->withCount('questions')->first() ?? abort(404, Lang::get('dictt.exercisesnotfound'));
        return view("exercises_detail", compact('exercises'));
    }

    public function changeLanguage($lang)
    {
        if (in_array($lang, ['tr', 'en'])) {
            session()->put('locale', $lang);
        }
        return redirect()->back();
    }
}
