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
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Headers;

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
        $courses = model_courses::orderBy('created_at', 'desc')->get();
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
        $modalSuccessTitle = null;
        $modalSuccessContent = null;
        return view('front.contact', compact(['modalSuccessTitle', 'modalSuccessContent']));
    }
    public function contactpost(Request $request)
    {

        $request->validate([
            'fullname' => 'required|min:3|max:100|',
            'email' => 'required|email|',
            'telephone' => 'digits:10|',
            'subject' => 'required|min:3|max:150|',
            'message' => 'required|min:10|max:2000|',
        ], [
            'fullname.required' => __('dictt.required_item', ['name' => __('dictt.fullname')]),
            'fullname.min' => __('dictt.mincharacter_item', ['name' => __('dictt.fullname'), 'number' => 3]),
            'fullname.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.fullname'), 'number' => 100]),
            'email.required' => __('dictt.required_item', ['name' => __('dictt.email')]),
            'email.email' => __('dictt.emailvalidation_item', ['name' => __('dictt.email')]),
            'telephone.digits' => __('dictt.digit_character_item', ['name' => __('dictt.phone'), 'number' => 10]),
            'subject.required' => __('dictt.required_item', ['name' => __('dictt.subject')]),
            'subject.min' => __('dictt.mincharacter_item', ['name' => __('dictt.subject'), 'number' => 3]),
            'subject.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.subject'), 'number' => 150]),
            'message.required' => __('dictt.required_item', ['name' => __('dictt.message')]),
            'message.min' => __('dictt.mincharacter_item', ['name' => __('dictt.message'), 'number' => 10]),
            'message.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.message'), 'number' => 2000]),
        ]);

        $newMessage = new model_messages();
        $newMessage->fullname = $request->fullname;
        $newMessage->email = $request->email;
        $newMessage->telephone = $request->telephone;
        $newMessage->subject = $request->subject;
        $newMessage->message = $request->message;
        $newMessage->save();

        Mail::send([], [], function ($message) use ($request) {
            $message->to('learnenglishwithala@gmail.com', 'Adem VAROL')
                ->subject($request->subject)
                ->html(
                    "<b>Ad Soyad:</b> {$request->fullname}<br>
             <b>Email:</b> {$request->email}<br>
             <b>Telefon:</b> {$request->telephone}<br><br>
             <b>Mesaj:</b><br>" . nl2br(e($request->message))
                );
        });

        $modalSuccessTitle = __('dictt.sendmessagesuccesstitle');
        $modalSuccessContent = __('dictt.sendmessagesuccesscontent');

        return redirect()->route('contact')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
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
