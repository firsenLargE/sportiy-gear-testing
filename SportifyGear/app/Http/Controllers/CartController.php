<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    // Get user's cart
    public function index()
    {
        $cart = Cart::with(['items.variant.product', 'items.variant.images', 'items.variant.discounts'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
        }

        $cartItems = $cart->items;

        $subtotal = $cartItems->sum(function ($item) {
            // Calculate price with discount
            $price = $item->price;
            if ($item->variant && $item->variant->discounts->isNotEmpty()) {
                $discount = $item->variant->discounts->first();
                if ($discount->discount_type === 'percentage') {
                    $price = $item->variant->price - ($item->variant->price * $discount->discount_value / 100);
                } else {
                    $price = $item->variant->price - $discount->discount_value;
                }
            }
            return $price * $item->quantity;
        });

        $shipping = $subtotal > 2000 ? 0 : 100; // Free shipping over Rs. 2000
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cart', 'cartItems', 'subtotal', 'shipping', 'total'));
    }

    // Add item to cart
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $variant = ProductVariant::with('discounts')->findOrFail($request->variant_id);

        // Check stock
        if ($variant->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . $variant->stock_quantity . ' items available in stock'
            ], 400);
        }

        // Get or create cart
        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        // Calculate final price with discount
        $finalPrice = $variant->price;
        if ($variant->discounts->isNotEmpty()) {
            $discount = $variant->discounts->first();
            if ($discount->discount_type === 'percentage') {
                $finalPrice -= ($variant->price * $discount->discount_value) / 100;
            } else {
                $finalPrice -= $discount->discount_value;
            }
        }

        // Check if item already in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            // Update quantity
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
            // Add new item
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $finalPrice
            ]);
        }

        $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart_count' => $cartCount
        ]);
    }

    // Update cart item quantity
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $cartItem = CartItem::findOrFail($id);
        $this->authorizeCartAccess($cartItem->cart);

        $variant = ProductVariant::findOrFail($cartItem->product_variant_id);

        if ($request->quantity > $variant->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . $variant->stock_quantity . ' items available'
            ], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // Recalculate totals
        $cart = $cartItem->cart;
        $subtotal = $cart->items->sum(function ($item) {
            $price = $item->price;
            if ($item->variant && $item->variant->discounts->isNotEmpty()) {
                $discount = $item->variant->discounts->first();
                if ($discount->discount_type === 'percentage') {
                    $price = $item->variant->price - ($item->variant->price * $discount->discount_value / 100);
                } else {
                    $price = $item->variant->price - $discount->discount_value;
                }
            }
            return $price * $item->quantity;
        });
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'subtotal' => number_format($subtotal, 2),
            'shipping' => number_format($shipping, 2),
            'total' => number_format($total, 2),
            'item_total' => number_format($cartItem->price * $cartItem->quantity, 2)
        ]);
    }

    // Remove item from cart
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $this->authorizeCartAccess($cartItem->cart);
        $cartItem->delete();

        // Recalculate totals
        $cart = $cartItem->cart;
        $subtotal = $cart->items->sum(function ($item) {
            $price = $item->price;
            if ($item->variant && $item->variant->discounts->isNotEmpty()) {
                $discount = $item->variant->discounts->first();
                if ($discount->discount_type === 'percentage') {
                    $price = $item->variant->price - ($item->variant->price * $discount->discount_value / 100);
                } else {
                    $price = $item->variant->price - $discount->discount_value;
                }
            }
            return $price * $item->quantity;
        });
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;

        $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $cartCount,
            'subtotal' => number_format($subtotal, 2),
            'shipping' => number_format($shipping, 2),
            'total' => number_format($total, 2)
        ]);
    }

    // Clear entire cart
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

    // Get cart count for header
    public function getCount()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $count = $cart ? $cart->items->sum('quantity') : 0;

        return response()->json([
            'count' => $count
        ]);
    }

    private function authorizeCartAccess($cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    }
}
