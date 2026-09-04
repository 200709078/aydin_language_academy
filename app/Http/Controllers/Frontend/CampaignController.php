<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPageSetting;
use Illuminate\Contracts\View\View;

class CampaignController extends Controller
{
    /**
     * Display the campaign page with only published campaign cards.
     */
    public function index(): View
    {
        $pageSettings = CampaignPageSetting::query()
            ->with('heroMediaAsset')
            ->orderBy('id')
            ->first();

        $campaigns = Campaign::query()
            ->publiclyAvailable()
            ->orderByRaw('sort_order IS NULL')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('frontend.campaigns', compact('pageSettings', 'campaigns'));
    }

}
