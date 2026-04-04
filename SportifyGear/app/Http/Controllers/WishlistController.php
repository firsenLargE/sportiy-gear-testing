<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{


    // Get user's wishlist
    public function index()
    {
        $wishlistItems = Wishlist::with(['product' => function ($q) {
            $q->with([
                'images' => function ($img) {
                    $img->where('is_primary', true);
                },
                'variants' => function ($var) {
                    $var->with('discounts')->where('stock_quantity', '>', 0);
                }
            ]);
        }])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    // Add to wishlist
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ], 400);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'wishlist_count' => $wishlistCount
        ]);
    }

    // Remove from wishlist
    public function remove($id)
    {
        $wishlistItem = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->firstOrFail();

        $wishlistItem->delete();

        $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist',
            'wishlist_count' => $wishlistCount
        ]);
    }

    // Toggle wishlist (add if not exists, remove if exists)
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlistItem = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $action = 'removed';
            $message = 'Removed from wishlist';
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);
            $action = 'added';
            $message = 'Added to wishlist';
        }

        $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'action' => $action,
            'wishlist_count' => $wishlistCount
        ]);
    }

    // Check if product is in wishlist
    public function check($productId)
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'in_wishlist' => $exists
        ]);
    }
}
