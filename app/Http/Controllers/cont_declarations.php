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
        $request->validate([
            'title' => 'required|min:3|max:255|',
            'context' => 'required|min:3|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'title.required' => __('dictt.required_item', ['name' => __('dictt.title')]),
            'title.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title'), 'number' => 3]),
            'title.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.title'), 'number' => 255]),
            'context.required' => __('dictt.required_item', ['name' => __('dictt.content')]),
            'context.min' => __('dictt.mincharacter_item', ['name' => __('dictt.content'), 'number' => 3]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

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

        $declaration = model_declarations::create([
            'theme_id' => $theme_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'context' => $request->context,
            'image' => $imageFileName,
            'pdf' => $pdfFileName,
            'video' => $request->video,
            'voice' => $request->voice
        ]);
        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.declaration')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => __('dictt.declaration'), 'name' => $declaration->title]);

        return redirect()->route('declarations_list', ['theme_id' => $theme_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
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
        $request->validate([
            'title' => 'required|min:3|max:255|',
            'context' => 'required|min:3|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'title.required' => __('dictt.required_item', ['name' => __('dictt.title')]),
            'title.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title'), 'number' => 3]),
            'title.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.title'), 'number' => 255]),
            'context.required' => __('dictt.required_item', ['name' => __('dictt.content')]),
            'context.min' => __('dictt.mincharacter_item', ['name' => __('dictt.content'), 'number' => 3]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

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
            'context' => $request->context,
            'image' => $imageFileName,
            'pdf' => $pdfFileName,
            'video' => $request->video,
            'voice' => $request->voice
        ]);
        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.declaration')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => Str::lower(__('dictt.declaration')), 'name' => $declaration->title]);

        return redirect()->route('declarations_list', ['theme_id' => $declaration->theme_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function destroy(string $declaration_id)
    {
        //
    }
}
