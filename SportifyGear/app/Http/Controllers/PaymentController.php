<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status_id !== 1) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Payment cannot be processed.');
        }
        $order->load(['items.productVariant.product']);
        return view('payment.index', compact('order'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cod,esewa,khalti',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status_id', 1)
            ->firstOrFail();

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'method' => $request->payment_method,
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        if ($request->payment_method == 'cod') {
            DB::transaction(function () use ($order, $payment) {
                $order->update(['status_id' => 2]); // Confirmed
                foreach ($order->items as $item) {
                    $item->productVariant->decrement('stock_quantity', $item->quantity);
                }
                $cart = Cart::where('user_id', $order->user_id)->first();
                if ($cart) {
                    $variantIds = $order->items->pluck('product_variant_id')->toArray();
                    $cart->items()->whereIn('product_variant_id', $variantIds)->delete();
                }
                $payment->update(['status' => 'paid', 'paid_at' => now()]);
            });
            return redirect()->route('orders.success', $order)->with('success', 'Order placed!');
        }

        // For eSewa/Khalti – placeholder (you can integrate later)
        return redirect()->route('orders.success', $order)->with('info', 'Payment gateway not yet integrated.');
    }
}
