<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
    public function productVariants()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            AttributeValue::class,
            'attribute_id',          // FK on attribute_values
            'id',                    // FK on product_variants (via pivot)
            'id',                    // Local key on attributes
            'id'                     // Local key on attribute_values
        );
    }
}
