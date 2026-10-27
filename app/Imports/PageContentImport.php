<?php

namespace App\Imports;

use App\Models\PageContent;
use App\Models\KeywordTemplate;
use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;

class PageContentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Trim semua string
        $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);
        
        $existing = null;

        $keywordText = $row['kata_kunci'] ?? null;
        $url         = $row['gdocs_url'] ?? ($row['url'] ?? '');
        $judul       = $row['title'] ?? 'Tanpa Judul';
        $templateIndex = $row['template_index'] ?? null;

        if (!$keywordText) {
            return null;
        }
        
        $keywordTemplate = KeywordTemplate::where(
            'name',
            $keywordText
        )->first();
        
        $regionType = isset($row['region_type'])
            ? strtolower(trim($row['region_type']))
            : null;
        
        if ($keywordTemplate) {
        
            $existingPage = PageContent::where(
                'keyword_template_id',
                $keywordTemplate->id
            )
            ->where(function ($q) use ($regionType) {
        
                if ($regionType === null || $regionType === '') {
                    $q->whereNull('region_type');
                } else {
                    $q->where('region_type', $regionType);
                }
            })
            ->first();
        
            // =========================
            // UPDATE TITLE + META SAJA
            // =========================
            if ($existingPage) {
        
                $existingPage->update([
                    'title' => $row['title'] ?? $existingPage->title,
                    'meta_description' =>
                        $row['meta_description']
                        ?? $existingPage->meta_description,
                ]);
        
                return null;
            }
        }

        // =========================
        // 1. FETCH GOOGLE DOCS TXT
        // =========================
        try {
            $txt = $this->fetchGoogleDocsTxt($url);
        } catch (Exception $e) {
            throw new Exception(
                "GAGAL IMPORT PADA JUDUL: '{$judul}'.\nAlasan: " . $e->getMessage()
            );
        }

        // =========================
        // 2. PARSE KE STRUCTURE
        // =========================
        $data = $this->parseTxtToSections($txt);

        // =========================
        // 3. DATABASE LOGIC
        // =========================
        $keywordTemplate = KeywordTemplate::firstOrCreate(
            ['name' => $keywordText],
            ['slug' => Str::slug($keywordText) ?: Str::random(8)]
        );

        $regionType = isset($row['region_type']) ? strtolower($row['region_type']) : null;
        $regionName = $row['region_name'] ?? null;
        $parentCityName = $row['parent_city_id'] ?? null;
        $parentDistrictName = $row['parent_district_id'] ?? null;

        $region = null;
        $parentCity = null;
        $parentDistrict = null;

        DB::transaction(function () use (
            &$region, &$parentCity, &$parentDistrict,
            $regionType, $regionName, $parentCityName, $parentDistrictName
        ) {
            if ($parentCityName) {
                $parentCity = Region::firstOrCreate(
                    ['name' => $parentCityName, 'type' => 'kota'],
                    ['slug' => Str::slug($parentCityName) ?: Str::random(8)]
                );
            }

            if ($parentDistrictName) {
                $parentDistrict = Region::firstOrCreate(
                    ['name' => $parentDistrictName, 'type' => 'kecamatan'],
                    [
                        'slug' => Str::slug($parentDistrictName) ?: Str::random(8),
                        'parent_id' => $parentCity?->id
                    ]
                );
            }

            if ($regionName) {
                $region = Region::firstOrCreate(
                    ['name' => $regionName, 'type' => $regionType],
                    ['slug' => Str::slug($regionName) ?: Str::random(8)]
                );
            }
        });

        $slug = Str::slug($row['title']);
        if (PageContent::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(5);
        }
        

        return new PageContent([
            'keyword_template_id' => $keywordTemplate->id,
            'region_type'         => $regionType,
            'region_id'           => $region?->id,
            'parent_city_id'      => $parentCity?->id,
            'parent_district_id'  => $parentDistrict?->id,
            'title'               => $row['title'] ?? null,
            'meta_description'    => $row['meta_description'] ?? null,
            'category'            => $row['category'] ?? null,
            'template_index'      => $row['template_index'] ?? null,
            'slug'                => $slug,
            'data'                => $this->cleanUtf8Deep($data),
        ]);
    }

    // =========================================================================
    // FETCH GOOGLE DOCS TXT
    // =========================================================================
    private function fetchGoogleDocsTxt(string $url): string
    {
        if (!$url) {
            throw new Exception("URL Kosong.");
        }

        preg_match('/document\/d\/([^\/]+)/', $url, $m);
        $docId = $m[1] ?? null;

        if (!$docId) {
            throw new Exception("Format URL Google Docs tidak valid.");
        }

        $txtUrl = "https://docs.google.com/document/d/{$docId}/export?format=txt";

        try {
            $response = Http::timeout(15)->get($txtUrl);
        } catch (\Exception) {
            throw new Exception("Timeout saat mengambil Google Docs.");
        }

        if ($response->failed()) {
            $status = $response->status();
            if (in_array($status, [401, 403])) {
                throw new Exception("Dokumen private ({$status}). Ubah ke 'Anyone with the link'.");
            }
            if ($status === 404) {
                throw new Exception("Dokumen tidak ditemukan (404).");
            }
            throw new Exception("Gagal mengambil data (HTTP {$status}).");
        }

        $content = trim($response->body());
        if ($content === '') {
            throw new Exception("Isi Google Docs kosong.");
        }

        return $content;
    }

    // =========================================================================
    // PARSER STATE-DRIVEN (FSM) – FINAL
    // =========================================================================
    private function parseTxtToSections(string $txt): array
    {
        $txt = str_replace(["\r\n", "\r"], "\n", $txt);
        $txt = preg_replace("/^\xEF\xBB\xBF|\x{FEFF}/u", '', $txt);
        $txt = preg_replace('/[\x{00A0}\x{2000}-\x{200B}]/u', ' ', $txt);

        $lines = explode("\n", $txt);

        $sections = [];
        $currentSection = null;
        $currentItem = null;
        $inContent = false;
        $contentBuffer = [];

        $flushItem = function () use (&$currentItem, &$currentSection) {
            if ($currentItem && $currentSection) {
                $currentSection['items'][] = $currentItem;
                $currentItem = null;
            }
        };

        $flushSection = function () use (&$currentSection, &$sections, &$contentBuffer) {
            if ($currentSection) {
                if (!empty($contentBuffer)) {
                    $currentSection['content'] = $this->processContentToHtml($contentBuffer);
                }
                $sections[] = $currentSection;
                $currentSection = null;
                $contentBuffer = [];
            }
        };

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            $trim = trim($line);

            // -------------------------
            // SECTION (HARD RESET)
            // -------------------------
            if (preg_match('/^#SECTION\s+(.+)/i', $trim, $m)) {
                $flushItem();
                $flushSection();

                $currentSection = [
                    'type'    => strtolower(trim($m[1])),
                    'title'   => '',
                    'content' => '',
                    'items'   => [],
                ];

                $inContent = false;
                continue;
            }

            if (!$currentSection) {
                continue;
            }

            // -------------------------
            // ITEM
            // -------------------------
            if (preg_match('/^#ITEM:/i', $trim)) {
                $flushItem();
                $currentItem = ['title' => '', 'description' => ''];
                $inContent = false;
                continue;
            }

            // -------------------------
            // TITLE (SECTION / ITEM)
            // -------------------------
            if (preg_match('/^#TITLE:/i', $trim)) {
                $j = $i + 1;
                while (isset($lines[$j]) && trim($lines[$j]) === '') {
                    $j++;
                }

                // Guard: jangan konsumsi penanda
                if (!isset($lines[$j]) || preg_match('/^#(SECTION|ITEM|CONTENT|DESCRIPTION)\b/i', trim($lines[$j]))) {
                    continue;
                }

                $text = trim($lines[$j]);
                $i = $j;

                if ($currentItem) {
                    $currentItem['title'] = $text;
                } else {
                    $currentSection['title'] = $text;
                }
                continue;
            }

            // -------------------------
            // CONTENT
            // -------------------------
            if (preg_match('/^#CONTENT:/i', $trim)) {
                $inContent = true;
                continue;
            }

            // -------------------------
            // DESCRIPTION (ITEM)
            // -------------------------
            if (preg_match('/^#DESCRIPTION:/i', $trim)) {
                if ($currentItem) {

                    $j = $i + 1;
                    $descriptionBuffer = [];

                    // Lewati baris kosong setelah #DESCRIPTION
                    while (isset($lines[$j]) && trim($lines[$j]) === '') {
                        $j++;
                    }

                    // Ambil semua baris sampai menemukan marker #
                    while (
                        isset($lines[$j]) &&
                        !preg_match('/^#(SECTION|ITEM|TITLE|CONTENT|DESCRIPTION)\b/i', trim($lines[$j]))
                    ) {
                        $descriptionBuffer[] = $lines[$j];
                        $j++;
                    }

                    // Proses description menjadi HTML
                    if (!empty($descriptionBuffer)) {
                        $currentItem['description'] = $this->processContentToHtml($descriptionBuffer);
                    }

                    // Posisikan pointer ke marker berikutnya
                    $i = $j - 1;
                }

                continue;
            }

            // -------------------------
            // TEXT CONTENT
            // -------------------------
            if ($inContent) {
                $contentBuffer[] = $line;
            }
        }

        // FINAL FLUSH
        if ($currentItem) {
            $currentSection['items'][] = $currentItem;
        }
        if ($currentSection) {
            if (!empty($contentBuffer)) {
                $currentSection['content'] = $this->processContentToHtml($contentBuffer);
            }
            $sections[] = $currentSection;
        }

        return $sections;
    }

    // =========================================================================
    // INLINE FORMATTER (bold & italic)
    // =========================================================================
    private function formatInline(string $text): string
    {
        // **bold**
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);

        // *italic*
        $text = preg_replace('/\*(.+?)\*/s', '<i>$1</i>', $text);

        return $text;
    }

    
    private function processContentToHtml(array $lines): string
    {
        $html = '';
    
        // Hapus baris kosong dan rapikan teks
        $lines = array_values(
            array_filter(
                $lines,
                fn ($line) => trim($line) !== ''
            )
        );
    
        $count = count($lines);
        $i = 0;
    
        while ($i < $count) {
            $trim = trim($lines[$i]);
    
            // =========================================
            // LIST: - item atau * item
            // =========================================
            if (preg_match('/^[-*]\s+(.+)$/', $trim)) {
                $html .= '<ul>';
    
                while ($i < $count) {
                    $current = trim($lines[$i]);
    
                    if (!preg_match('/^[-*]\s+(.+)$/', $current, $m)) {
                        break;
                    }
    
                    $html .= '<li>';
                    $html .= $this->formatInline(trim($m[1]));
                    $html .= '</li>';
    
                    $i++;
                }
    
                $html .= '</ul>';
    
                continue;
            }
    
            // =========================================
            // NORMAL CONTENT
            // Setiap baris menjadi paragraf sendiri
            // =========================================
            $html .= '<p>';
            $html .= $this->formatInline($trim);
            $html .= '</p>';
    
            $i++;
        }
    
        return $html;
    }


    // =========================================================================
    // UTF-8 CLEANER
    // =========================================================================
    private function cleanUtf8Deep($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->cleanUtf8Deep($v);
            }
    
            return $value;
        }
    
        if (!is_string($value)) {
            return $value;
        }
    
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}
