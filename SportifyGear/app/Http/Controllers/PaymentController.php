<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status_id !== 1) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be paid anymore.');
        }
        if (Payment::where('order_id', $order->id)->exists()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'A payment already exists for this order.');
        }
        $order->load(['items.productVariant.product']);
        return view('payment.index', compact('order'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'order_id'        => 'required|exists:orders,id',
            'payment_method'  => 'required|in:cod,khalti',
        ]);

        $user = Auth::user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('status_id', 1)
            ->lockForUpdate()
            ->first();

        if (!$order) {
            return redirect()->route('orders.my')
                ->with('error', 'Order not found or already processed.');
        }

        try {
            $paymentMethod = $request->payment_method;

            // Use Eloquent transaction
            DB::transaction(function () use ($order, $user, $paymentMethod) {
                // Check again for existing payment
                if (Payment::where('order_id', $order->id)->lockForUpdate()->exists()) {
                    throw new \Exception('Payment already exists for this order.');
                }

                Payment::create([
                    'order_id' => $order->id,
                    'user_id'  => $user->id,
                    'method'   => $paymentMethod,
                    'amount'   => $order->total,
                    'status'   => $paymentMethod === 'cod' ? 'paid' : 'pending',
                    'paid_at'  => $paymentMethod === 'cod' ? now() : null,
                ]);

                if ($paymentMethod === 'cod') {
                    $this->finalizeOrder($order);
                }
            });

            if ($paymentMethod === 'cod') {
                return redirect()->route('orders.success', $order)
                    ->with('success', 'Order placed successfully with Cash on Delivery!');
            }

            return redirect()->route('payment.khalti', $order)
                ->with('info', 'Redirecting to Khalti...');
        } catch (\Exception $e) {
            Log::error('Payment process error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function khaltiInitiate(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status_id !== 1) {
            abort(403);
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('method', 'khalti')
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            return redirect()->route('payment.show', $order)
                ->with('error', 'Payment record not found. Please try again.');
        }

        $amountInPaisa = (int) ($order->total * 100);
        $secretKey = config('services.khalti.secret_key');
        $initiateUrl = config('services.khalti.initiate_url');

        try {
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->withHeaders(['Authorization' => 'Key ' . $secretKey])
                ->post($initiateUrl, [
                    'return_url' => route('payment.khalti.callback', $order->id),
                    'website_url' => route('home'),
                    'amount' => $amountInPaisa,
                    'purchase_order_id' => (string) $order->order_number,
                    'purchase_order_name' => "Order #{$order->order_number}",
                    'customer_info' => [
                        'name'  => $order->address->name ?? Auth::user()->name,
                        'email' => Auth::user()->email,
                        'phone' => $order->address->phone_no ?? '',
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $payment->transaction_id = $data['pidx'] ?? null;
                $payment->save();

                $paymentUrl = $data['payment_url'] ?? null;
                if ($paymentUrl) {
                    return redirect()->away($paymentUrl);
                }
            }

            Log::error('Khalti initiation failed', ['response' => $response->body()]);
            return redirect()->route('payment.show', $order)
                ->with('error', 'Khalti gateway error. Please try again.');
        } catch (\Exception $e) {
            Log::error('Khalti initiation exception: ' . $e->getMessage());
            return redirect()->route('payment.show', $order)
                ->with('error', 'Could not connect to Khalti. Check your internet.');
        }
    }

    public function khaltiCallback(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $status = $request->query('status');
        $pidx = $request->query('pidx');

        Log::info('Khalti callback', ['order_id' => $orderId, 'status' => $status]);

        if ($status !== 'Completed') {
            return redirect()->route('payment.show', $order)
                ->with('error', 'Payment cancelled or failed. Status: ' . $status);
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('method', 'khalti')
            ->first();

        if (!$payment) {
            return redirect()->route('orders.my')->with('error', 'Payment record not found.');
        }

        $secretKey = config('services.khalti.secret_key');
        $lookupUrl = config('services.khalti.lookup_url');

        try {
            $lookup = Http::timeout(30)
                ->retry(3, 100)
                ->withHeaders(['Authorization' => 'Key ' . $secretKey])
                ->post($lookupUrl, ['pidx' => $pidx]);

            if ($lookup->successful()) {
                $data = $lookup->json();
                if (($data['status'] ?? '') === 'Completed') {
                    DB::transaction(function () use ($payment, $order, $data, $pidx) {
                        $payment->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'transaction_id' => $data['transaction_id'] ?? $pidx,
                            'payment_details' => $data,
                        ]);
                        $this->finalizeOrder($order);
                    });

                    return redirect()->route('orders.success', $order)
                        ->with('success', 'Payment successful! Your order is confirmed.');
                }
            }

            Log::error('Khalti lookup failed', ['response' => $lookup->body()]);
            return redirect()->route('payment.show', $order)
                ->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            Log::error('Khalti callback exception: ' . $e->getMessage());
            return redirect()->route('payment.show', $order)
                ->with('error', 'Verification error.');
        }
    }


    private function finalizeOrder(Order $order)
    {

        $order->update(['status_id' => 2]);

        foreach ($order->items as $item) {
            $item->productVariant->decrement('stock_quantity', $item->quantity);
        }

        $cart = Cart::where('user_id', $order->user_id)->first();
        if ($cart) {
            $variantIds = $order->items->pluck('product_variant_id')->toArray();
            $cart->items()->whereIn('product_variant_id', $variantIds)->delete();
        }
    }
}
