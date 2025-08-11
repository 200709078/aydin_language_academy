<?php

namespace App\Livewire;

use App\Models\model_courses;
use Livewire\Component;
use Illuminate\Support\Str;

class CourseList extends Component
{
    public $courses;
    public $confirmingDelete = false;
    public $courseToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;
    public function mount()
    {
        $this->courses = model_courses::orderBy('updated_at', 'desc')->get();
    }

    public function confirmDelete($id)
    {
        $course = model_courses::find($id);
        $this->courseToDelete = $course;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.course')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.course')), 'name' => $course->title_tr]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if ($this->courseToDelete) {
            $course = $this->courseToDelete;
            $this->courseToDelete->delete();
            $this->courses = model_courses::orderBy('updated_at', 'desc')->get();
            $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.course')]);
            $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.course')), 'name' => $course->title_tr]);
            $this->confirmingDelete = false;
        }
    }

    public function render()
    {
        return view('livewire.course-list');
    }
}
