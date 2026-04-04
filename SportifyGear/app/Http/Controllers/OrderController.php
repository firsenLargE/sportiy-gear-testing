<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Checkout page
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

        return view('order.checkout', compact('cart', 'addresses', 'subtotal', 'shipping', 'total'));
    }

    // Place order
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'coupon_code' => 'nullable|exists:coupons,code'
        ]);

        $cart = Cart::with(['items.variant'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Your cart is empty');
        }

        // Verify address belongs to user
        $address = Address::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$address) {
            return back()->with('error', 'Invalid address');
        }

        // Check stock availability
        foreach ($cart->items as $item) {
            if ($item->variant->stock_quantity < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->variant->product->name}");
            }
        }

        // Calculate totals
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

        // Apply coupon if exists
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
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'coupon_id' => $coupon ? $coupon->id : null,
                'address_id' => $address->id,
                'shipping_fee' => $shipping,
                'status_id' => 1, // Assuming 1 = pending
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'sub_total' => $subtotal,
                'total' => $total,
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                $price = $item->price;
                if ($item->variant && $item->variant->discounts->isNotEmpty()) {
                    $discount = $item->variant->discounts->first();
                    if ($discount->discount_type === 'percentage') {
                        $price = $item->variant->price - ($item->variant->price * $discount->discount_value / 100);
                    } else {
                        $price = $item->variant->price - $discount->discount_value;
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->variant->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $price
                ]);

                // Reduce stock
                $item->variant->decrement('stock_quantity', $item->quantity);
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            return redirect()->route('order.success', $order)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    // Order success page
    public function success(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $order->load(['items.productVariant.product', 'address', 'status']);
        return view('order.success', compact('order'));
    }

    // My orders
    public function myOrders()
    {
        $orders = Order::with(['status', 'items.productVariant.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('order.index', compact('orders'));
    }

    // Order details
    public function show(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $order->load(['items.productVariant.product', 'address', 'status', 'coupon']);
        return view('order.show', compact('order'));
    }

    // Cancel order
    public function cancel(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if (!in_array($order->status_id, [1, 2])) { // 1=pending, 2=confirmed
            return back()->with('error', 'This order cannot be cancelled');
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                $item->productVariant->increment('stock_quantity', $item->quantity);
            }

            $order->update(['status_id' => 5]); // 5 = cancelled

            DB::commit();

            return redirect()->route('order.show', $order)
                ->with('success', 'Order cancelled successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel order');
        }
    }
}
