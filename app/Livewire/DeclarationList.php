<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_declarations;
use Illuminate\Support\Str;

class DeclarationList extends Component
{
    public $theme_id;
    public $confirmingDelete = false;
    public $declarationToDelete = null;
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
        $declaration = model_declarations::find($id);
        $this->declarationToDelete = $declaration;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.declaration')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.declaration')), 'name' => $declaration->title]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if ($this->declarationToDelete) {
            $declaration = $this->declarationToDelete;
            $this->declarationToDelete->delete();
            $this->declarations = model_declarations::all();
            $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.declaration')]);
            $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.declaration')), 'name' => $declaration->title]);
            $this->confirmingDelete = false;
        }
    }

    public function render()
    {
        $declarations = model_declarations::where('theme_id', $this->theme_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('livewire.declaration-list', [
            'declarations' => $declarations
        ]);
    }
}
