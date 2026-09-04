<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignPageSetting;
use App\Models\MediaAsset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LogicException;

class CampaignLegacyDataSeeder extends Seeder
{
    private const HERO_SOURCE = 'frontend/images/campaigns/campaign-1.png';

    private const HERO_TARGET = 'campaigns/media-assets/import/campaign-page-hero.png';

    /**
     * Copy the current static Campaigns page content into the new source of truth.
     *
     * The seeder deliberately does not overwrite records that an administrator has
     * already created or edited, and it is not registered in DatabaseSeeder.
     */
    public function run(): void
    {
        $heroMediaAsset = $this->importHeroMediaAsset();

        DB::transaction(function () use ($heroMediaAsset): void {
            $campaignPageSetting = CampaignPageSetting::query()->firstOrCreate(
                ['id' => 1],
                [
                    'title_tr' => 'YABANCI DİL EĞİTİMİNDE AVANTAJLI FIRSATLAR',
                    'title_en' => 'Advantageous Opportunities in Foreign Language Education',
                    'description_tr' => 'Aydın Dil Akademisi’nde öğrencilerimizin başarısını destekleyen burs olanakları, ailelere özel avantajlar ve dönemsel kayıt fırsatları sunuyoruz.',
                    'description_en' => 'At Aydın Dil Akademisi, we offer scholarship opportunities that support our students’ success, special advantages for families, and seasonal enrollment opportunities.',
                    'hero_media_asset_id' => $heroMediaAsset->id,
                ],
            );

            $descriptionUpdates = [];

            if ($campaignPageSetting->description_tr === null) {
                $descriptionUpdates['description_tr'] = 'Aydın Dil Akademisi’nde öğrencilerimizin başarısını destekleyen burs olanakları, ailelere özel avantajlar ve dönemsel kayıt fırsatları sunuyoruz.';
            }

            if ($campaignPageSetting->description_en === null) {
                $descriptionUpdates['description_en'] = 'At Aydın Dil Akademisi, we offer scholarship opportunities that support our students’ success, special advantages for families, and seasonal enrollment opportunities.';
            }

            if ($descriptionUpdates !== []) {
                $campaignPageSetting->update($descriptionUpdates);
            }

            if (Campaign::query()->exists()) {
                foreach ($this->supplementalCampaigns() as $campaign) {
                    Campaign::query()->firstOrCreate(
                        ['title_tr' => $campaign['title_tr']],
                        $campaign,
                    );
                }

                return;
            }

            foreach (array_merge($this->legacyCampaigns(), $this->supplementalCampaigns()) as $campaign) {
                Campaign::query()->create($campaign);
            }
        });
    }

    private function importHeroMediaAsset(): MediaAsset
    {
        $sourcePath = public_path(self::HERO_SOURCE);

        if (! is_file($sourcePath)) {
            throw new LogicException('Mevcut kampanya sayfası hero görseli bulunamadı.');
        }

        $contents = file_get_contents($sourcePath);
        $dimensions = @getimagesize($sourcePath);

        if ($contents === false || $dimensions === false || ($dimensions['mime'] ?? null) !== 'image/png') {
            throw new LogicException('Mevcut kampanya sayfası hero görseli geçerli bir PNG dosyası değil.');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists(self::HERO_TARGET) && ! $disk->put(self::HERO_TARGET, $contents)) {
            throw new LogicException('Kampanya sayfası hero görseli public depoya kopyalanamadı.');
        }

        return MediaAsset::query()->firstOrCreate(
            [
                'disk' => 'public',
                'path_hash' => hash('sha256', "public\0".self::HERO_TARGET),
            ],
            [
                'path' => self::HERO_TARGET,
                'kind' => MediaAsset::KIND_IMAGE,
                'visibility' => MediaAsset::VISIBILITY_PUBLIC,
                'original_filename' => basename(self::HERO_SOURCE),
                'mime_type' => $dimensions['mime'],
                'size_bytes' => strlen($contents),
                'width' => $dimensions[0],
                'height' => $dimensions[1],
                'checksum' => hash('sha256', $contents),
                'uploaded_by' => null,
            ],
        );
    }

