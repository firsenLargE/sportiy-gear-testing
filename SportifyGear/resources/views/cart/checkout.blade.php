<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('orders.store') }}" id="checkoutForm">
                        @csrf
                        @foreach ($selectedItems as $item)
                            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
                        @endforeach
                        <!-- Address Section -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Shipping Address</h2>
                            <div class="mb-4">
                                <label for="address_id" class="block text-sm font-medium text-gray-700 mb-2">Select
                                    Address</label>
                                <select name="address_id" id="address_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Select an address</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->address_line1 }}, {{ $address->city }}, {{ $address->state }} -
                                            {{ $address->pincode }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-center">
                                <a href="#" class="text-orange-600 hover:text-orange-700 text-sm">+ Add New
                                    Address</a>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Payment Method</h2>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                                    <div>
                                        <p class="font-medium">Cash on Delivery</p>
                                        <p class="text-sm text-gray-500">Pay when you receive the order</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="esewa" class="mr-3">
                                    <div>
                                        <p class="font-medium">eSewa</p>
                                        <p class="text-sm text-gray-500">Pay via eSewa wallet</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="khalti" class="mr-3">
                                    <div>
                                        <p class="font-medium">Khalti</p>
                                        <p class="text-sm text-gray-500">Pay via Khalti wallet</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Order Summary -->

                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>

                    <div class="space-y-3 max-h-96 overflow-y-auto mb-4">
                        @forelse ($selectedItems ?? [] as $item)
                            @php
                                $price = $item->final_price ?? ($item->variant->price ?? 0);
                            @endphp
                            <div class="flex gap-3 pb-3 border-b">
                                <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                    @php
                                        $image =
                                            $item->variant->images->where('is_primary', true)->first() ??
                                            $item->variant->images->first();
                                    @endphp
                                    <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/64' }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">{{ $item->variant->product->name }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    <p class="text-orange-600 font-bold text-sm">
                                        Rs. {{ number_format($price * $item->quantity, 2) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-red-600 text-center py-4">No items selected. <a
                                    href="{{ route('cart.index') }}" class="underline">Go back</a></p>
                        @endforelse
                    </div>

                    {{-- subtotal, shipping, total --}}
                    <div class="space-y-2 pt-3 border-t">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">Rs. {{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span
                                class="font-semibold">{{ ($shipping ?? 0) > 0 ? 'Rs. ' . number_format($shipping, 2) : 'Free' }}</span>
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-gray-800">Total</span>
                                <span class="text-xl font-bold text-orange-600">Rs.
                                    {{ number_format($total ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" form="checkoutForm"
                        class="w-full mt-6 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700">
                        Pay Now
                    </button>
                    <a href="{{ route('cart.index') }}"
                        class="block text-center mt-3 text-gray-500 hover:text-gray-700 text-sm">
                        ← Back to Cart
                    </a>
                </div>

            </div>
        </div>
    </div>
    </div>
</x-frontend.layout>
