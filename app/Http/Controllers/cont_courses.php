<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\model_courses;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use Illuminate\Http\Request;

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
        $courses = model_courses::orderBy('created_at', 'desc')->paginate(6);
        return view('courses.list', compact('courses'));
    }
    public function create($theme_id)
    {

    }

    public function store(Request $request, $theme_id)
    {

    }

    public function show(string $id)
    {
        $course = model_courses::findOrFail($id);
        return view('front.course_detail', compact('course'));
    }

    public function edit(string $exercise_id)
    {

    }

    public function update(Request $request, string $exercise_id)
    {

    }

    public function destroy(string $exercise_id)
    {

    }
}
