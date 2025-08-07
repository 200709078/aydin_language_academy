<?php

namespace App\Livewire;

use App\Models\model_questions;
use Livewire\Component;
use Illuminate\Support\Str;

class QuestionList extends Component
{
    public $exercise_id;
    public $theme_id;
    public $confirmingDelete = false;
    public $questionToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;

    public function mount($exercise_id, $theme_id)
    {
        $this->exercise_id = $exercise_id;
        $this->theme_id = $theme_id;
    }
    public function confirmDelete($id)
    {
        $question = model_questions::find($id);
        $this->questionToDelete = $question;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.question')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.question')), 'name' => $question->question]);
        $this->confirmingDelete = true;
    
    }

    public function deleteItem()
    {
        if ($this->questionToDelete) {
            $question= $this->questionToDelete;
            $this->questionToDelete->delete();
            $this->questions = model_questions::all();
            $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.question')]);
            $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.question')), 'name' => $question->question]);
            $this->confirmingDelete = false;
        }
    }

    public function render()
    {
        $questions = model_questions::where('exercise_id', $this->exercise_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('livewire.question-list', [
            'questions' => $questions
        ]);
    }
}
