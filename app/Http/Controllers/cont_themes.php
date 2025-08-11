<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\model_themes;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class cont_themes extends Controller
{
    public function __construct()
    {
        $data['levels'] = model_levels::all();
        $data['sub_levels'] = model_sub_levels::all();
        view()->share($data);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        return view('admin.themes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:255|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'name.required' => __('dictt.required_item', ['name' => __('dictt.themename')]),
            'name.min' => __('dictt.mincharacter_item', ['name' => __('dictt.themename'), 'number' => 3]),
            'name.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.themename'), 'number' => 255]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = Str::slug($request->name) . '.' . $request->image->extension();
            $fileNamePath = 'photos/' . $fileName;
            $request->image->move(public_path('photos'), $fileNamePath);
        }
        $theme = model_themes::create([
            'level_id' => $request->level_id,
            'sub_level_id' => $request->sub_level_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $fileName,
        ]);

        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.theme')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => __('dictt.theme'), 'name' => $theme->name]);
        return redirect()->route('themes_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $theme_id)
    {
        $theme = model_themes::find($theme_id);
        return view('admin.themes.edit', compact('theme'));
    }

    public function update(Request $request, string $theme_id)
    {
        $request->validate([
            'name' => 'required|min:3|max:255|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'name.required' => __('dictt.required_item', ['name' => __('dictt.themename')]),
            'name.min' => __('dictt.mincharacter_item', ['name' => __('dictt.themename'), 'number' => 3]),
            'name.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.themename'), 'number' => 255]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->name) . '.' . $request->image->extension();
            $fileNamePath = 'photos/' . $imageFileName;
            $request->image->move(public_path('photos'), $fileNamePath);
        }

        $theme = model_themes::find($theme_id);
        $theme->level_id = $request->level_id;
        $theme->sub_level_id = $request->sub_level_id;
        $theme->name = ucwords(Str::lower($request->name));
        $theme->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            $theme->image = $imageFileName;
        }
        $theme->save();

        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.theme')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.theme'), 'name' => $theme->name]);
        return redirect()->route('themes_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function destroy(string $theme_id)
    {
        //
    }
}
