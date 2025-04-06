<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_declarations;
use Illuminate\Http\Request;

class cont_declarations extends Controller
{
    public function index()
    {
        $declarations = model_declarations::get();
        return $declarations;// view("front.themes", compact("themes"));
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
