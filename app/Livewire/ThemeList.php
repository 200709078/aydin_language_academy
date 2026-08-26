<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\model_themes;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class ThemeList extends Component
{
    use WithPagination;
    public $confirmingDelete = false;
    public $themeToDelete = null;
    public $modalConfirmTitle;
    public $modalConfirmContent;
    public $modalSuccessTitle;
    public $modalSuccessContent;
    public function confirmDelete($id)
    {
        $theme = model_themes::withCount(['declarations', 'exercises'])->find($id);

        if (
            $theme === null
            || $theme->declarations_count > 0
            || $theme->exercises_count > 0
        ) {
            return;
        }

        $this->themeToDelete = $theme;
        $this->modalConfirmTitle = __('dictt.deleteconfirmtitle', ['type' => __('dictt.theme')]);
        $this->modalConfirmContent = __('dictt.deleteconfirmcontent', ['type' => Str::lower(__('dictt.theme')), 'name' => $theme->name]);
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        if (! $this->themeToDelete) {
            return;
        }

        $theme = model_themes::find($this->themeToDelete->id);

        if (
            $theme === null
            || $theme->declarations()->exists()
            || $theme->exercises()->exists()
        ) {
            $this->confirmingDelete = false;

            return;
        }

        $theme->delete();
        $this->modalSuccessTitle = __('dictt.deletesuccesstitle', ['type' => __('dictt.theme')]);
        $this->modalSuccessContent = __('dictt.deletesuccesscontent', ['type' => Str::lower(__('dictt.theme')), 'name' => $theme->name]);
        $this->confirmingDelete = false;
    }

    public function render()
    {
        $themes = model_themes::with(['levels', 'sub_levels'])
            ->withCount(['declarations', 'exercises'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.theme-list', [
            'themes' => $themes
        ]);
    }
}
