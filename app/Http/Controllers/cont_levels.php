<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\model_levels;
use Illuminate\Support\Str;

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
            'name' => 'required|min:3|max:20|'
        ], [
            'name.required' => __('dictt.required_item', ['name' => __('dictt.levelname')]),
            'name.min' => __('dictt.mincharacter_item', ['name' => __('dictt.levelname'), 'number' => 3]),
            'name.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.levelname'), 'number' => 20]),
        ]);

        $level = model_levels::create([
            'name' => Str::upper($request->name),
            'slug' => Str::slug($request->name)
        ]);

        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.level')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => __('dictt.level'), 'name' => $level->name]);
        return redirect()->route('levels_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
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
        $request->validate([
            'name' => 'required|min:3|max:20|'
        ], [
            'name.required' => __('dictt.required_item', ['name' => __('dictt.levelname')]),
            'name.min' => __('dictt.mincharacter_item', ['name' => __('dictt.levelname'), 'number' => 3]),
            'name.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.levelname'), 'number' => 20]),
        ]);

        $level = model_levels::find($level_id);

        $level->name = Str::upper($request->name);
        $level->slug = Str::slug($request->name);
        $level->save();

        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.level')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.level'), 'name' => $level->name]);
        return redirect()->route('levels_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function destroy(string $level_id)
    {
        //
    }
}
