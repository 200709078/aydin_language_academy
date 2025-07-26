<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_exercises;
use Illuminate\Http\Request;
use App\Models\model_themes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class cont_exercises extends Controller
{
    public function index()
    {
        $exercises = model_exercises::withCount('questions');
        if (request()->get('title')) {
            $exercises = $exercises->where('title', 'like', '%' . request()->get('title') . '%');
        }
        if (request()->get('status')) {
            $exercises = $exercises->where('status', request()->get('status'));
        }
        $exercises = $exercises->orderByDesc('created_at')->paginate(5);
        //return view('admin.exercises.list', compact('exercises'));


        //$theme=model_themes::whereId( $theme_id )->with('exercises')->first()??abort(404,'EXERCISES NOT FOUND');
        return view("admin.exercises.list", compact("exercises"));
    }
    public function create($theme_id)
    {
        $theme = model_themes::find($theme_id);
        return view('admin.exercises.create', compact('theme'));
    }

    public function store(Request $request, $theme_id)
    {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->title) . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
         model_exercises::create([
            'theme_id' => $theme_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'qtext' => $request->qtext,
            'image' => $imageFileName,
            'video' => $request->video,
            'voice' => $request->voice

        ]);
        return redirect()->route('exercises_list', $theme_id)->with('success', Lang::get('dictt.exercisesaddsuccess')); 
    }

    public function show(string $id)
    {
        $exercises = model_exercises::with('topTen.user', 'results.user')->withCount('questions')->find($id) ?? abort(404, "EXERCISES NOT FOUND.");
        return 'GELDİM';//  view('admin.exercises.show', compact('exercises'));
    }

    public function edit(string $exercise_id)
    {
        $exercise = model_exercises::find($exercise_id);
        return view('admin.exercises.edit', compact('exercise'));
    }

    public function update(Request $request, string $exercise_id)
    {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->title) . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }

        $exercise = model_exercises::find($exercise_id);
        model_exercises::where('id', $exercise_id)->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'qtext' => $request->qtext,
            'image' => $imageFileName,
            'video' => $request->video,
            'voice' => $request->voice
        ]);
        return redirect()->route('exercises_list', $exercise->theme_id)->with('success', Lang::get('dictt.exercisesupdatesuccess'));
    }

    public function destroy(string $id)
    {
        $exercises = model_exercises::find($id)->delete();
        return redirect()->back()->with('success', Lang::get('dictt.exercisesdeletesuccess'));
    }
}
