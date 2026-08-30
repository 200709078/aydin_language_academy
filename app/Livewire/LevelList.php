<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_levels;
use Illuminate\Support\Str;

class LevelList extends Component
{
    public $confirmingDelete = false;
    public $levelToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;
    public function confirmDelete($id)
    {
        $level = model_levels::withCount('themes')->find($id);

        if ($level === null || $level->themes_count > 0) {
            return;
        }

        $this->levelToDelete = $level;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.level')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.level')), 'name' => $level->name]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if (! $this->levelToDelete) {
            return;
        }

        $level = model_levels::find($this->levelToDelete->id);

        if ($level === null || $level->themes()->exists()) {
            $this->confirmingDelete = false;

            return;
        }

        $level->delete();
        $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.level')]);
        $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.level')), 'name' => $level->name]);
        $this->confirmingDelete = false;
    }

    public function render()
    {
        return view('livewire.level-list', [
            'levels' => model_levels::withCount('themes')->orderBy('updated_at', 'desc')->get(),
        ]);
    }
}
