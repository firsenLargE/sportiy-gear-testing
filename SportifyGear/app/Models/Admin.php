<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
