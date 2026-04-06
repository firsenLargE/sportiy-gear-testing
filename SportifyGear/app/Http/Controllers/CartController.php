<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with([
            'items.variant.product',
            'items.variant.images',
            'items.variant.discounts',
            'items.variant.attributeValues.attribute'
        ])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
        }

        $cartItems = $cart->items;

        // Attach final_price to each item for easy use in Blade
        foreach ($cartItems as $item) {
            $item->final_price = $this->getFinalPrice($item->variant);
        }

        return view('cart.index', compact('cartItems'));
    }

    /**
     * Get final price after applying active discount
     */
    private function getFinalPrice($variant)
    {
        $price = $variant->price ?? 0;

        if ($variant->discounts && $variant->discounts->isNotEmpty()) {
            $discount = $variant->discounts->first();

            $now = Carbon::now();

            // Check if discount is currently active
            if ($discount->start_date && $now->lt($discount->start_date)) {
                return round($price, 2);
            }
            if ($discount->end_date && $now->gt($discount->end_date)) {
                return round($price, 2);
            }

            if ($discount->discount_type === 'percentage') {
                $price -= ($price * $discount->discount_value) / 100;
            } else {
                $price -= $discount->discount_value;
            }
        }

        return round(max(0, $price), 2);
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $variant = ProductVariant::with('discounts')->findOrFail($request->variant_id);

        if ($variant->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . $variant->stock_quantity . ' items available in stock'
            ], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $variant->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more than available stock'
                ], 400);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $cartItem = CartItem::findOrFail($id);
        $this->authorizeCartAccess($cartItem->cart);

        $variant = ProductVariant::with('discounts')->findOrFail($cartItem->product_variant_id);

        if ($request->quantity > $variant->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . $variant->stock_quantity . ' items available'
            ], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
    }

    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $this->authorizeCartAccess($cartItem->cart);
        $cartItem->delete();

        $cartCount = CartItem::where('cart_id', $cartItem->cart_id)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $cartCount
        ]);
    }

    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function getCount()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $count = $cart ? $cart->items->sum('quantity') : 0;
        return response()->json(['count' => $count]);
    }

    private function authorizeCartAccess($cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    }
}
