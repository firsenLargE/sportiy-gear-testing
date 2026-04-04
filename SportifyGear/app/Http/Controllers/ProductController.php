<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with([
                'categories:id,name,slug',
                'images:id,product_id,image_path,is_primary',
                'variants.discounts:id,name,discount_type,discount_value',
                'variants.attributeValues',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true);

        // 🔍 Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 📂 Category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 💰 Price
        if ($request->filled('min') || $request->filled('max')) {
            $query->whereHas('variants', function ($q) use ($request) {
                if ($request->filled('min')) {
                    $q->where('price', '>=', $request->min);
                }
                if ($request->filled('max')) {
                    $q->where('price', '<=', $request->max);
                }
            });
        }

        // 🎯 Attributes
        $attributes = Attribute::with('values')->get();
        foreach ($attributes as $attribute) {
            $values = $request->get('attribute_' . $attribute->id, []);
            if (!empty($values)) {
                $query->whereHas('variants.attributeValues', function ($q) use ($values) {
                    $q->whereIn('attribute_value_id', $values);
                });
            }
        }

        // 🔽 Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {

                case 'latest':
                    $query->latest(); // order by created_at desc
                    break;

                case 'price_low_high':
                    $query->whereHas('variants')
                        ->withMin('variants', 'price')
                        ->orderBy('variants_min_price', 'asc');
                    break;

                case 'price_high_low':
                    $query->whereHas('variants')
                        ->withMin('variants', 'price')
                        ->orderBy('variants_min_price', 'desc');
                    break;

                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;

                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
            }
        } else {
            // default sorting
            $query->latest();
        }

        // 📦 Pagination
        $products = $query->paginate(9)->withQueryString();

        // 🌳 Categories
        $categories = Category::with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();

        return view('products.index', compact('products', 'categories', 'attributes'));
    }


    public function homeProducts()
    {
        $products = Product::with([
            'categories:id,name,slug',
            'images:id,product_id,image_path,is_primary',
            'variants.attributeValues',
            'variants.discounts:id,name,discount_type,discount_value'
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        // Pass products to the home page
        return view('pages.home', compact('products'));
    }



    public function show($slug)
    {
        // Get the main product by slug
        $product = Product::with([
            'categories:id,name,slug',
            'images:id,product_id,image_path,is_primary',
            'variants' => function ($q) {
                $q->with([
                    'attributeValues:id,attribute_id,value', // correct columns
                    'discounts:id,name,discount_type,discount_value',
                    'images:id,product_variant_id,image_path,is_primary',
                ]);
            },
            'reviews.user:id,name', // User info for reviews
        ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail(); // 404 if not found

        // Related products (same categories, exclude current product)
        $relatedProducts = Product::with([
            'images:id,product_id,image_path,is_primary',
            'variants' => function ($q) {
                $q->with([
                    'attributeValues:id,attribute_id,value',
                    'discounts:id,name,discount_type,discount_value',
                    'images:id,product_variant_id,image_path,is_primary',
                ]);
            },
        ])
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('products.id', '!=', $product->id) // avoid ambiguity
            ->take(9)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
