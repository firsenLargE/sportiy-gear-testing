<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
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
    // ==================== CART-BASED CHECKOUT (STEP 1) ====================
    public function prepareOrder(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:cart_items,id',
        ]);

        $address = Address::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cartItems = CartItem::with('variant.discounts')
            ->whereIn('id', $request->selected_items)
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'No valid items selected.');
        }

        // Stock validation (no reduction yet)
        foreach ($cartItems as $item) {
            if ($item->variant->stock_quantity < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->variant->product->name}");
            }
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $this->getFinalPrice($item->variant) * $item->quantity;
        });
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'         => Auth::id(),
                'address_id'      => $address->id,
                'shipping_fee'    => $shipping,
                'status_id'       => 1, // Pending (awaiting payment)
                'order_number'    => 'ORD-' . strtoupper(uniqid()),
                'discount_amount' => 0,
                'sub_total'       => $subtotal,
                'total'           => $total,
            ]);

            foreach ($cartItems as $item) {
                $price = $this->getFinalPrice($item->variant);
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item->variant->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity'           => $item->quantity,
                    'price'              => $price,
                ]);
            }

            DB::commit();
            return redirect()->route('payment.show', $order)
                ->with('success', 'Please complete your payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to prepare order. Please try again.');
        }
    }

    // ==================== DIRECT ORDER FORM (GET) ====================
    public function directOrderForm($productId, $variantId = null)
    {
        $product = Product::findOrFail($productId);
        if (!$variantId) {
            $variant = $product->variants()->first();
        } else {
            $variant = $product->variants()->findOrFail($variantId);
        }

        $price = $this->getFinalPrice($variant);
        $subtotal = $price;
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;
        $addresses = Address::where('user_id', Auth::id())->get();

        return view('orders.place', compact('product', 'variant', 'price', 'subtotal', 'shipping', 'total', 'addresses'));
    }

    // ==================== PLACE ORDER (DIRECT) ====================
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id'         => 'required|exists:addresses,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1',
        ]);

        $address = Address::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $variant = ProductVariant::with('discounts')->findOrFail($request->product_variant_id);
        $quantity = $request->quantity;

        if ($variant->stock_quantity < $quantity) {
            return back()->with('error', "Insufficient stock. Only {$variant->stock_quantity} left.");
        }

        $price = $this->getFinalPrice($variant);
        $subtotal = $price * $quantity;
        $shipping = $subtotal > 2000 ? 0 : 100;
        $total = $subtotal + $shipping;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'         => Auth::id(),
                'address_id'      => $address->id,
                'shipping_fee'    => $shipping,
                'status_id'       => 1, // Pending
                'order_number'    => 'ORD-' . strtoupper(uniqid()),
                'discount_amount' => 0,
                'sub_total'       => $subtotal,
                'total'           => $total,
            ]);

            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity'           => $quantity,
                'price'              => $price,
            ]);

            DB::commit();
            return redirect()->route('payment.show', $order)
                ->with('success', 'Please complete your payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    // ==================== SUCCESS PAGE ====================
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['items.productVariant.product', 'address', 'status']);
        return view('orders.success', compact('order'));
    }

    // ==================== MY ORDERS LIST ====================
    public function myOrders()
    {
        $orders = Order::with(['status', 'items.productVariant.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    // ==================== ORDER DETAILS ====================
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['items.productVariant.product', 'address', 'status', 'coupon']);
        return view('orders.show', compact('order'));
    }

    // ==================== CANCEL ORDER ====================
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        // Only allow cancellation if status is Pending(1) or Confirmed(2)
        if (!in_array($order->status_id, [1, 2])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                $item->productVariant->increment('stock_quantity', $item->quantity);
            }
            $order->update(['status_id' => 6]); // Cancelled
            DB::commit();
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    // ==================== HELPER: Get final price after discount ====================
    private function getFinalPrice($variant)
    {
        $price = $variant->price ?? 0;
        if ($variant->discounts->isNotEmpty()) {
            $discount = $variant->discounts->first();
            $now = now();
            if ($discount->start_date && $now->lt($discount->start_date)) return $price;
            if ($discount->end_date && $now->gt($discount->end_date)) return $price;

            if ($discount->discount_type === 'percentage') {
                $price -= ($price * $discount->discount_value) / 100;
            } else {
                $price -= $discount->discount_value;
            }
        }
        return max(0, round($price, 2));
    }
}
