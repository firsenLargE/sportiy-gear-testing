<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden text-center">
                <!-- Success Icon -->
                <div class="bg-green-50 p-8">
                    <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mt-4">Order Placed Successfully!</h1>
                    <p class="text-gray-600 mt-2">Thank you for your purchase</p>
                </div>

                <!-- Order Details -->
                <div class="p-8">
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Order Date:</span>
                            <span class="text-gray-900">{{ $order->created_at->format('F j, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="text-xl font-bold text-orange-600">Rs.
                                {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Order Items Summary -->
                    <div class="text-left mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Order Items:</h3>
                        <div class="space-y-2">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                    <span>Rs. {{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shipping Info (fixed for province/district) -->
                    <div class="text-left bg-blue-50 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Shipping Address:</h3>
                        <p class="text-sm text-gray-600">
                            {{ $order->address->address_line1 }}
                            @if ($order->address->address_line2), {{ $order->address->address_line2 }} @endif
                            , {{ $order->address->district->name ?? '' }}, {{ $order->address->province->name ?? '' }}
                            @if ($order->address->nearest_landmark)
                                <br>(Near: {{ $order->address->nearest_landmark }})
                            @endif
                        </p>
                    </div>

                    <!-- Action Buttons (fixed route name) -->
                    <div class="flex gap-3">
                        <a href="{{ route('orders.show', $order) }}"
                            class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition">
                            View Order Details
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="flex-1 border border-orange-600 text-orange-600 py-2 rounded-lg hover:bg-orange-50 transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Confirmation Message -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-500">
                    A confirmation email has been sent to your registered email address.
                </p>
            </div>
        </div>
    </div>
</x-frontend.layout>