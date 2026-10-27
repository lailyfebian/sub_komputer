<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    public static function generateNestedSlug($name, $parents = [])
    {
        $parents = array_filter($parents);
        $slugParts = array_map(fn($p) => Str::slug($p), array_merge([$name], $parents));
        return implode('-', $slugParts);
    }
}
