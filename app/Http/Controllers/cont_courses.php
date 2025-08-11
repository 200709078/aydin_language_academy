<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_courses;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class cont_courses extends Controller
{
    public function __construct()
    {
        $data['levels'] = model_levels::all();
        $data['sub_levels'] = model_sub_levels::all();
        view()->share($data);
    }
    public function index()
    {
        //
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_tr' => 'required|min:3|',
            'title_en' => 'required|min:3|',
            'slogan_tr' => 'required|min:3|',
            'slogan_en' => 'required|min:3|',
            'description_tr' => 'required|min:3|',
            'description_en' => 'required|min:3|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'title_tr.required' => __('dictt.required_item', ['name' => __('dictt.title') . ' (tr)']),
            'title_tr.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title') . ' (tr)', 'number' => 3]),
            'title_en.required' => __('dictt.required_item', ['name' => __('dictt.title') . ' (en)']),
            'title_en.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title') . ' (en)', 'number' => 3]),
            'slogan_tr.required' => __('dictt.required_item', ['name' => __('dictt.slogan') . ' (tr)']),
            'slogan_tr.min' => __('dictt.mincharacter_item', ['name' => __('dictt.slogan') . ' (tr)', 'number' => 3]),
            'slogan_en.required' => __('dictt.required_item', ['name' => __('dictt.slogan') . ' (en)']),
            'slogan_en.min' => __('dictt.mincharacter_item', ['name' => __('dictt.slogan') . ' (en)', 'number' => 3]),
            'description_tr.required' => __('dictt.required_item', ['name' => __('dictt.description') . ' (tr)']),
            'description_tr.min' => __('dictt.mincharacter_item', ['name' => __('dictt.description') . ' (tr)', 'number' => 3]),
            'description_en.required' => __('dictt.required_item', ['name' => __('dictt.description') . ' (en)']),
            'description_en.min' => __('dictt.mincharacter_item', ['name' => __('dictt.description') . ' (en)', 'number' => 3]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }

        $course = model_courses::create([
            'title_tr' => $request->title_tr,
            'title_en' => $request->title_en,
            'slogan_tr' => $request->slogan_tr,
            'slogan_en' => $request->slogan_en,
            'description_tr' => $request->description_tr,
            'description_en' => $request->description_en,
            'image' => $imageFileName,
        ]);

        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.course')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => Str::lower(__('dictt.course')), 'name' => $course->title_tr]);
        return redirect()->route('courses_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function show(string $id)
    {
        $course = model_courses::findOrFail($id);
        return view('front.course_detail', compact('course'));
    }

    public function edit(string $course_id)
    {
        $course = model_courses::find($course_id);
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, string $course_id)
    {
        $request->validate([
            'title_tr' => 'required|min:3|',
            'title_en' => 'required|min:3|',
            'slogan_tr' => 'required|min:3|',
            'slogan_en' => 'required|min:3|',
            'description_tr' => 'required|min:3|',
            'description_en' => 'required|min:3|',
            'image' => 'image|nullable|max:1024|mimes:jpg,jpeg,png',
        ], [
            'title_tr.required' => __('dictt.required_item', ['name' => __('dictt.title') . ' (tr)']),
            'title_tr.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title') . ' (tr)', 'number' => 3]),
            'title_en.required' => __('dictt.required_item', ['name' => __('dictt.title') . ' (en)']),
            'title_en.min' => __('dictt.mincharacter_item', ['name' => __('dictt.title') . ' (en)', 'number' => 3]),
            'slogan_tr.required' => __('dictt.required_item', ['name' => __('dictt.slogan') . ' (tr)']),
            'slogan_tr.min' => __('dictt.mincharacter_item', ['name' => __('dictt.slogan') . ' (tr)', 'number' => 3]),
            'slogan_en.required' => __('dictt.required_item', ['name' => __('dictt.slogan') . ' (en)']),
            'slogan_en.min' => __('dictt.mincharacter_item', ['name' => __('dictt.slogan') . ' (en)', 'number' => 3]),
            'description_tr.required' => __('dictt.required_item', ['name' => __('dictt.description') . ' (tr)']),
            'description_tr.min' => __('dictt.mincharacter_item', ['name' => __('dictt.description') . ' (tr)', 'number' => 3]),
            'description_en.required' => __('dictt.required_item', ['name' => __('dictt.description') . ' (en)']),
            'description_en.min' => __('dictt.mincharacter_item', ['name' => __('dictt.description') . ' (en)', 'number' => 3]),
            'image.max' => __('dictt.imagemaxsize'),
            'image.mimes' => __('dictt.imagemimes'),
        ]);

        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        $course = model_courses::find($course_id);
        $course->title_tr = $request->title_tr;
        $course->title_en = $request->title_en;
        $course->slogan_tr = $request->slogan_tr;
        $course->slogan_en = $request->slogan_en;
        $course->description_tr = $request->description_tr;
        $course->description_en = $request->description_en;
        if ($request->hasFile('image')) {
            $course->image = $imageFileName;
        }
        $course->save();

        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.course')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.course'), 'name' => $course->title_en]);
        return redirect()->route('courses_list')
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function destroy(string $course_id)
    {
        //
    }
}
