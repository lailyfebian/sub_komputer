<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\SlugHelper;
use Illuminate\Support\Facades\Cache;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'slug', 'type', 'address'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($region) {
            $region->generateSlug();
        });

        static::updating(function ($region) {
            $region->generateSlug();
        });
    }
    
    protected static function booted()
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function specificAddresses()
    {
        return $this->hasMany(specificAddresses::class, 'region_id');
    }

    public function generateSlug()
    {
        $parents = [];
        $currentParent = $this->parent;

        while ($currentParent) {
            $parents[] = $currentParent->name;
            $currentParent = $currentParent->parent;
        }

        $this->slug = SlugHelper::generateNestedSlug($this->name, $parents);
    }

}
