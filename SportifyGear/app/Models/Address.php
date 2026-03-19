<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone_no',
        'email',
        'province',
        'district',
        'address_line1',
        'address_line2',
        'nearest_landmark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
