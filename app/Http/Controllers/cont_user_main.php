<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_courses;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_levels;
use App\Models\model_messages;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;

class cont_user_main extends Controller
{
    public function __construct()
    {
        $data['levels'] = model_levels::all();
        $data['sub_levels'] = model_sub_levels::all();

        view()->share($data);
    }
    public function theme_detail($theme_id)
    {
        $theme = model_themes::whereId($theme_id)->first() ?? abort(404, Lang::get('dictt.themenotfound'));
        return view("front.theme_detail", compact('theme'));
    }
    public function index()
    {
        $courses = model_courses::orderBy('created_at', 'desc')->paginate(6);
        return view('front.home', compact('courses'));
    }

    public function themes($level_slug, $sub_level_slug)
    {
        $themes = model_themes::whereHas('levels', fn($l) => $l->where('slug', $level_slug))
            ->whereHas('sub_levels', fn($sl) => $sl->where('slug', $sub_level_slug))
            ->with(['levels', 'sub_levels'])
            ->get();

        return view('front.themes', ['themes' => $themes]);
    }
    public function about()
    {
        return view('front.about');
    }
    public function contact()
    {
        return view('front.contact');
    }
    public function contactpost(Request $request)
    {
        $newMessage = new model_messages();
        $newMessage->fullname = $request->fullname;
        $newMessage->email = $request->email;
        $newMessage->telephone = $request->telephone;
        $newMessage->subject = $request->subject;
        $newMessage->message = $request->message;
        $newMessage->save();

        Mail::raw($request->message, function ($message) use ($request) {
            $message->from($request->email, $request->fullname);
            $message->to('ademvarol0808@hotmail.com', 'Adem VAROL');
            $message->subject($request->subject);
        });

        return redirect('contact')->with('success', Lang::get('dictt.messagesended'));
    }

    public function tab1($theme_id)
    {
        $declarations = model_declarations::where('theme_id', '=', $theme_id)->paginate(1) ?? abort(404, 'DECLARATION NOT FOUND');
        $exercises = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->paginate(1) ?? abort(404, 'EXERCISE NOT FOUND');
        $themes = model_themes::whereId($theme_id)->with(['levels', 'sub_levels'])->get() ?? abort(404, Lang::get('dictt.themenotfound'));
        return view('front.theme_detail', compact(['declarations', 'exercises', 'themes']));
    }
    public function tab2($theme_id)
    {
        $declarations = model_declarations::where('theme_id', '=', $theme_id)->paginate(1) ?? abort(404, 'DECLARATION NOT FOUND');
        $exercises = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->paginate(1) ?? abort(404, 'EXERCISE NOT FOUND');
        $themes = model_themes::whereId($theme_id)->with(['levels', 'sub_levels'])->get() ?? abort(404, Lang::get('dictt.themenotfound'));
        return view('front.theme_detail', compact(['declarations', 'exercises', 'themes']));
    }

    public function exercises_result(Request $request, $theme_id)
    {
        /* 
            $exercises = model_exercises::with('questions')->whereSlug($slug)->first() ?? abort(404, 'EXERCISES NOT FOUND.');
    $correct = 0;
    if ($exercises->my_result != null) {
        abort(404, 'You have join this exercises before.');
    }

    foreach ($exercises->questions as $question) {
        model_user_answers::create([
            'user_id' => auth()->user()->id,
            'question_id' => $question->id,
            'user_select' => $request->post($question->id)
        ]);
        if ($question->correct_answer === $request->post($question->id)) {
            $correct += 1;
        }
    }
    $point = round((100 / count($exercises->questions)) * $correct, 2);
    $wrong = count($exercises->questions) - $correct;
    model_results::create([
        'user_id' => auth()->user()->id,
        'exercises_id' => $exercises->id,
        'point' => $point,
        'correct_number' => $correct,
        'wrong_number' => $wrong
    ]);
    return redirect()->route('exercises.detail', $exercises->slug)->with('success', 'YOUR SUCCESSFULLY TO EXERCISES. Your Point:' . $point);
        */

        //$declarations = model_declarations::where('theme_id', '=', $theme_id)->get() ?? abort(404, 'THEME NOT FOUND');
        //$exercises = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->get() ?? abort(404, 'THEME NOT FOUND');
        //return view('front.theme_detail', compact(['declarations', 'exercises', 'theme_id', 'request']));
        $exercises = model_exercises::with('questions')->whereId($theme_id)->first() ?? abort(404, Lang::get('dictt.exercisesnotfound'));
        //return $exercises;
        return Lang::get('dictt.commingsoon');
        //return redirect($request->session()->previousUrl());
    }

    public function levels_list()
    {
        $levels = model_levels::orderBy('updated_at', 'desc')->get();
        return view("admin.levels.list", compact('levels'));
    }
    public function sub_levels_list()
    {
        $sub_levels = model_sub_levels::orderBy('updated_at', 'desc')->get();
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

}
