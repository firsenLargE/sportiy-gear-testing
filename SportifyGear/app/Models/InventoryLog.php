<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_variant_id',
        'change_type',
        'quantity_change',
        'reference_id'
    ];
}
