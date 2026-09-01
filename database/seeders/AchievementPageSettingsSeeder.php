<?php

namespace Database\Seeders;

use App\Models\AchievementPageSetting;
use App\Models\CampaignPageSetting;
use Illuminate\Database\Seeder;

class AchievementPageSettingsSeeder extends Seeder
{
    /**
     * Preserve the existing public heading and introduction while making the
     * page settings database-managed. The current campaign hero is deliberately
     * shared, not copied, until a separate achievement image is supplied.
     */
    public function run(): void
    {
        $campaignHeroMediaAssetId = CampaignPageSetting::query()
            ->orderBy('id')
            ->value('hero_media_asset_id');

        $achievementPageSetting = AchievementPageSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'title_tr' => 'Başarılarımız',
                'title_en' => 'Achievements',
                'description_tr' => 'Yerleştirme ve başarı kayıtlarımızı yıllara göre inceleyebilirsiniz.',
                'description_en' => 'Browse our placement and achievement records by year.',
                'hero_media_asset_id' => $campaignHeroMediaAssetId,
            ],
        );

        $updates = [];

        foreach ([
            'title_tr' => 'Başarılarımız',
            'title_en' => 'Achievements',
            'description_tr' => 'Yerleştirme ve başarı kayıtlarımızı yıllara göre inceleyebilirsiniz.',
            'description_en' => 'Browse our placement and achievement records by year.',
        ] as $attribute => $value) {
            if ($achievementPageSetting->{$attribute} === null) {
                $updates[$attribute] = $value;
            }
        }

        if ($achievementPageSetting->hero_media_asset_id === null && $campaignHeroMediaAssetId !== null) {
            $updates['hero_media_asset_id'] = $campaignHeroMediaAssetId;
        }

        if ($updates !== []) {
            $achievementPageSetting->update($updates);
        }
    }
}
