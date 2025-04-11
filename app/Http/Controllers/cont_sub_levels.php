<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubLevelRequest;
use App\Models\model_sub_levels;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function store(SubLevelRequest $request)
    {
        $request->merge([
            'slug' => Str::slug($request->name),
        ]);

        model_sub_levels::create($request->post());
        return redirect()->route('sub_levels_list')->with('success', 'SUB LEVEL ADD SUCCESSFULLY...');
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
        model_sub_levels::where('id', $sub_level_id)->update([
            'name'=>ucwords(Str::lower($request->name)),
            'slug'=>Str::slug($request->name)
            ]);
        return redirect()->route('sub_levels_list')->with('success', 'SUB LEVEL UPDATE SUCCESSFULLY...'); 
    }

    public function destroy(string $sub_level_id)
    {
        model_sub_levels::find($sub_level_id)->whereId($sub_level_id)->delete();
        return redirect()->back()->with('success', 'SUB LEVEL DELETE SUCCESSFULLY...');
    }
}
