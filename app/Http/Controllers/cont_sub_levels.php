<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_sub_levels;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class cont_sub_levels extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return view('admin.sub_levels.create');
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

        model_sub_levels::create([
            'name' => Str::upper($request->name),
            'slug' => Str::slug($request->name)
        ]);
        return redirect()->route('sub_levels_list')->with('success', Lang::get('dictt.subleveladdsuccess'));
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $sub_level_id)
    {
        $sub_level = model_sub_levels::find($sub_level_id);
        return view('admin.sub_levels.edit', compact('sub_level'));
    }

    public function update(Request $request, string $sub_level_id)
    {
        $request->validate([
            'name' => 'required|min:3|max:200|'
        ], [
            'name.required' => Lang::get('dictt.required_name'),
            'name.min' => Lang::get('dictt.mincharacter_name'),
            'name.max' => Lang::get('dictt.maxcharacter_name'),
        ]);

        model_sub_levels::where('id', $sub_level_id)->update([
            'name' => Str::upper($request->name),
            'slug' => Str::slug($request->name)
        ]);
        return redirect()->route('sub_levels_list')->with('success', Lang::get('dictt.sublevelupdatesuccess'));
    }

    public function destroy(string $sub_level_id)
    {
        model_sub_levels::findOrFail($sub_level_id)->delete();
        return redirect()->route('sub_levels_list')
            ->with('success', Lang::get('dictt.subleveldeletesuccess'));

        /*         model_sub_levels::find($sub_level_id)->whereId($sub_level_id)->delete();
                return redirect()->back()->with('success', Lang::get('dictt.subleveldeletesuccess')); */
    }
}
