<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'description',
        'price',
        'stock_quantity',
        'weight'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values'
        );
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class);
    }
    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',
            'product_variant_id',
            'attribute_value_id'
        );
    }
    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }
}
