<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\questions_model;
use App\Models\exams_model;
use App\Http\Requests\QuestionCreateRequest;
use App\Http\Requests\QuestionUpdateRequest;
use Illuminate\Support\Str;
class questions_cont_admin extends Controller
{
    public function index($id)
    {
        $exam = exams_model::whereId($id)->with('questions')->first() ?? abort(404, 'QUIZ NOT FOUND');
        return view('admin.questions.list', compact('exam'));
    }
    public function create($id)
    {
        $exam = exams_model::find($id);
        return view('admin.questions.create', compact('exam'));
    }
    public function store(QuestionCreateRequest $request, $id)
    {
        if ($request->hasFile('image')) {
            $fileName = Str::slug($request->question) . '.' . $request->image->extension();
            $fileNamePath = 'questions_photo/' . $fileName;
            $request->image->move(public_path('questions_photo'), $fileNamePath);
            $request->merge([
                'image' => $fileNamePath,
            ]);
        }
        exams_model::find($id)->questions()->create($request->post());
        return redirect()->route('questions.index', $id)->with('success', 'QUESTION ADD SUCCESSFULLY');
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $exam_id, string $question_id)
    {
        $question = exams_model::find($exam_id)->questions()->whereId($question_id)->first() ?? abort(404, 'EXAM OR QUESTION NOT FOUND.');
        return view('admin.questions.edit', compact('question'));
    }
    public function update(QuestionUpdateRequest $request, string $exam_id, string $question_id)
    {
        //$request->except(['_token', '_method']);
        if ($request->hasFile('image')) {
            $fileName = Str::slug($request->question) . '.' . $request->image->extension();
            $fileNamePath = 'questions_photo/' . $fileName;
            $request->image->move(public_path('questions_photo'), $fileNamePath);
            $request->merge([
                'image' => $fileNamePath,
            ]);
        }

        exams_model::find($exam_id) ?? abort(404, 'EXAM NOT FOUND');
        questions_model::find($question_id) ?? abort(404, 'QUESTION NOT FOUND');
        questions_model::where('id', $question_id)->update($request->except(['_method', '_token']));
        return redirect()->route('questions.index', $exam_id)->with('success', 'QUESTION UPDATE SUCCESSFULLY...');
    }
    public function destroy(string $exam_id, string $question_id)
    {
        exams_model::find($exam_id)->questions()->whereId($question_id)->delete();
        return redirect()->route('questions.index', $exam_id)->with('success', 'QUESTION DELETE SUCCESSFULLY...');
    }
}
