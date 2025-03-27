<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_levels;
use App\Models\model_messages;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        return redirect('contact')->with('success', ' Teşekkürler mesajınız bize iletildi...');
    }

    public function tab1($theme_id)
    {
        $declarations = model_declarations::where('theme_id', '=', $theme_id)->get() ?? abort(404, 'THEME NOT FOUND');
        $exercises = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->get() ?? abort(404, 'THEME NOT FOUND');
        return view('front.theme_detail', compact(['declarations', 'exercises', 'theme_id']));
    }
    public function tab2($theme_id)
    {
        $declarations = model_declarations::where('theme_id', '=', $theme_id)->get() ?? abort(404, 'THEME NOT FOUND');
        $exercises = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->get() ?? abort(404, 'THEME NOT FOUND');
        return view('front.theme_detail', compact(['declarations', 'exercises', 'theme_id']));
    }

    public function exercises_result(Request $request, $theme_id)
    {
        //$declarations = model_declarations::where('theme_id', '=', $theme_id)->get() ?? abort(404, 'THEME NOT FOUND');
        //$exercises = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->get() ?? abort(404, 'THEME NOT FOUND');
        //return view('front.theme_detail', compact(['declarations', 'exercises', 'theme_id', 'request']));
        $exercises = model_exercises::with('questions')->whereId($theme_id)->get() ?? abort(404, 'EXAM NOT FOUND.');
        return $exercises;
        //return redirect($request->session()->previousUrl());
    }
    public function qq(string $theme_id)
    {
        $questions = model_exercises::where('theme_id', '=', $theme_id)->with('questions')->paginate(1);
        return $questions;
    }
}
