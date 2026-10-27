<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Helpers\PageContentHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PageContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_template_id',
        'region_id',
        'region_type',
        'parent_city_id',
        'parent_district_id',
        'title',
        'meta_description',
        'slug',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Relasi ke KeywordTemplate
     */
    public function keywordTemplate()
    {
        return $this->belongsTo(KeywordTemplate::class);
    }

    /**
     * Relasi ke Region
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Relasi opsional ke kota & kecamatan (jika dibutuhkan untuk filter)
     */
    public function parentCity()
    {
        return $this->belongsTo(Region::class, 'parent_city_id');
    }

    public function parentDistrict()
    {
        return $this->belongsTo(Region::class, 'parent_district_id');
    }

    /**
     * Accessor untuk menampilkan wilayah (Global, Kecamatan Bandung, dsb)
     */
    public function getRegionDisplayAttribute(): string
    {
        if ($this->region_type === 'kota' || !$this->region_id) {
            return 'Global';
        }

        if ($this->region) {
            return ucfirst($this->region_type) . ' ' . $this->region->name;
        }

        return 'Global';
    }

    /**
     * Generate slug unik sebelum disimpan
     */
    protected static function booted()
    {
        static::saving(function (PageContent $page) {
            if (empty($page->slug)) {
                $page->slug = static::generateUniqueSlug($page->title);
            }
        });

        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    /**
     * Fungsi untuk membuat slug unik
     */
    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
