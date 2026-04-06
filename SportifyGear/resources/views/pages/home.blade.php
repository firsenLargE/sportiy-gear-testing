<x-frontend.layout>

    <section class="py-12 bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-5">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div class="w-full text-center">
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                        Latest Products
                    </h1>

                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($products as $product)
                    @php
                        $variant = $product->variants->count() ? $product->variants->random() : null;

                        $originalPrice = 0;
                        $finalPrice = 0;
                        $discountLabel = null;

                        $variantImage = $variant
                            ? $variant->primary_image ??
                                ($variant->images->first()
                                    ? asset('storage/' . $variant->images->first()->image_path)
                                    : $product->display_image)
                            : $product->display_image;

                        if ($variant) {
                            $originalPrice = $variant->price;
                            $finalPrice = $originalPrice;

                            if ($variant->discounts->isNotEmpty()) {
                                $discount = $variant->discounts->first();

                                if ($discount->discount_type === 'percentage') {
                                    $finalPrice -= ($originalPrice * $discount->discount_value) / 100;
                                    $discountLabel = '-' . $discount->discount_value . '%';
                                } else {
                                    $finalPrice -= $discount->discount_value;
                                    $discountLabel = '-Rs ' . number_format($discount->discount_value);
                                }
                            }
                        }
                    @endphp

                    <a href="{{ route('products.show', $product->slug) }}"
                        class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-100 transform hover:-translate-y-0.5 flex flex-col relative">

                        <!-- Discount Badge -->
                        @if ($discountLabel)
                            <div
                                class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md z-10">
                                {{ $discountLabel }}
                            </div>
                        @endif

                        <!-- Product Image -->
                        <div
                            class="relative bg-gray-100 h-40 sm:h-56 flex items-center justify-center overflow-hidden p-2">
                            <img src="{{ $variantImage }}"
                                class="max-h-full max-w-full object-contain transition-transform duration-500 group-hover:scale-110">
                        </div>

                        <!-- Product Details -->
                        <div class="p-3 sm:p-4">
                            @if ($product->category)
                                <div class="text-xs text-orange-500 font-medium mb-1 uppercase tracking-wide">
                                    {{ $product->category->name }}
                                </div>
                            @endif

                            <h3 class="font-bold text-gray-800 line-clamp-2 group-hover:text-orange-600 transition">
                                {{ $product->name }}
                            </h3>

                            @if ($variant && $variant->description)
                                <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">
                                    {!! Str::limit(strip_tags($variant->description), 120) !!}
                                </p>
                            @endif

                            @if ($variant)
                                <div class="mt-3 flex items-baseline gap-2 flex-wrap">
                                    <span class="text-xl font-bold text-orange-600">
                                        Rs. {{ number_format($finalPrice, 2) }}
                                    </span>
                                    @if ($finalPrice < $originalPrice)
                                        <span class="text-gray-400 line-through text-sm">
                                            Rs. {{ number_format($originalPrice, 2) }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Rating & Stock -->
                            <div class="mt-3 flex items-center justify-between">
                                @if ($product->reviews_count > 0)
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex items-center gap-0.5">
                                            @php $rating = round($product->reviews_avg_rating * 2) / 2; @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($rating >= $i)
                                                    <svg class="w-3.5 h-3.5 text-yellow-400 fill-current"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                    </svg>
                                                @elseif ($rating + 0.5 == $i)
                                                    <svg class="w-3.5 h-3.5 text-yellow-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <defs>
                                                            <linearGradient
                                                                id="half-grad-{{ $product->id }}-{{ $i }}">
                                                                <stop offset="50%" stop-color="#FBBF24" />
                                                                <stop offset="50%" stop-color="#E5E7EB" />
                                                            </linearGradient>
                                                        </defs>
                                                        <path
                                                            fill="url(#half-grad-{{ $product->id }}-{{ $i }})"
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-gray-400 text-xs">({{ $product->reviews_count }})</span>
                                    </div>
                                @else
                                    <div></div>
                                @endif

                                <!-- Stock -->
                                @if ($variant && $variant->stock_quantity > 0)
                                    <div class="flex items-center gap-1 text-xs text-green-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>{{ $variant->stock_quantity }} in stock</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1 text-xs text-red-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Out of Stock</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Remove pagination since it's a collection -->
        </div>
    </section>

</x-frontend.layout>
