<?php

namespace App\Http\Controllers;

use App\Models\Address;
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

        foreach ($cartItems as $item) {
            $item->final_price = $this->getFinalPrice($item->variant);
        }

        return view('cart.index', compact('cartItems'));
    }

    private function getFinalPrice($variant)
    {
        $price = $variant->price ?? 0;

        if ($variant->discounts && $variant->discounts->isNotEmpty()) {
            $discount = $variant->discounts->first();
            $now = Carbon::now();

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

    /**
     * Checkout 
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to proceed to checkout.');
        }

        $selectedItemIds = $request->input('selected_items', []);
        if (empty($selectedItemIds)) {
            return redirect()->route('cart.index')->with('error', 'Please select at least one item to checkout.');
        }

        // Get the user's cart
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $selectedItems = CartItem::with([
            'variant.product',
            'variant.images',
            'variant.discounts',
            'variant.attributeValues.attribute'
        ])
            ->where('cart_id', $cart->id)
            ->whereIn('id', $selectedItemIds)
            ->get();
        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Selected items not found in your cart.');
        }

        foreach ($selectedItems as $item) {
            $item->final_price = $this->getFinalPrice($item->variant);
        }

        $subtotal = $selectedItems->sum(function ($item) {
            return ($item->final_price ?? $item->variant->price ?? 0) * $item->quantity;
        });

        $addresses = Address::with(['province', 'district', 'shippingZone'])
            ->where('user_id', $user->id)
            ->get();
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;

        return view('cart.checkout', compact('selectedItems', 'addresses', 'subtotal', 'shipping', 'total'));
    }

    private function getItemFinalPrice($item)
    {
        $price = $item->price;
        if ($item->variant && $item->variant->discounts->isNotEmpty()) {
            $discount = $item->variant->discounts->first();
            if ($discount->discount_type === 'percentage') {
                $price = $item->variant->price - ($item->variant->price * $discount->discount_value / 100);
            } else {
                $price = $item->variant->price - $discount->discount_value;
            }
        }
        return $price;
    }
}
