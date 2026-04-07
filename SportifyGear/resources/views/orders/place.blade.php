<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Complete Your Order</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('orders.store') }}" id="directOrderForm">
                        @csrf
                        <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">

                        <!-- Address -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Shipping Address</h2>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Address</label>
                                <select name="address_id" required class="w-full px-3 py-2 border rounded-lg">
                                    <option value="">Choose an address</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->address_line1 }}, {{ $address->city }},
                                            {{ $address->state }} -
                                            {{ $address->pincode }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-center">
                                <a href="#" class="text-orange-600 text-sm">+ Add New Address</a>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Quantity</h2>
                            <div class="flex items-center gap-4">
                                <button type="button" onclick="changeQty(-1)"
                                    class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-xl font-bold">−</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                    max="{{ $variant->stock_quantity }}"
                                    class="w-20 text-center border rounded-lg py-2">
                                <button type="button" onclick="changeQty(1)"
                                    class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-xl font-bold">+</button>
                                <span class="text-gray-600 ml-2">({{ $variant->stock_quantity }} available)</span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Payment Method</h2>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer">
                                    <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                                    Cash on Delivery
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer">
                                    <input type="radio" name="payment_method" value="esewa" class="mr-3"> eSewa
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer">
                                    <input type="radio" name="payment_method" value="khalti" class="mr-3">
                                    Khalti
                                </label>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        <div class="flex gap-3 pb-3 border-b mb-3">
                            @php
                                $image =
                                    $variant->images->where('is_primary', true)->first() ?? $variant->images->first();
                            @endphp
                            <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden">
                                <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/64' }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $variant->name ?? 'Default' }}</p>
                                <p class="text-orange-600 font-bold">Rs. {{ number_format($price, 2) }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold" id="subtotalDisplay">Rs.
                                    {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span id="shippingDisplay">
                                    @if ($shipping > 0)
                                        Rs. {{ number_format($shipping, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-orange-600" id="totalDisplay">Rs.
                                        {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" form="directOrderForm"
                            class="w-full mt-6 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700 font-semibold">
                            Pay Now
                        </button>

                        <a href="{{ route('products.show', $product->slug) }}"
                            class="block text-center mt-3 text-gray-500 hover:text-gray-700 text-sm">
                            ← Back to Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const pricePerUnit = {{ $price }};
        const shippingFreeThreshold = 2000;
        const baseShipping = 100;

        function updateTotals() {
            let qty = parseInt(document.getElementById('quantity').value) || 1;
            let subtotal = pricePerUnit * qty;
            let shipping = subtotal > shippingFreeThreshold ? 0 : baseShipping;
            let total = subtotal + shipping;

            document.getElementById('subtotalDisplay').innerText = 'Rs. ' + subtotal.toFixed(2);
            document.getElementById('shippingDisplay').innerHTML = shipping ? 'Rs. ' + shipping.toFixed(2) : 'Free';
            document.getElementById('totalDisplay').innerText = 'Rs. ' + total.toFixed(2);
        }

        function changeQty(delta) {
            let input = document.getElementById('quantity');
            let newVal = parseInt(input.value) + delta;
            let max = parseInt(input.max);
            if (newVal >= 1 && newVal <= max) {
                input.value = newVal;
                updateTotals();
            }
        }

        document.getElementById('quantity').addEventListener('input', updateTotals);
    </script>
</x-frontend.layout>
