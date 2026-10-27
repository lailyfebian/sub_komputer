<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class specificAddresses extends Model
{
    use HasFactory;

    protected $table = 'specific_addresses';

    protected $fillable = [
        'region_id',
        'category',
    ];

    /**
     * Region
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * School addresses
     */
    public function schoolAddresses()
    {
        return $this->hasMany(schoolAddresses::class, 'specific_address_id');
    }
}
