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

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $theme_id)
    {
        model_themes::find($theme_id)->whereId($theme_id)->delete();
        return redirect()->back()->with('success', 'THEME DELETE SUCCESSFULLY...');
    }
}
