<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubLevelCreateRequest;
use App\Models\model_sub_levels;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class cont_sub_levels extends Controller
{
    public function index()
    {
        $sub_levels = model_sub_levels::get();
        return $sub_levels;// view("front.themes", compact("themes"));
    }

    public function create()
    {
        return view('admin.sub_levels.create');
    }

    public function store(SubLevelCreateRequest $request)
    {
        $request->merge([
            'slug' => Str::slug($request->name),
        ]);

        model_sub_levels::create($request->post());
        return redirect()->route('sub_levels_list')->with('success', 'NEW SUB LEVELS ADD SUCCESSFULLY...');
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

    public function destroy(string $sub_level_id)
    {
        model_sub_levels::find($sub_level_id)->whereId($sub_level_id)->delete();
        return redirect()->back()->with('success', 'SUB LEVEL DELETE SUCCESSFULLY...');
    }
}
