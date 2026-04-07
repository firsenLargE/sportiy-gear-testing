<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;          // <-- ADD THIS
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Checkout page (cart-based) – not used if you use CartController@checkout
    public function checkout()
    {
        $cart = Cart::with(['items.variant.product', 'items.variant.discounts'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $addresses = Address::where('user_id', Auth::id())->get();

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

        return view('orders.checkout', compact('cart', 'addresses', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Place order – supports:
     * - Direct single product (product_variant_id)
     * - Full cart checkout (no selected_items)
     * - Selected cart items (selected_items[])
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id'          => 'required|exists:addresses,id',
            'coupon_code'         => 'nullable|exists:coupons,code',
            'product_variant_id'  => 'nullable|exists:product_variants,id',
            'quantity'            => 'nullable|integer|min:1',
            'payment_method'      => 'nullable|string|in:cod,esewa,khalti',
            'selected_items'      => 'nullable|array',
            'selected_items.*'    => 'exists:cart_items,id',
        ]);

        // Verify address belongs to user
        $address = Address::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return back()->with('error', 'Invalid address');
        }

        // ------------------------------
        // DIRECT ORDER (single product)
        // ------------------------------
        if ($request->filled('product_variant_id')) {
            $variant = ProductVariant::with('product', 'discounts')->findOrFail($request->product_variant_id);
            $quantity = $request->input('quantity', 1);

            if ($variant->stock_quantity < $quantity) {
                return back()->with('error', "Insufficient stock for {$variant->product->name}. Only {$variant->stock_quantity} left.");
            }

            $price = $variant->price;
            if ($variant->discounts->isNotEmpty()) {
                $discount = $variant->discounts->first();
                if ($discount->discount_type === 'percentage') {
                    $price -= ($variant->price * $discount->discount_value / 100);
                } else {
                    $price -= $discount->discount_value;
                }
            }

            $subtotal = $price * $quantity;
            $shipping = $subtotal > 2000 ? 0 : 100;
            $total = $subtotal + $shipping;

            DB::beginTransaction();
            try {
                $order = Order::create([
                    'user_id'        => Auth::id(),
                    'address_id'     => $address->id,
                    'shipping_fee'   => $shipping,
                    'status_id'      => 1,
                    'order_number'   => 'ORD-' . strtoupper(uniqid()),
                    'sub_total'      => $subtotal,
                    'total'          => $total,
                    'payment_method' => $request->payment_method ?? 'cod',
                ]);

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity'           => $quantity,
                    'price'              => $price,
                ]);

                $variant->decrement('stock_quantity', $quantity);

                DB::commit();
                return redirect()->route('orders.success', $order)
                    ->with('success', 'Order placed successfully!');
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'Failed to place order. Please try again.');
            }
        }

        // ------------------------------
        // CART-BASED ORDER (supports selection)
        // ------------------------------
        $selectedItemIds = $request->input('selected_items', []);

        // If specific items are selected, fetch only those
        if (!empty($selectedItemIds)) {
            $cartItems = CartItem::with('variant')
                ->whereIn('id', $selectedItemIds)
                ->get();

            if ($cartItems->isEmpty()) {
                return back()->with('error', 'Selected items not found.');
            }
        } else {
            // Fallback: full cart (no selection mode)
            $cart = Cart::with(['items.variant'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return back()->with('error', 'Your cart is empty');
            }
            $cartItems = $cart->items;
        }

        // Stock check for all items being ordered
        foreach ($cartItems as $item) {
            if ($item->variant->stock_quantity < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->variant->product->name}");
            }
        }

        // Calculate subtotal (with discounts)
        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->variant->price ?? 0;
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

        // Apply coupon if provided
        $coupon = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                if ($coupon->discount_type === 'percentage') {
                    $discount = ($total * $coupon->discount_value) / 100;
                    $total -= $discount;
                } else {
                    $total -= $coupon->discount_value;
                }
            }
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'        => Auth::id(),
                'coupon_id'      => $coupon ? $coupon->id : null,
                'address_id'     => $address->id,
                'shipping_fee'   => $shipping,
                'status_id'      => 1,
                'order_number'   => 'ORD-' . strtoupper(uniqid()),
                'sub_total'      => $subtotal,
                'total'          => $total,
                'payment_method' => $request->payment_method ?? 'cod',
            ]);

            foreach ($cartItems as $item) {
                $price = $item->variant->price ?? 0;
                if ($item->variant && $item->variant->discounts->isNotEmpty()) {
                    $discount = $item->variant->discounts->first();
                    if ($discount->discount_type === 'percentage') {
                        $price = $item->variant->price - ($item->variant->price * $discount->discount_value / 100);
                    } else {
                        $price = $item->variant->price - $discount->discount_value;
                    }
                }

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item->variant->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity'           => $item->quantity,
                    'price'              => $price,
                ]);

                $item->variant->decrement('stock_quantity', $item->quantity);
            }

            // Clear ONLY the used items from the cart
            if (!empty($selectedItemIds)) {
                CartItem::whereIn('id', $selectedItemIds)->delete();
            } else {
                // Full cart fallback: clear whole cart
                $cart = Cart::where('user_id', Auth::id())->first();
                if ($cart) $cart->items()->delete();
            }

            DB::commit();
            return redirect()->route('orders.success', $order)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    // Order success page
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        $order->load(['items.productVariant.product', 'address', 'status']);
        return view('orders.success', compact('order'));
    }

    // My orders list
    public function myOrders()
    {
        $orders = Order::with(['status', 'items.productVariant.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // Single order details
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        $order->load(['items.productVariant.product', 'address', 'status', 'coupon']);
        return view('orders.show', compact('order'));
    }

    // Cancel order
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if (!in_array($order->status_id, [1, 2])) {
            return back()->with('error', 'This order cannot be cancelled');
        }

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                $item->productVariant->increment('stock_quantity', $item->quantity);
            }
            $order->update(['status_id' => 5]);
            DB::commit();
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel order');
        }
    }

    // Show direct order form for a single product/variant (GET route)
    public function directOrderForm($productId, $variantId = null)
    {
        $product = Product::findOrFail($productId);

        if (!$variantId) {
            $variant = $product->variants()->first();
        } else {
            $variant = $product->variants()->findOrFail($variantId);
        }

        $price = $variant->price;
        if ($variant->discounts->isNotEmpty()) {
            $discount = $variant->discounts->first();
            if ($discount->discount_type === 'percentage') {
                $price -= ($variant->price * $discount->discount_value / 100);
            } else {
                $price -= $discount->discount_value;
            }
        }

        $subtotal = $price;
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;

        $addresses = Address::where('user_id', Auth::id())->get();

        return view('orders.place', compact('product', 'variant', 'price', 'subtotal', 'shipping', 'total', 'addresses'));
    }
}
