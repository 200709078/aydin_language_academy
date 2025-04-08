<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\model_levels;

class cont_levels extends Controller
{
    public function index()
    {
        $levels = model_levels::get();
        return $levels;// view("front.themes", compact("themes"));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
