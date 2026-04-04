<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

            @if ($orders->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-2">No orders yet</h2>
                    <p class="text-gray-500 mb-6">You haven't placed any orders yet.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                        Start Shopping
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden order-item">
                            <div class="border-b border-gray-200 p-6">
                                <div class="flex flex-wrap justify-between items-start gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Order #{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500">Placed on
                                            {{ $order->created_at->format('F j, Y') }}</p>
                                    </div>
                                    <div>
                                        <span
                                            class="inline-block px-3 py-1 text-xs font-semibold rounded-full 
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
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/80' }}"
                                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                                @if ($variant && $variant->name)
                                                    <p class="text-sm text-gray-500">Variant: {{ $variant->name }}</p>
                                                @endif
                                                <div class="flex justify-between items-center mt-2">
                                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                                    <p class="font-semibold text-orange-600">Rs.
                                                        {{ number_format($item->price * $item->quantity, 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="bg-gray-50 p-6">
                                <div class="flex flex-wrap justify-between items-center">
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Total:</span> Rs.
                                            {{ number_format($order->total, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Shipping:</span>
                                            @if ($order->shipping_fee > 0)
                                                Rs. {{ number_format($order->shipping_fee, 2) }}
                                            @else
                                                Free
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex gap-3 mt-4 sm:mt-0">
                                        <a href="{{ route('order.show', $order) }}"
                                            class="px-4 py-2 border border-orange-600 text-orange-600 rounded-lg hover:bg-orange-50 transition">
                                            View Details
                                        </a>
                                        @if (in_array($order->status_id, [1, 2]))
                                            <form method="POST" action="{{ route('order.cancel', $order) }}"
                                                class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                                    onclick="return confirm('Are you sure you want to cancel this order?')">
                                                    Cancel Order
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-frontend.layout>
