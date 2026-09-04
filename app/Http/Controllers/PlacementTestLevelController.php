<?php

namespace App\Http\Controllers;

use App\Models\PlacementTestLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlacementTestLevelController extends Controller
{
    /**
     * Display the fixed CEFR placement-test levels and their current settings.
     */
    public function index(): View
    {
        $levels = PlacementTestLevel::query()
            ->withCount([
                'questions as active_questions_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->withSum([
                'questions as active_questions_points_sum' => fn ($query) => $query->where('is_active', true),
            ], 'points')
            ->orderBy('sequence')
            ->get();

        return view('admin.placement-test.levels.index', compact('levels'));
    }

    /**
     * Show the configurable settings for one fixed CEFR level.
     */
    public function edit(PlacementTestLevel $placementTestLevel): View
    {
        return view('admin.placement-test.levels.edit', compact('placementTestLevel'));
    }

    /**
     * Update settings that affect future placement-test attempts.
     */
    public function update(Request $request, PlacementTestLevel $placementTestLevel): RedirectResponse
    {
        $settings = $this->validatedSettings($request, $placementTestLevel);

        $placementTestLevel->update($settings);

        return redirect()
            ->route('placement_test_levels_list')
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => $placementTestLevel->code]))
            ->with('modalSuccessContent', "{$placementTestLevel->code} seviye ayarları güncellendi.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedSettings(Request $request, PlacementTestLevel $placementTestLevel): array
    {
        $settings = [
            'is_active' => $request->boolean('is_active'),
        ];

        if ($placementTestLevel->code === 'C2') {
            return [
                ...$settings,
                'has_exam' => false,
                'question_count' => 0,
                'pass_percentage' => null,
            ];
        }

        $validated = $request->validate([
            'pass_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (is_numeric($value) && (int) $value % 5 !== 0) {
                        $fail('Geçme yüzdesi 5’in katı olmalıdır.');
                    }
                },
            ],
        ], [
            'pass_percentage.required' => 'Geçme yüzdesi zorunludur.',
            'pass_percentage.integer' => 'Geçme yüzdesi tam sayı olmalıdır.',
            'pass_percentage.min' => 'Geçme yüzdesi 0’dan küçük olamaz.',
            'pass_percentage.max' => 'Geçme yüzdesi 100’den büyük olamaz.',
        ]);

        return [
            ...$settings,
            'has_exam' => true,
            'question_count' => $placementTestLevel->question_count,
            'pass_percentage' => $validated['pass_percentage'],
        ];
    }
}
