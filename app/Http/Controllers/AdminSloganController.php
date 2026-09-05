<?php

namespace App\Http\Controllers;

use App\Models\Slogan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminSloganController extends Controller
{
    /**
     * List every slogan that can be selected randomly for the homepage.
     */
    public function index(): View
    {
        $slogans = Slogan::query()
            ->latest('id')
            ->paginate(20);

        return view('admin.slogans.index', compact('slogans'));
    }

    /**
     * Show the form for a new slogan.
     */
    public function create(): View
    {
        return view('admin.slogans.create');
    }

    /**
     * Store a new homepage slogan.
     */
    public function store(Request $request): RedirectResponse
    {
        Slogan::create($this->validatedSlogan($request));

        return redirect()
            ->route('admin.slogans.index')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => __('dictt.slogan')]))
            ->with('modalSuccessContent', __('dictt.slogan_created'));
    }

    /**
     * Show the form for an existing slogan.
     */
    public function edit(Slogan $slogan): View
    {
        return view('admin.slogans.edit', compact('slogan'));
    }

    /**
     * Update one homepage slogan.
     */
    public function update(Request $request, Slogan $slogan): RedirectResponse
    {
        $slogan->update($this->validatedSlogan($request));

        return redirect()
            ->route('admin.slogans.index')
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => __('dictt.slogan')]))
            ->with('modalSuccessContent', __('dictt.slogan_updated'));
    }

    /**
     * Permanently remove a slogan from the homepage pool.
     */
    public function destroy(Slogan $slogan): RedirectResponse
    {
        $slogan->delete();

        return redirect()
            ->route('admin.slogans.index')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => __('dictt.slogan')]))
            ->with('modalSuccessContent', __('dictt.slogan_deleted'));
    }

    /**
     * @return array{title_tr: string, title_en: string}
     */
    private function validatedSlogan(Request $request): array
    {
        $validated = $request->validate([
            'title_tr' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
        ]);

        return [
            'title_tr' => $this->requiredTrimmedValue($validated, 'title_tr', 'slogan_title_tr'),
            'title_en' => $this->requiredTrimmedValue($validated, 'title_en', 'slogan_title_en'),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function requiredTrimmedValue(array $validated, string $attribute, string $label): string
    {
        $value = trim((string) ($validated[$attribute] ?? ''));

        if ($value === '') {
            throw ValidationException::withMessages([
                $attribute => __('dictt.required_item', ['name' => __('dictt.'.$label)]),
            ]);
        }

        return $value;
    }
}
