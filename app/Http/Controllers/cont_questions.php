<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\model_exercises;
use App\Models\model_questions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class cont_questions extends Controller
{
    public function index($id)
    {
        $exercises = model_exercises::whereId($id)->with('questions')->first() ?? abort(404, 'QUIZ NOT FOUND');
        return view('admin.questions.list', compact('exercises'));
    }
    public function create($exercises_id)
    {
        $exercise = model_exercises::find($exercises_id);
        return view('admin.questions.create', compact('exercise'));
    }
    public function store(Request $request, $exercises_id)
    {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }

        model_questions::create([
            'exercises_id' => $exercises_id,
            'question' => $request->question,
            'image' => $imageFileName,
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,
            'answer4' => $request->answer4,
            'answer5' => $request->answer5,
            'correct_answer' => $request->correct_answer
        ]);
        return redirect()->route('questions_list', $exercises_id)->with('success', Lang::get('dictt.questionaddsuccess'));
    }
    public function show(string $id)
    {
        //
    }
    /*     public function edit(string $exercises_id, string $question_id)
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
        } */

    public function edit(string $question_id)
    {
        $question = model_questions::find($question_id);
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, string $question_id)
    {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        model_questions::where('id', $question_id)->update([
            'question' => $request->question,
            'image' => $imageFileName,
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,
            'answer4' => $request->answer4,
            'answer5' => $request->answer5,
            'correct_answer' => $request->correct_answer
        ]);
        $question = model_questions::find($question_id);
        return redirect()->route('questions_list', $question->exercises_id)->with('success', Lang::get('dictt.questionupdatesuccess'));
    }
    public function destroy(string $exercise_id, string $question_id)
    {
        model_exercises::find($exercise_id)->questions()->whereId($question_id)->delete();
        return redirect()->route('questions_list', $exercise_id)->with('success', Lang::get('dictt.questiondeletesuccess'));
    }
}
