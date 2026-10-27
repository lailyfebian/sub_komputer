<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KeywordTemplate;
use App\Models\Region;
use App\Models\PageContent;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate static sitemap files';

    public function handle()
    {
        $pageContents = PageContent::with('keywordTemplate')->cursor();

        $urls = [];

        foreach ($pageContents as $page) {

            if (!$page->keywordTemplate) {
                continue;
            }

            $keywordSlug = $page->keywordTemplate->slug;

           
            // KEYWORD + REGION (berdasarkan region_type)
            $regions = Region::where('type', $page->region_type)->get();

            foreach ($regions as $region) {

                $urls[] = route('sales.keyword.region', [
                    'keywords' => $keywordSlug,
                    'type' => $region->type,
                    'locationSlug' => $region->slug
                ]);
            }

             // KEYWORD ONLY
            if (empty($page->region_type)) {

                $urls[] = route('sales.keyword.only', [
                    'keywords' => $keywordSlug
                ]);
                continue;
            }

        }

        $urls = array_unique($urls);

        $this->info("Total URLs: " . count($urls));

        $chunks = array_chunk($urls, 1000);

        $sitemaps = [];

        foreach ($chunks as $i => $chunk) {

            $filename = 'sitemap-' . ($i + 1) . '.xml';
            $path = base_path('../public_html/sd-pn/'.$filename);

            $xml = $this->generateXml($chunk);

            file_put_contents($path, $xml);

            $sitemaps[] = url($filename);

            $this->info("Generated: $filename");
        }

        $index = $this->generateIndex($sitemaps);

        file_put_contents(base_path('../public_html/sd-pn/sitemap.xml'), $index);

        $this->info('Sitemap index generated.');
    }

    private function generateXml($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {

            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url, ENT_XML1) . '</loc>';
            $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function generateIndex($sitemaps)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($sitemaps as $map) {

            $xml .= '<sitemap>';
            $xml .= '<loc>' . $map . '</loc>';
            $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $xml .= '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }
}