<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Http\Requests\QuestionCreateRequest;
use App\Http\Requests\QuestionUpdateRequest;
use Illuminate\Support\Str;
class cont_questions extends Controller
{
    public function index($id)
    {
        $exercises = model_exercises::whereId($id)->with('questions')->first() ?? abort(404, 'QUIZ NOT FOUND');
        return view('admin.questions.list', compact('exercises'));
    }
    public function create($id)
    {
        $exercises = model_exercises::find($id);
        return view('admin.questions.create', compact('exercises'));
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
        model_exercises::find($id)->questions()->create($request->post());
        return redirect()->route('questions.index', $id)->with('success', 'QUESTION ADD SUCCESSFULLY');
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $exercises_id, string $question_id)
    {
        $question = model_exercises::find($exercises_id)->questions()->whereId($question_id)->first() ?? abort(404, 'EXERCISES OR QUESTION NOT FOUND.');
        return view('admin.questions.edit', compact('question'));
    }
    public function update(QuestionUpdateRequest $request, string $exercises_id, string $question_id)
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

        model_exercises::find($exercises_id) ?? abort(404, 'EXERCISES NOT FOUND');
        model_questions::find($question_id) ?? abort(404, 'QUESTION NOT FOUND');
        model_questions::where('id', $question_id)->update($request->except(['_method', '_token']));
        return redirect()->route('questions.index', $exercises_id)->with('success', 'QUESTION UPDATE SUCCESSFULLY...');
    }
    public function destroy(string $exercise_id, string $question_id)
    {
        model_exercises::find($exercise_id)->questions()->whereId($question_id)->delete();
        return redirect()->route('questions_list', $exercise_id)->with('success', 'QUESTION DELETE SUCCESSFULLY...');
    }
}
