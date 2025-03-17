<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\exams_model;
use App\Http\Requests\ExamCreateRequest;
use App\Http\Requests\ExamUpdateRequest;
use App\Models\results_model;
use Illuminate\Support\Str;

class exams_cont_admin extends Controller
{
    public function index()
    {
        $exams = exams_model::withCount('questions');
        if (request()->get('title')) {
            $exams = $exams->where('title', 'like', '%' . request()->get('title') . '%');
        }
        if (request()->get('status')) {
            $exams = $exams->where('status', request()->get('status'));
        }
        $exams = $exams->orderByDesc('created_at')->paginate(5);
        return view('admin.exams.list', compact('exams'));
    }
    public function create()
    {
        return view('admin.exams.create');
    }
    public function store(ExamCreateRequest $request)
    {
        exams_model::create($request->post());
        return redirect()->route('exams.index')->with('success', 'NEW EXAM ADD SUCCESSFULLY...');
    }



    public function show(string $id)
    {
        $exam = exams_model::with('topTen.user', 'results.user')->withCount('questions')->find($id) ?? abort(404, "EXAM NOT FOUND.");
        return view('admin.exams.show', compact('exam'));
    }


    public function edit(string $id)
    {
        $exam = exams_model::withCount('questions')->find($id) ?? abort(404, 'EXAM NOT FOUND');
        return view('admin.exams.edit', compact('exam'));
    }
    public function update(ExamUpdateRequest $request, string $id)
    {
        $request->merge([
            'slug' => Str::slug($request->title),
        ]);
        exams_model::find($id) ?? abort(404, 'EXAM NOT FOUND');
        exams_model::where('id', $id)->update($request->except(['_method', '_token']));
        return redirect()->route('exams.index')->with('success', 'EXAM UPDATE SUCCESSFULLY...');
    }
    public function destroy(string $id)
    {
        $exam = exams_model::find($id) ?? abort(404, 'EXAM NOT FOUND');
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'EXAM DELETE SUCCESSFULLY...');
    }
}
