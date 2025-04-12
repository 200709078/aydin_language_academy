<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThemeRequest;
use Illuminate\Http\Request;
use App\Models\model_themes;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use Illuminate\Support\Str;

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

    public function store(ThemeRequest $request)
    {
        //$request->except(['_token', '_method']);
        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = Str::slug($request->name) . '.' . $request->image->extension();
            $fileNamePath = 'photos/' . $fileName;
            $request->image->move(public_path('photos'), $fileNamePath);
        }
        model_themes::create([
            'level_id' => $request->level_id,
            'sub_level_id' => $request->sub_level_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $fileName,
        ]);

        return redirect()->route('themes_list')->with('success', 'THEME ADD SUCCESSFULLY...');
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
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->name) . '.' . $request->image->extension();
            $fileNamePath = 'photos/' . $imageFileName;
            $request->image->move(public_path('photos'), $fileNamePath);
        }

        model_themes::where('id', $theme_id)->update([
            'level_id' => $request->level_id,
            'sub_level_id' => $request->sub_level_id,
            'name' => ucwords(Str::lower($request->name)),
            'slug' => Str::slug($request->name),
            'image' => $imageFileName
        ]);
        return redirect()->route('themes_list')->with('success', 'THEME UPDATE SUCCESSFULLY...');
    }

    public function destroy(string $theme_id)
    {
        model_themes::find($theme_id)->whereId($theme_id)->delete();
        return redirect()->back()->with('success', 'THEME DELETE SUCCESSFULLY...');
    }
}
