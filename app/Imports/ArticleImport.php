<?php

namespace App\Imports;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ArticleImport implements ToModel, WithHeadingRow
{
    /**
     * Clean string/array to valid UTF-8 and remove BOM/control chars
     */
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

        // Remove BOM and control characters
        $value = preg_replace('/[\x00-\x1F\x80-\xFF]/u', '', $value);

        // Force valid UTF-8
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }

    public function model(array $row)
    {
        // Trim semua value string
        $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

        // Ambil data dari Google Docs
        $txt = $this->fetchGoogleDocsTxt($row['gdocs_url'] ?? null);
        $data = $this->parseTxtToSections($txt);

        // Buat PageContent
        return new Article([
            'title'=> $row['title'] ?? null,
            'slug' => Str::slug($row['title'] ?? '') ?? Str::random(8),
            'data' => $this->cleanUtf8Deep($data),
        ]);
    }


    private function fetchGoogleDocsTxt($url)
    {
        if (!$url) return '';

        // Extract document id robustly
        if (preg_match('/document\/d\/([^\/]+)/', $url, $m)) {
            $docId = $m[1];
        } else {
            return '';
        }

        $txtUrl = "https://docs.google.com/document/d/{$docId}/export?format=txt";

        return @file_get_contents($txtUrl) ?: '';
    }

    /**
     * Convert bullet lists in plain text into simple <ul><li> HTML
     */
    private function convertBulletsToHtml($text)
    {
        $lines = preg_split("/\r\n|\r|\n/", trim($text));

        $items = [];
        $currentTitle = null;
        $currentDescription = '';

        foreach ($lines as $line) {
            $trim = ltrim($line);

            if (preg_match('/^[\-\*•]\s*(.+)$/', $trim, $m)) {
                if ($currentTitle !== null) {
                    $items[] = ['title' => $currentTitle, 'description' => trim($currentDescription)];
                }

                $currentTitle = $m[1];
                $currentDescription = '';
                continue;
            }

            if ($currentTitle !== null) {
                $currentDescription .= ' ' . trim($line);
            }
        }

        if ($currentTitle !== null) {
            $items[] = ['title' => $currentTitle, 'description' => trim($currentDescription)];
        }

        $html = "<ul>";
        foreach ($items as $item) {
            $html .= "<li>" . $item['title'];
            if (!empty($item['description'])) {
                $html .= "<p>" . $item['description'] . "</p>";
            }
            $html .= "</li>";
        }
        $html .= "</ul>";

        return $html;
    }

    private function parseTxtToSections($txt)
    {
        if (!$txt) return [];

        // Normalize lines, remove BOM + weird spaces
        $lines = preg_split("/\r\n|\r|\n/", $txt);
        $clean = [];
        foreach ($lines as $line) {
            $line = preg_replace("/^\xEF\xBB\xBF|\x{FEFF}/u", '', $line);
            $line = preg_replace('/[\x{00A0}\x{2000}-\x{200B}]/u', ' ', $line);
            $line = trim($line);
            if ($line !== '') $clean[] = $line;
        }
        $lines = array_values($clean);

        $sections = [];
        $currentSection = null;
        $state = null;
        $activeItem = null;

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            if (preg_match('/^#SECTION\s+(.+)/i', $line, $m)) {
                if ($activeItem && $currentSection) {
                    $currentSection['items'][] = $activeItem;
                    $activeItem = null;
                }
                if ($currentSection) {
                    $sections[] = $currentSection;
                }

                $currentSection = ['type' => strtolower(trim($m[1])), 'title' => '', 'content' => '', 'items' => []];
                continue;
            }

            if ($line === '#TITLE:' && $currentSection) {
                if (isset($lines[$i + 1])) {
                    $currentSection['title'] = $lines[++$i];
                }
                continue;
            }

            if ($line === '#CONTENT:' && $currentSection) {
                $state = 'section_content';
                continue;
            }

            if ($state === 'section_content' && $currentSection) {
                if (str_starts_with($line, '#')) {
                    $state = null;
                    $i--;
                } else {
                    $currentSection['content'] .= $line . "\n";
                    continue;
                }
            }
        }

        if ($currentSection) {
            $sections[] = $currentSection;
        }

        // Convert bullet lists to html for readability
        foreach ($sections as &$section) {
            if (!empty($section['content']) && preg_match('/^[\-\*•]/m', $section['content'])) {
                $section['content'] = $this->convertBulletsToHtml($section['content']);
            }
        }

        return $sections;
    }

    private function isHeading($line)
    {
        return strlen($line) > 3 && strtoupper($line) === $line;
    }
}
