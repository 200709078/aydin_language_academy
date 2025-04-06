<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_sub_levels;
use Illuminate\Http\Request;

class cont_sub_levels extends Controller
{
    public function index()
    {
        $sub_levels = model_sub_levels::get();
        return $sub_levels;// view("front.themes", compact("themes"));
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

    public function destroy(string $id)
    {
        //
    }
}
