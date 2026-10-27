<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schoolAddresses extends Model
{
    use HasFactory;

    protected $table = 'school_addresses';

    protected $fillable = [
        'specific_address_id',
        'school_name',
        'address',
    ];

    /**
     * Specific address
     */
    public function specificAddress()
    {
        return $this->belongsTo(
            specificAddresses::class,
            'specific_address_id'
        );
    }
}
