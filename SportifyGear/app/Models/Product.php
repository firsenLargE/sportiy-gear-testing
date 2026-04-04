<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'is_active',
        'admin_id'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }

    // Display Image
    public function getPrimaryImageAttribute()
    {
        $primary = $this->images->where('is_primary', true)->first();
        return $primary ? asset('storage/' . $primary->image_path) : null;
    }

    // Minimum Price
    public function getMinPriceAttribute()
    {
        return $this->variants->min('price') ?? 0;
    }

    // Maximum Price
    public function getMaxPriceAttribute()
    {
        return $this->variants->max('price') ?? 0;
    }

    // Default Variant (cheapest)
    public function getDefaultVariantAttribute()
    {
        return $this->variants->sortBy('price')->first();
    }
}
