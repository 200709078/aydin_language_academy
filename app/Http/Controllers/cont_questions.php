<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\model_exercises;
use App\Models\model_questions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
    public function store(Request $request, $exercise_id)
    {
        $request->validate([
            'question' => 'required|min:3|',
            'answer1' => 'required|min:3|',
            'answer2' => 'required|min:3|',
            'answer3' => 'required|min:3|',
            'answer4' => 'required|min:3|',
            'answer5' => 'required|min:3|'
        ], [
            'question.required' => Lang::get('dictt.required_question'),
            'question.min' => Lang::get('dictt.mincharacter_question'),
            'answer1.required' => Lang::get('dictt.required_answer1'),
            'answer1.min' => Lang::get('dictt.mincharacter_answer1'),
            'answer2.required' => Lang::get('dictt.required_answer2'),
            'answer2.min' => Lang::get('dictt.mincharacter_answer2'),
            'answer3.required' => Lang::get('dictt.required_answer3'),
            'answer3.min' => Lang::get('dictt.mincharacter_answer3'),
            'answer4.required' => Lang::get('dictt.required_answer4'),
            'answer4.min' => Lang::get('dictt.mincharacter_answer4'),
            'answer5.required' => Lang::get('dictt.required_answer5'),
            'answer5.min' => Lang::get('dictt.mincharacter_answer5'),

        ]);
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }

        $question = model_questions::create([
            'exercise_id' => $exercise_id,
            'question' => $request->question,
            'image' => $imageFileName,
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,
            'answer4' => $request->answer4,
            'answer5' => $request->answer5,
            'correct_answer' => $request->correct_answer
        ]);
        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.question')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => Str::lower(__('dictt.question')), 'name' => $question->question]);

        return redirect()->route('questions_list', ['exercise_id' => $exercise_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
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
        $request->validate([
            'question' => 'required|min:3|',
            'answer1' => 'required|min:3|',
            'answer2' => 'required|min:3|',
            'answer3' => 'required|min:3|',
            'answer4' => 'required|min:3|',
            'answer5' => 'required|min:3|'
        ], [
            'question.required' => Lang::get('dictt.required_question'),
            'question.min' => Lang::get('dictt.mincharacter_question'),
            'answer1.required' => Lang::get('dictt.required_answer1'),
            'answer1.min' => Lang::get('dictt.mincharacter_answer1'),
            'answer2.required' => Lang::get('dictt.required_answer2'),
            'answer2.min' => Lang::get('dictt.mincharacter_answer2'),
            'answer3.required' => Lang::get('dictt.required_answer3'),
            'answer3.min' => Lang::get('dictt.mincharacter_answer3'),
            'answer4.required' => Lang::get('dictt.required_answer4'),
            'answer4.min' => Lang::get('dictt.mincharacter_answer4'),
            'answer5.required' => Lang::get('dictt.required_answer5'),
            'answer5.min' => Lang::get('dictt.mincharacter_answer5'),

        ]);
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $imageFileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('photos'), $imageFileName);
        }
        $question = model_questions::find($question_id);
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
        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.question')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => Str::lower(__('dictt.question')), 'name' => $question->question]);

        return redirect()->route('questions_list', ['exercise_id' => $question->exercise_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }
    public function destroy(string $exercise_id, string $question_id)
    {
        model_questions::findOrFail($question_id)->delete();
        return redirect()->route('questions_list')
            ->with('success', Lang::get('dictt.declarationdeletesuccess'));
    }
}
