<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Complete Your Payment</h1>

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
                    {{ session('info') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Payment Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Select Payment Method</h2>

                        <form method="POST" action="{{ route('payment.process') }}" id="paymentForm">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            {{-- Payment Methods --}}
                            <div class="space-y-4">
                                {{-- Cash on Delivery --}}
                                <label
                                    class="block border rounded-lg p-4 cursor-pointer hover:border-orange-500 transition 
                                    {{ old('payment_method', 'cod') == 'cod' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="cod"
                                            {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}
                                            class="w-4 h-4 text-orange-600 focus:ring-orange-500">
                                        <div class="ml-4 flex items-center">
                                            <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <span class="ml-3 font-semibold text-gray-800">Cash on Delivery</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2 ml-8">Pay with cash when your order arrives.
                                    </p>
                                </label>

                                {{-- Khalti --}}
                                <label
                                    class="block border rounded-lg p-4 cursor-pointer hover:border-orange-500 transition 
                                    {{ old('payment_method') == 'khalti' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="khalti"
                                            {{ old('payment_method') == 'khalti' ? 'checked' : '' }}
                                            class="w-4 h-4 text-orange-600 focus:ring-orange-500">
                                        <div class="ml-4 flex items-center">
                                            <img src="/khalti.png" alt="Khalti" class="h-8 w-auto">
                                            <span class="ml-3 font-semibold text-gray-800">Khalti</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2 ml-8">Pay using Khalti digital wallet.</p>
                                </label>
                            </div>

                            @error('payment_method')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror

                            <button type="submit" id="payButton"
                                class="w-full mt-8 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700 transition font-semibold">
                                Pay Rs. {{ number_format($order->total, 2) }}
                            </button>
                        </form>

                        <p class="text-xs text-gray-400 text-center mt-4">
                            By placing your order, you agree to our Terms of Service and Privacy Policy.
                        </p>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>
                        <p class="text-sm text-gray-500 mb-3">Order #{{ $order->order_number }}</p>

                        <div class="space-y-3 max-h-80 overflow-y-auto mb-4">
                            @foreach ($order->items as $item)
                                @php
                                    $product = $item->productVariant->product ?? $item->product;
                                    $variant = $item->productVariant;
                                    $image =
                                        $product->images->where('is_primary', true)->first() ??
                                        $product->images->first();
                                @endphp
                                <div class="flex gap-3 pb-3 border-b">
                                    <div class="w-14 h-14 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                        <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/56' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm">{{ $product->name }}</p>
                                        @if ($variant->name)
                                            <p class="text-xs text-gray-500">{{ $variant->name }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                        <p class="text-orange-600 font-bold text-sm">
                                            Rs. {{ number_format($item->price * $item->quantity, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-2 pt-3 border-t">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rs. {{ number_format($order->sub_total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-semibold">
                                    {{ $order->shipping_fee > 0 ? 'Rs. ' . number_format($order->shipping_fee, 2) : 'Free' }}
                                </span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-gray-800">Total</span>
                                    <span class="text-xl font-bold text-orange-600">
                                        Rs. {{ number_format($order->total, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-sm text-gray-600 border-t pt-4">
                            <p class="font-medium mb-1">Shipping Address:</p>
                            <p>{{ $order->address->name }}</p>
                            <p>{{ $order->address->address_line1 }}
                                @if ($order->address->address_line2)
                                    , {{ $order->address->address_line2 }}
                                @endif
                            </p>
                            <p>{{ $order->address->district?->name }}, {{ $order->address->province?->name }}</p>
                            <p>Phone: {{ $order->address->phone_no }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Prevent double submission and show Khalti-specific message --}}
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('payButton');
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (btn.disabled) {
                e.preventDefault();
                return false;
            }

            btn.disabled = true;

            // Change button text based on payment method
            if (selectedMethod === 'khalti') {
                btn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Redirecting to Khalti...
                `;
            } else {
                btn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
            }
        });
    </script>
</x-frontend.layout>
