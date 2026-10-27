<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PageContent;
use Illuminate\Support\Facades\Cache;

class KeywordTemplate extends Model
{
    use HasFactory;

    protected $table = 'keyword_templates';

    protected $fillable = [
        'name',
        'slug'
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }


    public function pages()
    {
        return $this->hasMany(PageContent::class);
    }
}
