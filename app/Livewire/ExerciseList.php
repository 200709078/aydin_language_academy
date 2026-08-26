<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_exercises;
use Illuminate\Support\Str;

class ExerciseList extends Component
{
    public $theme_id;
    public $confirmingDelete = false;
    public $exerciseToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;

    public function mount($theme_id)
    {
        $this->theme_id = $theme_id;
    }

    public function confirmDelete($id)
    {
        $exercise = model_exercises::withCount('questions')->find($id);

        if ($exercise === null || $exercise->questions_count > 0) {
            return;
        }

        $this->exerciseToDelete = $exercise;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.exercise')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.exercise')), 'name' => $exercise->title]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if (! $this->exerciseToDelete) {
            return;
        }

        $exercise = model_exercises::find($this->exerciseToDelete->id);

        if ($exercise === null || $exercise->questions()->exists()) {
            $this->confirmingDelete = false;

            return;
        }

        $exercise->delete();
        $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.exercise')]);
        $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.exercise')), 'name' => $exercise->title]);
        $this->confirmingDelete = false;
    }

    public function render()
    {
        $exercises = model_exercises::where('theme_id', $this->theme_id)
            ->withCount('questions')
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('livewire.exercise-list', [
            'exercises' => $exercises
        ]);
    }
}
