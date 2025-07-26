<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\model_levels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class cont_levels extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return view('admin.levels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:200|'
        ], [
            'name.required' => Lang::get('dictt.required_name'),
            'name.min' => Lang::get('dictt.mincharacter_name'),
            'name.max' => Lang::get('dictt.maxcharacter_name'),
        ]);

        model_levels::create([
            'name' => ucwords(Str::upper($request->name)),
            'slug' => Str::slug($request->name)
        ]);
        return redirect()->route('levels_list')->with('success', Lang::get('dictt.leveladdsuccess'));
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $level_id)
    {
        $level = model_levels::find($level_id);
        return view('admin.levels.edit', compact('level'));
    }

    public function update(Request $request, string $level_id)
    {
        model_levels::where('id', $level_id)->update([
            'name' => ucwords(Str::upper($request->name)),
            'slug' => Str::slug($request->name)
        ]);
        return redirect()->route('levels_list')->with('success', Lang::get('dictt.levelupdatesuccess'));
    }

    public function destroy(string $level_id)
    {
        model_levels::find($level_id)->whereId($level_id)->delete();
        return redirect()->back()->with('success', Lang::get('dictt.leveldeletesuccess'));
    }
}
