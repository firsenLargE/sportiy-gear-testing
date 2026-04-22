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
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ==================== CART-BASED CHECKOUT (STEP 1) ====================
    public function prepare(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'exists:cart_items,id',
            'shipping_fee'   => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $cart = $user->cart;
        if (!$cart) {
            return back()->with('error', 'Your cart is empty.');
        }

        $cartItems = CartItem::with(['variant.discounts', 'variant.product'])
            ->where('cart_id', $cart->id)
            ->whereIn('id', $request->selected_items)
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'No valid items selected from your cart.');
        }

        // Stock validation
        foreach ($cartItems as $item) {
            if ($item->variant->stock_quantity < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->variant->product->name}");
            }
        }

        // Calculate subtotal using final price after discounts
        $subtotal = $cartItems->sum(function ($item) {
            return $this->getFinalPrice($item->variant) * $item->quantity;
        });

        // Use the shipping fee submitted from the frontend (dynamic based on address)
        $shippingFee = (float) $request->input('shipping_fee', 0);
        $total = $subtotal + $shippingFee;

        DB::beginTransaction();
        try {
            // Ensure status ID 1 (Pending) exists
            if (!Status::find(1)) {
                throw new \Exception('Order status "Pending" not found. Please run the StatusSeeder.');
            }

            $order = Order::create([
                'user_id'         => $user->id,
                'address_id'      => $address->id,
                'shipping_fee'    => $shippingFee,
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
                    'product_name'       => $item->variant->product->name, // ✅ Populate product_name
                    'product_variant_id' => $item->product_variant_id,
                    'quantity'           => $item->quantity,
                    'price'              => $price,
                ]);
            }

            // Remove the selected items from the cart
            CartItem::whereIn('id', $request->selected_items)->delete();

            DB::commit();

            return redirect()->route('payment.show', $order)
                ->with('success', 'Order created. Please choose a payment method.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order preparation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'selected_items' => $request->selected_items,
                'trace' => $e->getTraceAsString()
            ]);
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

        $variant = ProductVariant::with('discounts', 'product')->findOrFail($request->product_variant_id);
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
            // Ensure status ID 1 exists
            if (!Status::find(1)) {
                throw new \Exception('Order status "Pending" not found.');
            }

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
                'product_name'       => $variant->product->name,
                'product_variant_id' => $variant->id,
                'quantity'           => $quantity,
                'price'              => $price,
            ]);

            DB::commit();

            return redirect()->route('payment.show', $order)
                ->with('success', 'Please complete your payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Direct order placement failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['items.productVariant.product', 'address', 'status']);
        return view('orders.success', compact('order'));
    }


    public function myOrders()
    {
        $orders = Order::with(['status', 'items.productVariant.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }


    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $order->load(['items.productVariant.product', 'address', 'status', 'coupon']);
        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if (!in_array($order->status_id, [1, 2])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                $item->productVariant->increment('stock_quantity', $item->quantity);
            }
            $order->update(['status_id' => 6]);
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
