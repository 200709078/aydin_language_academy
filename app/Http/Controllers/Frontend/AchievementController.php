<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\AchievementPageSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementController extends Controller
{
    /**
     * Show only currently public annual achievement records.
     */
    public function index(): View
    {
        $pageSettings = AchievementPageSetting::query()
            ->with('heroMediaAsset')
            ->orderBy('id')
            ->first();

        $achievementYears = Achievement::query()
            ->publiclyAvailable()
            ->whereHas('publicEntries')
            ->with([
                'publicEntries' => static function (HasMany $query): void {
                    $query->select([
                        'id',
                        'achievements_id',
                        'full_name',
                        'name_permission_status',
                        'university_name',
                        'department_name',
                        'description',
                        'branch',
                        'card_sub_title',
                        'sort_order',
                    ]);
                },
            ])
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        $initialOpenYearId = $achievementYears->first()?->getKey();

        return view('frontend.achievements', compact(
            'achievementYears',
            'initialOpenYearId',
            'pageSettings',
        ));
    }

}