    /**
     * @return list<array<string, int|string|null>>
     */
    private function legacyCampaigns(): array
    {
        return [
            [
                'title_tr' => 'Yaz + Kış Kampanyası',
                'title_en' => 'Summer + Winter Campaign',
                'description_tr' => 'Kış sezonu kayıtlarında yaz kurslarımız ücretsizdir.',
                'description_en' => 'Our summer courses are free with winter season registrations.',
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 1,
            ],
            [
                'title_tr' => 'Ücretsiz Seviye Tespit Sınavına %15 İndirim',
                'title_en' => '15% Discount with Free Placement Test',
                'description_tr' => 'Ücretsiz online seviye tespit sınavına katılarak hem ingilize seviyenizi ölçün hem de size tanımlanan %15 indirimden yararlanın.',
                'description_en' => 'Join our free online placement test to assess your English level and benefit from your exclusive 15% discount.',
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 2,
            ],
            [
                'title_tr' => '%100\'e varan burs avantajları',
                'title_en' => 'Scholarship Advantages Up to 100%',
                'description_tr' => 'Her yıl şubat mart aylarında düzenlediğimiz bursluluk sınavıyla %100’e varan kurs olanaklarından yararlanabilirsiniz.',
                'description_en' => 'With the scholarship exam we organize every year in February-March, you can benefit from course opportunities up to 100%.',
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 3,
            ],
            [
                'title_tr' => 'Instagram Çekilişlerimize Katılın',
                'title_en' => 'Join Our Instagram Giveaways',
                'description_tr' => 'Periyodik olarak instagram takipçilerimiz arasından yapmış olduğumuz canlı çekilişlerle ücretsiz ve indirimli kurs fırsatlarından yararlanın.',
                'description_en' => 'Benefit from free and discounted course opportunities with live giveaways we periodically hold among our Instagram followers.',
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 4,
            ],
        ];
    }

