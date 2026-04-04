<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>

            @if ($wishlistItems->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-2">Your wishlist is empty</h2>
                    <p class="text-gray-500 mb-6">Save your favorite items here to buy them later.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                        Start Shopping
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($wishlistItems as $item)
                        @php
                            $product = $item->product;
                            $variant = $product->variants->first();
                            $price = $variant->price ?? 0;
                            $originalPrice = $price;
                            $discountLabel = null;

                            if ($variant && $variant->discounts->isNotEmpty()) {
                                $discount = $variant->discounts->first();
                                if ($discount->discount_type === 'percentage') {
                                    $price -= ($variant->price * $discount->discount_value) / 100;
                                    $discountLabel = '-' . $discount->discount_value . '%';
                                } else {
                                    $price -= $discount->discount_value;
                                    $discountLabel = '-Rs ' . number_format($discount->discount_value);
                                }
                            }

                            $image = $product->images->where('is_primary', true)->first();
                            if (!$image && $variant && $variant->images->isNotEmpty()) {
                                $image =
                                    $variant->images->where('is_primary', true)->first() ?? $variant->images->first();
                            }
                        @endphp
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group wishlist-item"
                            data-product-id="{{ $product->id }}">
                            <div class="relative overflow-hidden bg-gray-100 aspect-square">
                                <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/300' }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @if ($discountLabel)
                                    <span
                                        class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $discountLabel }}</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 line-clamp-2 mb-2">{{ $product->name }}</h3>
                                <div class="flex items-baseline gap-2 mb-3">
                                    <span class="text-orange-600 font-bold">Rs. {{ number_format($price, 2) }}</span>
                                    @if ($price < $originalPrice)
                                        <span class="text-gray-400 line-through text-sm">Rs.
                                            {{ number_format($originalPrice, 2) }}</span>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        class="add-to-cart flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition text-sm"
                                        data-variant-id="{{ $variant->id ?? '' }}">
                                        Add to Cart
                                    </button>
                                    <button
                                        class="remove-from-wishlist p-2 border border-red-300 text-red-500 rounded-lg hover:bg-red-50 transition"
                                        data-product-id="{{ $product->id }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        // Add to Cart from Wishlist
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                const variantId = this.dataset.variantId;
                if (!variantId) {
                    alert('Product variant not available');
                    return;
                }

                fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            variant_id: variantId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.updateCartCountExternal?.(data.cart_count);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Remove from Wishlist
        document.querySelectorAll('.remove-from-wishlist').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                if (confirm('Remove this item from wishlist?')) {
                    fetch(`/wishlist/remove/${productId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Failed to remove item');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
</x-frontend.layout>
