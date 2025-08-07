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
            'name' => 'required|min:3|max:255|'
        ], [
            'name.required' => Lang::get('dictt.required_name'),
            'name.min' => Lang::get('dictt.mincharacter_name'),
            'name.max' => Lang::get('dictt.maxcharacter_name'),
        ]);


        $sub_level = model_sub_levels::create([
            'name' => Str::upper($request->name),
            'slug' => Str::slug($request->name)
        ]);

        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.level')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => Str::lower(__('dictt.level')), 'name' => $sub_level->name]);
        return redirect()->route('levels_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
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
            'name' => 'required|min:3|max:255|'
        ], [
            'name.required' => Lang::get('dictt.required_name'),
            'name.min' => Lang::get('dictt.mincharacter_name'),
            'name.max' => Lang::get('dictt.maxcharacter_name'),
        ]);

        $sub_level = model_sub_levels::find($sub_level_id);

        $sub_level->name = Str::upper($request->name);
        $sub_level->slug = Str::slug($request->name);
        $sub_level->save();
        
        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.sublevel')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => Str::lower(__('dictt.sublevel')), 'name' => $sub_level->name]);
        return redirect()->route('sub_levels_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);}

    public function destroy(string $sub_level_id)
    {
        model_sub_levels::findOrFail($sub_level_id)->delete();
        return redirect()->route('sub_levels_list')
            ->with('success', Lang::get('dictt.subleveldeletesuccess'));
    }
}
