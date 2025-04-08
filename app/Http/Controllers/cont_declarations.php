<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_declarations;
use Illuminate\Http\Request;

class cont_declarations extends Controller
{
    public function index()
    {
        //
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

    public function destroy(string $declaration_id)
    {
        model_declarations::find($declaration_id)->whereId($declaration_id)->delete();
        return redirect()->back()->with('success', 'DECLARATION DELETE SUCCESSFULLY...');
    }
}
