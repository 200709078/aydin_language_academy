<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_sub_levels;
use Illuminate\Support\Str;

class SubLevelList extends Component
{
    public $confirmingDelete = false;
    public $sublevelToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;
    public function confirmDelete($id)
    {
        $sublevel = model_sub_levels::withCount('themes')->find($id);

        if ($sublevel === null || $sublevel->themes_count > 0) {
            return;
        }

        $this->sublevelToDelete = $sublevel;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.sublevel')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.sublevel')), 'name' => $sublevel->name]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if (! $this->sublevelToDelete) {
            return;
        }

        $sublevel = model_sub_levels::find($this->sublevelToDelete->id);

        if ($sublevel === null || $sublevel->themes()->exists()) {
            $this->confirmingDelete = false;

            return;
        }

        $sublevel->delete();
        $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.sublevel')]);
        $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.sublevel')), 'name' => $sublevel->name]);
        $this->confirmingDelete = false;
    }

    public function render()
    {
        return view('livewire.sub-level-list', [
            'sublevels' => model_sub_levels::withCount('themes')->orderBy('updated_at', 'desc')->get(),
        ]);
    }
}
