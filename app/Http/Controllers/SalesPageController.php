<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\PageContent;
use App\Models\Region;
use App\Models\KeywordTemplate;

class SalesPageController extends Controller
{
    /**
     * ----------------------------------------------
     * KEYWORD + REGION
     * ----------------------------------------------
     */
    public function keywordWithRegion($keywords, $type, $locationSlug)
    {
            $region = Region::where('slug', $locationSlug)->firstOrFail();
            $regionType = $region->type;

            $keyword = KeywordTemplate::where('slug', $keywords)->firstOrFail();

            // Ambil parent_id sesuai hierarki
            $parentDistrictId = $region->type === 'kelurahan' || $region->type === 'desa' ? $region->parent_id : null;
            $parentCityId = match ($regionType) {
                'kecamatan' => $region->parent_id,
                'kelurahan', 'desa' => $region->parent?->parent_id,
                default => null,
            };

            // Build region trail
            $regionTrail = $this->buildRegionTrail($region);

            // Ambil PageContent sesuai fallback logic
            $page = $this->findPageContent($keyword->id, $region->id, $regionType, $parentDistrictId, $parentCityId);
            
            if (! $page) {
                return redirect('/');
            }

            // Replace token {region} & {year}
            $year = date('Y');
            $page->title = str_replace(['{region}', '{year}'], [$regionTrail, $year], $page->title);
            $page->meta_description = str_replace(['{region}', '{year}'], [$regionTrail, $year], $page->meta_description);
            $page->slug  = str_replace(['{region}', '{year}'], [$regionTrail, $year], $page->slug);

            // Replace token di data section
            $data = $this->replaceTokensInData($page->data ?? [], $regionTrail, $year, $region->address ?? '');

            $view = [
                'region'      => $region,
                'regionType'  => $regionType,
                'keyword'     => $keyword,
                'page'        => $page,
                'data'        => $data,
                'regionTrail' => $regionTrail,
            ];

        return view('layouts.sales-page.show', $view);
    }

    /**
     * Build region trail string
     */
    private function buildRegionTrail(Region $region): string
    {
        $trail = $region->name;
        if (in_array($region->type, ['desa', 'kelurahan'])) {
            $trail .= ' ' . ($region->parent->name ?? '') . ' ' . ($region->parent?->parent->name ?? '');
        } elseif ($region->type === 'kecamatan') {
            $trail .= ' ' . ($region->parent->name ?? '');
        }
        return $trail;
    }

    /**
     * Cari PageContent sesuai fallback logic
     */
    private function findPageContent(int $keywordId, ?int $regionId, string $regionType, ?int $parentDistrictId, ?int $parentCityId)
    {
        $query = PageContent::where('keyword_template_id', $keywordId);

        // 1. Jika region_id ada → ambil paling spesifik
        if ($regionId) {
            $page = (clone $query)->where('region_id', $regionId)->first();
            if ($page) return $page;
        }

        // 2. Fallback berdasarkan region_type
        switch ($regionType) {
            case 'kota':
                if ($parentCityId) {
                    $page = (clone $query)
                        ->whereNull('region_id')
                        ->where('region_type', 'kota')
                        ->where('parent_city_id', $parentCityId)
                        ->first();
                    if ($page) return $page;
                }
                $page = (clone $query)
                    ->whereNull('region_id')
                    ->where('region_type', 'kota')
                    ->first();
                if ($page) return $page;
                break;

            case 'kecamatan':
            case 'kelurahan':
            case 'desa':
                $types = in_array($regionType, ['kelurahan', 'desa']) ? ['kelurahan', 'desa'] : ['kecamatan'];

                // Parent district spesifik
                if ($parentDistrictId) {
                    $page = (clone $query)
                        ->whereNull('region_id')
                        ->whereIn('region_type', $types)
                        ->where('parent_district_id', $parentDistrictId)
                        ->first();
                    if ($page) return $page;
                }

                // Parent city fallback
                if ($parentCityId) {
                    $page = (clone $query)
                        ->whereNull('region_id')
                        ->whereIn('region_type', $types)
                        ->whereNull('parent_district_id')
                        ->where('parent_city_id', $parentCityId)
                        ->first();
                    if ($page) return $page;
                }

                // Global fallback type
                $page = (clone $query)
                    ->whereNull('region_id')
                    ->whereIn('region_type', $types)
                    ->first();
                if ($page) return $page;

                break;
        }

        // 3. Global fallback terakhir
        return (clone $query)->whereNull('region_id')->firstOrFail();
    }


    /**
     * Replace token {region} & {year} & {alamat} di data section
     */
    private function replaceTokensInData(array $rawData, string $regionTrail, string $year, string $regionAddress): array
    {
        $data = [];
        foreach ($rawData as $section) {
            if (!empty($section['type'])) {
                array_walk_recursive($section, function (&$value) use ($regionTrail, $year, $regionAddress) {
                    if (is_string($value)) {
                        $value = str_replace(['{region}', '{year}', '{address}'], [$regionTrail, $year, $regionAddress], $value);
                    }
                });
                $data[$section['type']] = $section;
            }
        }
        return $data;
    }


    /**
     * ----------------------------------------------
     * KEYWORD ONLY
     * ----------------------------------------------
     */
    public function keywordOnly($keywords)
    {
            $keyword = KeywordTemplate::where('slug', $keywords)->firstOrFail();
            $page = PageContent::where('keyword_template_id', $keyword->id)
                ->whereNull('region_id')
                ->firstOrFail();
                
            if (! $page) {
                return redirect('/');
            }

            $year = date('Y');
            
            $regionTrail = '';

            $page->title = str_replace(['{region}', '{year}'], [$regionTrail, $year], $page->title);
            $page->meta_description = str_replace(['{region}', '{year}'], [$regionTrail, $year], $page->meta_description);
            $page->slug  = str_replace(['{region}', '{year}'], [$regionTrail, $year], $page->slug);

            $rawData = $page->data ?? [];
            $data = $this->replaceTokensInData($page->data ?? [], $regionTrail, $year, 'Jalan Taman Wijaya Kusuma, Ps. Baru, Kecamatan Sawah Besar, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10710');



            $view = [
                'region'  => null,
                'regionType' => null,
                'keyword' => $keyword,
                'page'    => $page,
                'data'    => $data
            ];

        return view('layouts.sales-page.show', $view);
    }

}
