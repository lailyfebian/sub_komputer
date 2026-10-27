<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'data',
    ];

    protected $casts = [
        'data' => 'array', // or 'json'
    ];

    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }
}
