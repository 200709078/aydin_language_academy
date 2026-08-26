<?php

namespace App\Console\Commands;

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
        $baseUrl = 'https://www.learnenglishwithala.com';
        $sitemap = Sitemap::create();

        $staticUrls = [
            ['/', 1.0],
            ['/basarilarimiz', 0.9],
            ['/kampanyalarimiz', 0.9],
            ['/yorumlar', 0.7],
            ['/kurslarimiz/okul-oncesi', 0.8],
            ['/kurslarimiz/ilkokul', 0.8],
            ['/kurslarimiz/ortaokul', 0.8],
            ['/kurslarimiz/lise', 0.8],
            ['/kurslarimiz/genel-ingilizce', 0.8],
            ['/kurslarimiz/ielts', 0.8],
            ['/kurslarimiz/yks-dil', 0.8],
            ['/kurslarimiz/yds-yokdil', 0.8],
            ['/kurslarimiz/toefl', 0.8],
            ['/kurslarimiz/pte-academic', 0.8],
            ['/kurslarimiz/test-of-english', 0.8],
            ['/kurslarimiz/sat', 0.8],
            ['/kurslarimiz/konusma-kulupleri', 0.8],
            ['/subelerimiz/ortaca', 0.8],
            ['/subelerimiz/dalaman', 0.8],
            ['/subelerimiz/koycegiz', 0.8],
            ['/seviye-tespit-sinavi', 0.8],
            ['/iletisim', 0.7],
        ];

        foreach ($staticUrls as [$path, $priority]) {
            $sitemap->add(Url::create($baseUrl . $path)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority($priority));
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Generated sitemap...');
    }
}
