<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_levels;
use Illuminate\Support\Str;

class LevelList extends Component
{
    public $levels;
    public $confirmingDelete = false;
    public $levelToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;
    public function mount()
    {
        $this->levels = model_levels::all();
    }

    public function confirmDelete($id)
    {
        $level = model_levels::find($id);
        $this->levelToDelete = $level;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.level')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.level')), 'name' => $level->name]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if ($this->levelToDelete) {
            $level = $this->levelToDelete;
            $this->levelToDelete->delete();
            $this->levels = model_levels::all();
            $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.level')]);
            $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.level')), 'name' => $level->name]);
            $this->confirmingDelete = false;
        }
    }

    public function render()
    {
        return view('livewire.level-list');
    }
}
