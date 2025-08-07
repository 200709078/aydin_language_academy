<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_sub_levels;
use Illuminate\Support\Str;

class SubLevelList extends Component
{
    public $sublevels;
    public $confirmingDelete = false;
    public $sublevelToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;
    public function mount()
    {
        $this->sublevels = model_sub_levels::all();
    }

    public function confirmDelete($id)
    {
        $sublevel = model_sub_levels::find($id);
        $this->sublevelToDelete = $sublevel;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.sublevel')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.sublevel')), 'name' => $sublevel->name]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        $sublevel = $this->sublevelToDelete;
        $this->sublevelToDelete->delete();
        $this->sublevels = model_sub_levels::all();
        $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.sublevel')]);
        $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.sublevel')), 'name' => $sublevel->name]);
        $this->confirmingDelete = false;
    }

    public function render()
    {
        return view('livewire.sub-level-list');
    }
}
