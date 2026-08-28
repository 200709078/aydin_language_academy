<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_exercises;
use Illuminate\Http\Request;
use App\Models\model_themes;
use Illuminate\Support\Str;

class cont_exercises extends Controller
{
    public function create($theme_id)
    {
        $theme = model_themes::find($theme_id);
        return view('admin.exercises.create', compact('theme'));
    }

    public function store(Request $request, $theme_id)
    {
        $request->validate([
            'title' => 'required|min:3|max:255|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'title.required' => __('dictt.required_item', ['name' => __('dictt.title')]),
            'title.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title'), 'number' => 3]),
            'title.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.title'), 'number' => 255]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->title) . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        $exercise = model_exercises::create([
            'theme_id' => $theme_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'qtext' => $request->qtext,
            'image' => $imageFileName,
            'video' => $request->video,
            'voice' => $request->voice

        ]);
        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.exercise')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => __('dictt.exercise'), 'name' => $exercise->title]);

        return redirect()->route('exercises_list', ['theme_id' => $theme_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function edit(string $exercise_id)
    {
        $exercise = model_exercises::find($exercise_id);
        return view('admin.exercises.edit', compact('exercise'));
    }

    public function update(Request $request, string $exercise_id)
    {
        $request->validate([
            'title' => 'required|min:3|max:255|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'title.required' => __('dictt.required_item', ['name' => __('dictt.title')]),
            'title.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title'), 'number' => 3]),
            'title.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.title'), 'number' => 255]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->title) . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }

        $exercise = model_exercises::find($exercise_id);
        $exercise->title = $request->title;
        $exercise->slug = Str::slug($request->title);
        $exercise->qtext = $request->qtext;
        if ($request->hasFile('image')) {
            $exercise->image = $imageFileName;
        }
        $exercise->video = $request->video;
        $exercise->voice = $request->voice;
        $exercise->save();

        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.exercise')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.exercise'), 'name' => $exercise->title]);

        return redirect()->route('exercises_list', ['theme_id' => $exercise->theme_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }
}
