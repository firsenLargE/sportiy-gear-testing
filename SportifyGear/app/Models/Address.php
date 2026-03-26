<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'shipping_zone_id',
        'name',
        'phone_no',
        'email',
        'province_id',
        'district_id',
        'address_line1',
        'address_line2',
        'nearest_landmark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