    /**
     * Additional published campaigns supplied after the original static page
     * was imported. These are created when absent without overwriting admin edits.
     *
     * @return list<array<string, int|string|null>>
     */
    private function supplementalCampaigns(): array
    {
        return [
            [
                'title_tr' => 'Bursluluk Sınavı',
                'title_en' => 'Scholarship Examination',
                'description_tr' => "Belirli dönemlerde düzenlediğimiz bursluluk sınavlarımız ile öğrencilerimize başarı durumlarına göre %100’e varan eğitim bursu kazanma fırsatı sunuyoruz.\n\nBurs oranları öğrencinin sınav performansına göre belirlenir.\n\n✓ %100’e Varan Burs Fırsatı\n✓ Başarıya Dayalı Değerlendirme\n✓ Sınırlı Kontenjan",
                'description_en' => "Through scholarship examinations held during selected periods, we offer our students the opportunity to earn education scholarships of up to 100%, based on their achievement level.\n\nScholarship rates are determined according to the student’s exam performance.\n\n✓ Scholarship Opportunity up to 100%\n✓ Performance-Based Evaluation\n✓ Limited Capacity",
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 5,
            ],
            [
                'title_tr' => 'Kabul Sınavı & Başarı Bursu',
                'title_en' => 'Admission Exam & Achievement Scholarship',
                'description_tr' => "Bursluluk sınavı dönemleri dışında da öğrencilerimize burs fırsatı sunmaya devam ediyoruz.\n\nKabul sınavımıza katılan öğrenciler, sınav performanslarına göre değerlendirilerek Başarı Bursu kazanabilir.\n\nÖğrencinin dil seviyesi ve sınav başarısı doğrultusunda uygun eğitim programı ve burs avantajı belirlenir.\n\n✓ Yıl Boyunca Başvuru İmkânı\n✓ Başarıya Göre Burs Avantajı\n✓ Seviyeye Uygun Programlama",
                'description_en' => "We continue to offer scholarship opportunities outside scholarship examination periods.\n\nStudents who take our admission exam may earn an Achievement Scholarship based on their exam performance.\n\nAn appropriate education program and scholarship advantage are determined according to the student’s language level and exam success.\n\n✓ Apply Throughout the Year\n✓ Achievement-Based Scholarship Advantage\n✓ Program Matched to the Student’s Level",
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 6,
            ],
            [
                'title_tr' => 'Kış Dönemi Tam Kayıtta Yaz Kursu %100 Ücretsiz',
                'title_en' => 'Free Summer Course with Full Winter-Term Enrollment',
                'description_tr' => "Kış dönemi programlarımıza tam kayıt yaptıran öğrencilerimize yaz kursu tamamen ücretsiz!\n\nÜstelik yaz kursu kapsamında kullanılacak kaynak ve eğitim materyalleri de ücretsiz olarak sunulmaktadır.\n\nÖğrencilerimiz yaz döneminde İngilizceden uzak kalmadan gelişimlerini sürdürürken ailelerimiz ek kurs ve materyal ücreti ödemez.\n\n✓ Yaz Kursu %100 Ücretsiz\n✓ Kaynak & Materyaller Dahil\n✓ Kış Dönemi Tam Kayıtlarına Özel",
                'description_en' => "Students who complete full enrollment in our winter-term programs receive the summer course completely free of charge.\n\nThe resources and course materials used as part of the summer course are also provided free of charge.\n\nWhile students continue improving their English during the summer, families pay no additional course or material fees.\n\n✓ Summer Course 100% Free\n✓ Resources & Materials Included\n✓ Exclusive to Full Winter-Term Enrollments",
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 7,
            ],
            [
                'title_tr' => 'Kardeş İndirimi',
                'title_en' => 'Sibling Discount',
                'description_tr' => "Aynı aileden birden fazla öğrencinin Aydın Dil Akademisi’nde eğitim alması durumunda ailelerimize özel kardeş indirimi sunuyoruz.\n\nKardeşlerin birlikte eğitim almasını destekliyor, aile bütçesine katkı sağlayan özel kayıt avantajları sunuyoruz.\n\n✓ Kardeşlere Özel İndirim\n✓ Ailelere Özel Kayıt Avantajı",
                'description_en' => "When more than one student from the same family studies at Aydın Dil Akademisi, we offer a special sibling discount for families.\n\nWe support siblings learning together and provide special enrollment advantages that help the family budget.\n\n✓ Special Discount for Siblings\n✓ Family-Specific Enrollment Advantage",
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 8,
            ],
            [
                'title_tr' => 'Arkadaşını Getir Kampanyası',
                'title_en' => 'Refer-a-Friend Campaign',
                'description_tr' => "Aydın Dil Akademisi deneyimini arkadaşınla paylaş, birlikte öğrenmenin avantajını yaşa.\n\nMevcut öğrencilerimizin yeni bir arkadaşını kurumumuza yönlendirmesi ve arkadaşının kayıt olması durumunda dönemsel olarak özel kayıt avantajları sunuyoruz.\n\n✓ Mevcut Öğrencilere Özel\n✓ Yeni Kayıtlarda Ek Avantaj\n✓ Dönemsel Kampanya Fırsatları",
                'description_en' => "Share the Aydın Dil Akademisi experience with a friend and enjoy the advantages of learning together.\n\nWhen a current student refers a new friend to our institution and that friend enrolls, we periodically offer special enrollment advantages.\n\n✓ Exclusive to Current Students\n✓ Additional Advantage for New Enrollments\n✓ Seasonal Campaign Opportunities",
                'link_type' => Campaign::LINK_TYPE_NONE,
                'internal_destination' => null,
                'external_url' => null,
                'status' => Campaign::STATUS_PUBLISHED,
                'sort_order' => 9,
            ],
        ];
    }
}
