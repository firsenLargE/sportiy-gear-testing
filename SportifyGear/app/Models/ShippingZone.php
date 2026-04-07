<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'district_id',
        'shipping_fee',
        'is_active',
    ];


    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getShippingStatusAttribute(): string
    {
        return $this->is_active ? 'Available' : 'Not Available';
    }
}
