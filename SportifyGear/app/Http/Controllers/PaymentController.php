<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Show payment page
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status_id !== 0) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Payment not allowed.');
        }

        $order->load(['items.productVariant.product']);

        return view('payment.index', compact('order'));
    }

    // Process payment
    public function process(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cod,esewa,khalti',
        ]);

        $order = Order::with('items.productVariant')
            ->where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status_id', 0)
            ->firstOrFail();

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'method' => $request->payment_method,
            'amount' => $order->total,
            'currency' => 'NPR',
            'status' => 'pending',
        ]);

        switch ($request->payment_method) {

            case 'cod':
                DB::transaction(function () use ($order, $payment) {
                    $order->update(['status_id' => 1]); // processing
                });

                return redirect()->route('orders.success', $order)
                    ->with('success', 'Order placed successfully! Pay on delivery.');

            case 'esewa':
                return $this->initiateEsewa($order, $payment);

            case 'khalti':
                return $this->initiateKhalti($order, $payment);
        }
    }

    private function initiateEsewa($order, $payment)
    {
        session(['esewa_payment_id' => $payment->id]);

        $pid = 'ORDER_' . $order->id . '_' . time();

        $payment->update([
            'transaction_id' => $pid
        ]);

        $data = [
            'amt' => $order->total,
            'txAmt' => 0,
            'psc' => 0,
            'pdc' => $order->shipping_fee,
            'tAmt' => $order->total + $order->shipping_fee,
            'pid' => $pid,
            'scd' => 'EPAYTEST',
            'su' => route('payment.esewa.success'),
            'fu' => route('payment.esewa.failure'),
        ];

        return view('payment.esewa_redirect', compact('data'));
    }

    private function initiateKhalti($order, $payment)
    {
        $pid = 'KHALTI_' . $order->id . '_' . time();

        $payment->update([
            'transaction_id' => $pid
        ]);

        return view('payment.khalti_redirect', compact('order', 'payment'));
    }

    // eSewa Success
    public function esewaSuccess(Request $request)
    {
        $payment = Payment::with('order')
            ->where('transaction_id', $request->pid)
            ->first();

        if (!$payment) {
            return redirect()->route('home')->with('error', 'Invalid payment.');
        }

        DB::transaction(function () use ($payment, $request) {

            $payment->update([
                'reference_id' => $request->refId,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $order = $payment->order;

            $order->update(['status_id' => 1]);

            $this->reduceStock($order);
        });

        return redirect()->route('orders.success', $payment->order)
            ->with('success', 'Payment successful!');
    }

    // eSewa Failure
    public function esewaFailure(Request $request)
    {
        $payment = Payment::with('order')
            ->where('transaction_id', $request->pid)
            ->first();

        if (!$payment) {
            return redirect()->route('home')->with('error', 'Payment not found.');
        }

        $payment->update([
            'status' => 'failed'
        ]);

        return redirect()->route('orders.show', $payment->order)
            ->with('error', 'Payment failed. Try again.');
    }

    // Stock reduction
    private function reduceStock($order)
    {
        foreach ($order->items as $item) {
            if ($item->productVariant) {
                $item->productVariant->decrement('stock_quantity', $item->quantity);
            }
        }
    }
}
