<?php

namespace App\Console\Commands;

use App\Models\model_courses;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sitemap Generator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');
        $sitemap = Sitemap::create();

        $sitemap->add(Url::create('https://www.learnenglishwithala.com/')
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(1.0));

        $sitemap->add(Url::create('https://www.learnenglishwithala.com/about')
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9));

        $sitemap->add(Url::create('https://www.learnenglishwithala.com/contact')
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        foreach (model_levels::all() as $levels) {
            foreach (model_sub_levels::all() as $sub_levels) {
                $sitemap->add(Url::create('https://www.learnenglishwithala.com/' . $levels->slug . '/' . $sub_levels->slug)
                    ->setLastModificationDate($levels->updated_at)
                    ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        }

        model_themes::all()->each(function (model_themes $theme) use ($sitemap) {
            $sitemap->add(Url::create('https://www.learnenglishwithala.com/tab1/' . $theme->id)
                ->setLastModificationDate($theme->updated_at)
                ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6));
            $sitemap->add(Url::create('https://www.learnenglishwithala.com/tab2/' . $theme->id)
                ->setLastModificationDate($theme->updated_at)
                ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.5));
        });

        model_courses::all()->each(function (model_courses $course) use ($sitemap) {
            $sitemap->add(Url::create('https://www.learnenglishwithala.com/course/' . $course->id)
                ->setLastModificationDate($course->updated_at)
                ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.4));
        });

/*         model_courses::all()->each(function (model_courses $course) use ($sitemap) {
            $sitemap->add(Url::create(route('course_detail', $course->id))
                ->setLastModificationDate($course->updated_at)
                ->setChangeFrequency(url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.4));
        }); */

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Generated sitemap...');
    }
}
