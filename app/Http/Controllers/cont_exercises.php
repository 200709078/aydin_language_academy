<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExercisesUpdateRequest;
use App\Models\model_exercises;
use Illuminate\Http\Request;
use App\Models\model_themes;
use Illuminate\Support\Str;

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
        return view("admin.exercises.list",compact("exercises"));
    }
    public function create($theme_id)
    {
        $theme = model_themes::find($theme_id);
        return view('admin.exercises.create', compact('theme'));
    }

    public function store(Request $request, $theme_id)
    {
        model_exercises::create([
            'theme_id' => $theme_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description
        ]); 
        return redirect()->route('exercises_list', $theme_id)->with('success', 'EXERCISES ADD SUCCESSFULLY...');
    }

    public function show(string $id)
    {
        $exercises = model_exercises::with('topTen.user', 'results.user')->withCount('questions')->find($id) ?? abort(404, "EXERCISES NOT FOUND.");
        return 'GELDİM';//  view('admin.exercises.show', compact('exercises'));
    }


    public function edit(string $id)
    {
        $exercises = model_exercises::withCount('questions')->find($id) ?? abort(404, 'EXERCISES NOT FOUND');
        return view('admin.exercises.edit', compact('exercises'));
    }
    public function update(ExercisesUpdateRequest $request, string $id)
    {
        $request->merge([
            'slug' => Str::slug($request->title),
        ]);
        model_exercises::find($id) ?? abort(404, 'EXERCISES NOT FOUND');
        model_exercises::where('id', $id)->update($request->except(['_method', '_token']));
        return redirect()->route('exercises.index')->with('success', 'EXERCISES UPDATE SUCCESSFULLY...');
    }
    public function destroy(string $id)
    {
        $exercises = model_exercises::find($id)->delete();
        return redirect()->back()->with('success', 'EXERCISES DELETE SUCCESSFULLY...');
    }
}
