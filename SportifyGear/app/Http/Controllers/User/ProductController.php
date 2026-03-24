<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of active products with their variants.
     */
    public function index()
    {
        // Get all active products with variants and their images
        $products = Product::with([
            'variants.images', // eager load variant images
            'images',          // product images
            'categories'       // optional: categories
        ])
            ->where('is_active', true)
            ->get();

        // Pass to view
        return view('user.products.index', compact('products'));
    }

    /**
     * Display a single product with its variants
     */
    public function show($slug)
    {
        $product = Product::with([
            'variants.images',
            'images',
            'categories',
            'attributes',
            'variants.attributeValues'
        ])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('user.products.show', compact('product'));
    }
}
