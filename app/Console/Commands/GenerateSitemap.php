<?php

namespace App\Console\Commands;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate
        {--url= : Crawl edilecek başlangıç URLsi}
        {--path= : Sitemap çıktı dosyası}';

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
        $baseUrl = rtrim((string) ($this->option('url') ?: 'https://www.learnenglishwithala.com'), '/');
        $outputPath = (string) ($this->option('path') ?: public_path('sitemap.xml'));

        if (filter_var($baseUrl, FILTER_VALIDATE_URL) === false) {
            $this->error('A valid sitemap crawl URL is required.');

            return self::FAILURE;
        }

        $this->info("Generating sitemap from {$baseUrl}...");
        $crawledUrls = [];

        $sitemap = SitemapGenerator::create($baseUrl)
            ->shouldCrawl(fn (UriInterface $url): bool => $this->shouldCrawl($url))
            ->hasCrawled(function (Url $url, ?ResponseInterface $response) use (&$crawledUrls, $baseUrl): ?Url {
                if ($response?->getStatusCode() !== 200 || ! str_contains(strtolower($response->getHeaderLine('Content-Type')), 'text/html')) {
                    return null;
                }

                $canonicalUrl = $this->canonicalizeUrl($url->url, $baseUrl);

                if (isset($crawledUrls[$canonicalUrl])) {
                    return null;
                }

                $crawledUrls[$canonicalUrl] = true;

                return Url::create($canonicalUrl)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY);
            })
            ->setMaximumCrawlCount(500)
            ->setConcurrency(1)
            ->getSitemap();

        $this->addPublicStaticRoutes($sitemap, $baseUrl);
        $sitemap->writeToFile($outputPath);

        $this->info("Generated sitemap at {$outputPath}.");

        return self::SUCCESS;
    }

    private function shouldCrawl(UriInterface $url): bool
    {
        if ($url->getQuery() !== '') {
            return false;
        }

        $path = '/' . trim($url->getPath(), '/');

        foreach ([
            '/admin',
            '/giris',
            '/login',
            '/logout',
            '/register',
            '/uye-ol',
            '/user',
            '/profile',
            '/dokumanlar',
            '/temalar',
            '/tema',
            '/alistirmalar',
            '/yorumlar',
            '/yorumlarim',
            '/iletisim',
            '/seviye-tespit-sinavi/sinav',
            '/seviye-tespit-sinavi/sinavlarim',
            '/changeLanguage',
            '/contact',
            '/api',
            '/sanctum',
            '/up',
            '/forgot-password',
            '/reset-password',
            '/confirm-password',
            '/email',
            '/two-factor-challenge',
        ] as $excludedPath) {
            if ($path === $excludedPath || str_starts_with($path, $excludedPath . '/')) {
                return false;
            }
        }

        return true;
    }

    private function addPublicStaticRoutes(Sitemap $sitemap, string $baseUrl): void
    {
        foreach (app('router')->getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)
                || ! $route->getName()
                || $this->requiresAuthentication($route)
                || preg_match('/\{[^?}]+\}/', $route->uri())) {
                continue;
            }

            $path = parse_url(route($route->getName()), PHP_URL_PATH);

            if (! is_string($path)) {
                continue;
            }

            $url = new Uri($baseUrl . ($path === '/' ? '/' : '/' . ltrim($path, '/')));

            if (! $this->shouldCrawl($url)) {
                continue;
            }

            $sitemap->add(
                Url::create($this->canonicalizeUrl((string) $url, $baseUrl))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        }
    }

    private function canonicalizeUrl(string $url, string $baseUrl): string
    {
        return rtrim($url, '/') === $baseUrl ? $baseUrl . '/' : $url;
    }

    private function requiresAuthentication(Route $route): bool
    {
        foreach ($route->gatherMiddleware() as $middleware) {
            if ($middleware === 'auth'
                || str_starts_with($middleware, 'auth:')
                || $middleware === 'admin'
                || str_starts_with($middleware, 'admin:')
                || str_contains($middleware, 'Authenticate')) {
                return true;
            }
        }

        return false;
    }
}
