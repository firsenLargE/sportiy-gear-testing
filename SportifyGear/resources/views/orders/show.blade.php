<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                <a href="{{ route('orders.my') }}" class="text-orange-600 hover:text-orange-700">
                    ← Back to My Orders
                </a>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <div class="flex flex-wrap justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Order #{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('F j, Y') }}</p>
                        </div>
                        <div>
                            <span
                                class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                                @if ($order->status_id == 1) bg-yellow-100 text-yellow-800
                                @elseif($order->status_id == 2) bg-blue-100 text-blue-800
                                @elseif($order->status_id == 3) bg-green-100 text-green-800
                                @elseif($order->status_id == 4) bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->status->name ?? 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800 mb-3">Order Timeline</h3>
                    <div class="relative">
                        <div class="flex justify-between">
                            <div class="text-center flex-1">
                                <div
                                    class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium">Order Placed</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('M d') }}</p>
                            </div>
                            <div class="text-center flex-1">
                                <div
                                    class="w-8 h-8 {{ $order->status_id >= 2 ? 'bg-orange-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium">Confirmed</p>
                            </div>
                            <div class="text-center flex-1">
                                <div
                                    class="w-8 h-8 {{ $order->status_id >= 3 ? 'bg-orange-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium">Shipped</p>
                            </div>
                            <div class="text-center flex-1">
                                <div
                                    class="w-8 h-8 {{ $order->status_id >= 4 ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium">Delivered</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-800">Order Items</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach ($order->items as $item)
                        <div class="p-6">
                            <div class="flex gap-4">
                                @php
                                    $variant = $item->productVariant;
                                    $product = $item->product;
                                    $image = $product->images->where('is_primary', true)->first();
                                    if (!$image && $variant && $variant->images->isNotEmpty()) {
                                        $image =
                                            $variant->images->where('is_primary', true)->first() ??
                                            $variant->images->first();
                                    }
                                @endphp
                                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/96' }}"
                                        alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-wrap justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                            @if ($variant && $variant->name)
                                                <p class="text-sm text-gray-500">Variant: {{ $variant->name }}</p>
                                            @endif
                                            @if ($variant && $variant->attributeValues->isNotEmpty())
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @foreach ($variant->attributeValues as $attr)
                                                        {{ $attr->attribute->name }}: {{ $attr->value }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-600">Qty: {{ $item->quantity }}</p>
                                            <p class="font-semibold text-orange-600">Rs.
                                                {{ number_format($item->price, 2) }}</p>
                                            <p class="text-sm text-gray-500">Total: Rs.
                                                {{ number_format($item->price * $item->quantity, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary & Shipping Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Information (fixed for province/district) -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="font-semibold text-gray-800">Shipping Information</h3>
                    </div>
                    <div class="p-6">
                        <p class="font-medium text-gray-800">{{ $order->address->name ?? Auth::user()->name }}</p>
                        <p class="text-gray-600 text-sm mt-1">{{ $order->address->phone_no ?? '' }}</p>
                        <p class="text-gray-600 text-sm mt-2">
                            {{ $order->address->address_line1 }}
                            @if ($order->address->address_line2), {{ $order->address->address_line2 }} @endif
                            , {{ $order->address->district->name ?? '' }}, {{ $order->address->province->name ?? '' }}
                        </p>
                        @if ($order->address->nearest_landmark)
                            <p class="text-gray-600 text-sm">Near: {{ $order->address->nearest_landmark }}</p>
                        @endif
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="font-semibold text-gray-800">Payment Summary</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>Rs. {{ number_format($order->sub_total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping Fee</span>
                                <span>
                                    @if ($order->shipping_fee > 0)
                                        Rs. {{ number_format($order->shipping_fee, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            @if ($order->coupon)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Coupon Discount</span>
                                    <span class="text-green-600">
                                        -Rs.
                                        {{ number_format($order->sub_total + $order->shipping_fee - $order->total, 2) }}
                                    </span>
                                </div>
                            @endif
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-800">Total Paid</span>
                                    <span class="text-xl font-bold text-orange-600">Rs.
                                        {{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        @if (in_array($order->status_id, [1, 2]))
                            <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-6"
                                onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Need Help -->
            <div class="bg-blue-50 rounded-xl p-6 mt-6 text-center">
                <h3 class="font-semibold text-gray-800 mb-2">Need Help?</h3>
                <p class="text-gray-600 text-sm mb-3">Have questions about your order? Contact our support team</p>
                <a href="{{ route('contact.index') }}"
                    class="text-orange-600 hover:text-orange-700 font-medium">Contact Support →</a>
            </div>
        </div>
    </div>
</x-frontend.layout>