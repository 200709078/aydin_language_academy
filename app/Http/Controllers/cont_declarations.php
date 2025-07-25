<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_declarations;
use App\Models\model_themes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class cont_declarations extends Controller
{
    public function index()
    {
        //
    }

    public function create($theme_id)
    {
        $theme = model_themes::find($theme_id);
        return view('admin.declarations.create', compact('theme'));
    }

    public function store(Request $request, $theme_id)
    {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->title) . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        $pdfFileName = null;
        if ($request->hasFile('pdf')) {
            $pdfFileName = Str::slug($request->title) . '.' . $request->pdf->extension();
            $request->pdf->move(public_path('pdfs'), $pdfFileName);
        }

        model_declarations::create([
            'theme_id' => $theme_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'contents' => $request->contents,
            'image' => $imageFileName,
            'pdf' => $pdfFileName,
            'video' => $request->video,
            'voice' => $request->voice
        ]);
        return redirect()->route('declarations_list', $theme_id)->with('success', Lang::get('dictt.declarationaddsuccess'));
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $declaration_id)
    {
        $declaration = model_declarations::find($declaration_id);
        return view('admin.declarations.edit', compact('declaration'));
    }

    public function update(Request $request, string $declaration_id)
    {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = Str::slug($request->title) . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        $pdfFileName = null;
        if ($request->hasFile('pdf')) {
            $pdfFileName = Str::slug($request->title) . '.' . $request->pdf->extension();
            $request->pdf->move(public_path('pdfs'), $pdfFileName);
        }

        $declaration = model_declarations::find($declaration_id);

        model_declarations::where('id', $declaration_id)->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'contents' => $request->contents,
            'image' => $imageFileName,
            'pdf' => $pdfFileName,
            'video' => $request->video,
            'voice' => $request->voice
        ]);
        return redirect()->route('declarations_list', $declaration->theme_id)->with('success', Lang::get('dictt.declarationupdatesuccess'));
    }

    public function destroy(string $declaration_id)
    {
        model_declarations::find($declaration_id)->whereId($declaration_id)->delete();
        return redirect()->back()->with('success', Lang::get('dictt.declarationdeletesuccess'));
    }
}
