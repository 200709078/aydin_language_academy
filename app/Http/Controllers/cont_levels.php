<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LevelRequest;
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

    public function store(LevelRequest $request)
    {
        $request->merge([
            'slug' => Str::slug($request->name),
        ]);

        model_levels::create($request->post());

/*         flash()
            ->option('position', 'top-center')  // Position on the screen
            ->option('timeout', 5000)           // How long to display (milliseconds)
            ->success('Your changes have been saved!')
            ->setTitle('SSSSS'); */

        return redirect()->route('levels_list')->with('success','LEVEL ADD SUCCESSFULLY...');
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

    public function destroy(string $level_id)
    {
        model_levels::find($level_id)->whereId($level_id)->delete();
        return redirect()->back()->with('success', 'LEVEL DELETE SUCCESSFULLY...');
    }
}
