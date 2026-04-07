<x-frontend.layout>
    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm text-gray-500">
                <a href="/" class="hover:text-orange-600">Home</a>
                <span class="mx-2">/</span>
                @if ($product->categories->isNotEmpty())
                    <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
                        class="hover:text-orange-600">
                        {{ $product->categories->first()->name }}
                    </a>
                    <span class="mx-2">/</span>
                @endif
                <span class="text-gray-900">{{ $product->name }}</span>
            </nav>

            <!-- Product Detail -->
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

                <!-- Left: Images Gallery -->
                <div class="lg:w-1/2">
                    @php
                        $allProductImages = $product->images->where('image_path', '!=', null);
                        $allVariantImages = collect();
                        foreach ($product->variants as $variant) {
                            if ($variant->images && $variant->images->isNotEmpty()) {
                                $allVariantImages = $allVariantImages->merge($variant->images);
                            }
                        }
                        $allImages = $allProductImages->merge($allVariantImages)->unique('image_path');
                        $primaryImage = $allImages->where('is_primary', true)->first() ?? $allImages->first();
                    @endphp

                    <!-- Main Image -->
                    <div
                        class="w-full h-[500px] bg-gray-100 flex items-center justify-center overflow-hidden rounded-xl">
                        <img id="mainProductImage"
                            src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_path) : $product->display_image ?? 'https://via.placeholder.com/600x600?text=No+Image' }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-contain transition-transform duration-500">
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if ($allImages->isNotEmpty() && $allImages->count() > 1)
                        <div class="grid grid-cols-5 gap-3">
                            @foreach ($allImages as $index => $image)
                                <button onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}', this)"
                                    class="thumbnail-btn relative rounded-lg overflow-hidden border-2 {{ $loop->first ? 'border-orange-500' : 'border-transparent' }} hover:border-orange-300 transition-all focus:outline-none">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ $product->name }} thumbnail" class="w-full h-24 object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right: Product Details -->
                <div class="lg:w-1/2">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>

                    @if ($product->categories->isNotEmpty())
                        <div class="mb-3">
                            <span
                                class="inline-block bg-orange-100 text-orange-700 text-sm font-semibold px-3 py-1 rounded-full">
                                {{ $product->categories->pluck('name')->join(', ') }}
                            </span>
                        </div>
                    @endif

                    @if ($product->reviews_count > 0)
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center gap-1">
                                @php $rating = round($product->reviews_avg_rating * 2) / 2; @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($rating >= $i)
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                        </svg>
                                    @elseif($rating + 0.5 == $i)
                                        <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient id="half-grad-{{ $product->id }}">
                                                    <stop offset="50%" stop-color="#FBBF24" />
                                                    <stop offset="50%" stop-color="#E5E7EB" />
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#half-grad-{{ $product->id }})"
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span
                                class="text-gray-600 text-sm font-medium">{{ number_format($product->reviews_avg_rating, 1) }}
                                out of 5</span>
                            <span class="text-gray-400">|</span>
                            <span class="text-gray-600 text-sm">{{ $product->reviews_count }} reviews</span>
                        </div>
                    @endif

                    <!-- Price -->
                    @php
                        $selectedVariant = $product->variants->first();
                        $finalPrice = $selectedVariant->price ?? 0;
                        $originalPrice = $finalPrice;
                        $discountPercent = null;
                        if ($selectedVariant && $selectedVariant->discounts->isNotEmpty()) {
                            $discount = $selectedVariant->discounts->first();
                            if ($discount->discount_type === 'percentage') {
                                $finalPrice -= ($selectedVariant->price * $discount->discount_value) / 100;
                                $discountPercent = $discount->discount_value;
                            } else {
                                $finalPrice -= $discount->discount_value;
                            }
                        }
                    @endphp

                    <div class="mb-6">
                        <div class="flex items-baseline gap-3">
                            <span id="finalPrice" class="text-3xl lg:text-4xl font-bold text-orange-600">Rs.
                                {{ number_format($finalPrice, 2) }}</span>
                            @if ($finalPrice < $originalPrice)
                                <span id="originalPrice" class="text-gray-400 line-through text-lg">Rs.
                                    {{ number_format($originalPrice, 2) }}</span>
                                @if ($discountPercent)
                                    <span
                                        class="bg-red-500 text-white text-sm font-semibold px-2 py-1 rounded">{{ $discountPercent }}%
                                        OFF</span>
                                @endif
                            @endif
                        </div>
                        <p class="text-sm text-green-600 mt-1">Inclusive of all taxes</p>
                    </div>

                    <!-- Variant Selection -->
                    @if ($product->variants->isNotEmpty() && $product->variants->count() > 1)
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Variant:</label>
                            <select id="variantSelect"
                                class="w-full lg:w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $varFinalPrice = $variant->price;
                                        if ($variant->discounts->isNotEmpty()) {
                                            $discount = $variant->discounts->first();
                                            if ($discount->discount_type === 'percentage') {
                                                $varFinalPrice -= ($variant->price * $discount->discount_value) / 100;
                                            } else {
                                                $varFinalPrice -= $discount->discount_value;
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $variant->id }}" data-price="{{ $varFinalPrice }}"
                                        data-original-price="{{ $variant->price }}"
                                        data-stock="{{ $variant->stock_quantity }}"
                                        data-discount-percent="{{ $variant->discounts->first() && $variant->discounts->first()->discount_type === 'percentage' ? $variant->discounts->first()->discount_value : 0 }}"
                                        @if ($variant->images->isNotEmpty()) data-images='@json($variant->images->map(fn($img) => asset('storage/' . $img->image_path)))' @endif>
                                        {{ $variant->name ?? 'Default' }} - Rs. {{ number_format($varFinalPrice, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Stock Status -->
                    <div class="mb-6">
                        <div id="stockInfo" class="flex items-center gap-2">
                            @if ($selectedVariant && $selectedVariant->stock_quantity > 0)
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-green-600 font-medium">{{ $selectedVariant->stock_quantity }} in
                                    stock</span>
                            @else
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-red-500 font-medium">Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    @if ($product->variants->first() && $product->variants->first()->attributeValues->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Specifications</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                @foreach ($product->variants->first()->attributeValues as $attr)
                                    <div class="flex">
                                        <span
                                            class="w-32 text-sm font-medium text-gray-600">{{ $attr->attribute->name }}:</span>
                                        <span class="text-sm text-gray-800">{{ $attr->value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @auth
                        <div class="flex gap-3">
                            <button id="addToCartBtn"
                                class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 21v-6">
                                    </path>
                                </svg>
                                Add to Cart
                            </button>
                            <button id="wishlistBtn"
                                class="p-3 border border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-500 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('orders.place', ['productId' => $product->id, 'variantId' => $variant->id]) }}"
                                class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 text-center">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Buy Now
                            </a>
                        </div>
                    @else
                        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-amber-500 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <div>
                                    <p class="text-amber-700 font-medium">Please login to purchase or book this product</p>
                                    <div class="mt-2 space-x-3">
                                        <a href="{{ route('login') }}"
                                            class="inline-block bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">Login</a>
                                        <a href="{{ route('register') }}"
                                            class="inline-block bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Register</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 opacity-50 cursor-not-allowed">
                            <button disabled
                                class="flex-1 bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 21v-6">
                                    </path>
                                </svg>
                                Login to Add to Cart
                            </button>
                            <button disabled class="p-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3">
                            <button disabled
                                class="w-full bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Login to Book
                            </button>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="mb-6 mt-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Product Details</h1>
                <div class="text-gray-600 leading-relaxed">
                    {!! $selectedVariant->description ?? $product->description !!}
                </div>
            </div>

            @if ($relatedProducts->isNotEmpty())
                <div class="mt-16">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">You May Also Like</h2>
                        <a href="{{ route('products.index') }}"
                            class="text-orange-600 hover:text-orange-700 font-medium">View All →</a>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 lg:gap-6">
                        @foreach ($relatedProducts as $rel)
                            @php
                                $relVariant = $rel->variants->first();
                                $relPrice = $relVariant->price ?? 0;
                                $relOriginalPrice = $relPrice;
                                $relDiscountLabel = null;
                                if ($relVariant && $relVariant->discounts->isNotEmpty()) {
                                    $relDiscount = $relVariant->discounts->first();
                                    if ($relDiscount->discount_type === 'percentage') {
                                        $relPrice -= ($relVariant->price * $relDiscount->discount_value) / 100;
                                        $relDiscountLabel = '-' . $relDiscount->discount_value . '%';
                                    } else {
                                        $relPrice -= $relDiscount->discount_value;
                                        $relDiscountLabel = '-Rs ' . number_format($relDiscount->discount_value);
                                    }
                                }
                                $relImages = $rel->images->where('is_primary', true);
                                if ($relImages->isEmpty() && $relVariant && $relVariant->images->isNotEmpty()) {
                                    $relImages = $relVariant->images->where('is_primary', true);
                                }
                                if ($relImages->isEmpty() && $rel->images->isNotEmpty()) {
                                    $relImages = collect([$rel->images->first()]);
                                }
                                $relImage = $relImages->first();
                            @endphp
                            <a href="{{ route('products.show', $rel->slug) }}"
                                class="group bg-white rounded-xl hover:shadow-xl transition-all duration-300 overflow-hidden">
                                <div class="relative overflow-hidden bg-gray-100 aspect-square">
                                    <img src="{{ $relImage ? asset('storage/' . $relImage->image_path) : $rel->display_image ?? 'https://via.placeholder.com/300x300?text=No+Image' }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        alt="{{ $rel->name }}">
                                    @if ($relDiscountLabel)
                                        <span
                                            class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $relDiscountLabel }}</span>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="font-semibold text-gray-800 line-clamp-2 text-sm lg:text-base">
                                        {{ $rel->name }}</h3>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <span class="text-orange-600 font-bold text-sm lg:text-base">Rs.
                                            {{ number_format($relPrice, 2) }}</span>
                                        @if ($relPrice < $relOriginalPrice)
                                            <span class="text-gray-400 line-through text-xs">Rs.
                                                {{ number_format($relOriginalPrice, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    <script>
        // Function to change main image
        function changeMainImage(imageUrl, element) {
            document.getElementById('mainProductImage').src = imageUrl;
            document.querySelectorAll('.thumbnail-btn').forEach(btn => {
                btn.classList.remove('border-orange-500');
                btn.classList.add('border-transparent');
            });
            if (element) {
                element.classList.remove('border-transparent');
                element.classList.add('border-orange-500');
            }
        }

        // --- NEW: Variant IDs already in cart (injected from backend) ---
        const cartVariantIds = new Set(@json($cartVariantIds));

        document.addEventListener('DOMContentLoaded', function() {
            const variantSelect = document.getElementById('variantSelect');
            if (variantSelect) {
                variantSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price);
                    const originalPrice = parseFloat(selectedOption.dataset.originalPrice);
                    const stock = parseInt(selectedOption.dataset.stock);
                    const discountPercent = parseInt(selectedOption.dataset.discountPercent);
                    const variantImages = selectedOption.dataset.images;

                    const finalPriceSpan = document.getElementById('finalPrice');
                    const originalPriceSpan = document.getElementById('originalPrice');
                    if (finalPriceSpan) {
                        finalPriceSpan.textContent = 'Rs. ' + price.toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    if (originalPriceSpan) {
                        if (price < originalPrice) {
                            originalPriceSpan.classList.remove('hidden');
                            originalPriceSpan.textContent = 'Rs. ' + originalPrice.toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        } else {
                            originalPriceSpan.classList.add('hidden');
                        }
                    }
                    const discountPercentSpan = document.querySelector('.bg-red-500.text-white.text-sm');
                    if (discountPercentSpan && discountPercent > 0) {
                        discountPercentSpan.textContent = discountPercent + '% OFF';
                        discountPercentSpan.classList.remove('hidden');
                    } else if (discountPercentSpan) {
                        discountPercentSpan.classList.add('hidden');
                    }
                    const stockInfoDiv = document.getElementById('stockInfo');
                    if (stockInfoDiv) {
                        if (stock > 0) {
                            stockInfoDiv.innerHTML =
                                `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="text-green-600 font-medium">${stock} in stock</span>`;
                        } else {
                            stockInfoDiv.innerHTML =
                                `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><span class="text-red-500 font-medium">Out of Stock</span>`;
                        }
                    }
                    if (variantImages && variantImages !== 'null') {
                        try {
                            const images = JSON.parse(variantImages);
                            if (images.length > 0) {
                                const mainImage = document.getElementById('mainProductImage');
                                if (mainImage) mainImage.src = images[0];
                                const thumbnailContainer = document.querySelector('.grid.grid-cols-5');
                                if (thumbnailContainer && images.length > 1) {
                                    thumbnailContainer.innerHTML = '';
                                    images.forEach((imageUrl, index) => {
                                        const button = document.createElement('button');
                                        button.onclick = () => changeMainImage(imageUrl, button);
                                        button.className =
                                            `thumbnail-btn relative rounded-lg overflow-hidden border-2 ${index === 0 ? 'border-orange-500' : 'border-transparent'} hover:border-orange-300 transition-all focus:outline-none`;
                                        button.innerHTML =
                                            `<img src="${imageUrl}" alt="Product thumbnail" class="w-full h-24 object-cover">`;
                                        thumbnailContainer.appendChild(button);
                                    });
                                } else if (thumbnailContainer && images.length === 1) {
                                    thumbnailContainer.style.display = 'none';
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing variant images:', e);
                        }
                    }
                });
            }

            @auth
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const variantSelect = document.getElementById('variantSelect');
                    let variantId = null;
                    if (variantSelect) {
                        variantId = variantSelect.value;
                    } else {
                        variantId = '{{ $product->variants->first()->id ?? '' }}';
                    }
                    if (!variantId) {
                        showNotification('error', 'Product variant not available');
                        return;
                    }
                    // --- NEW: Check if already in cart ---
                    if (cartVariantIds.has(variantId.toString())) {
                        showNotification('info', 'This item is already in your cart!');
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
                                showNotification('success', data.message);
                                updateCartCount(data.cart_count);
                                // --- NEW: Add to local Set ---
                                cartVariantIds.add(variantId.toString());
                            } else {
                                showNotification('error', data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('error', 'Failed to add to cart');
                        });
                });
            }

            const wishlistBtn = document.getElementById('wishlistBtn');
            if (wishlistBtn) {
                wishlistBtn.addEventListener('click', function() {
                    fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: '{{ $product->id }}'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', data.message);
                                const icon = wishlistBtn.querySelector('svg');
                                if (data.action === 'added') {
                                    icon.classList.add('text-red-500');
                                    icon.classList.remove('text-gray-500');
                                } else {
                                    icon.classList.remove('text-red-500');
                                    icon.classList.add('text-gray-500');
                                }
                                updateWishlistCount(data.wishlist_count);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
                fetch('{{ route('wishlist.check', $product->id) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.in_wishlist) {
                            const icon = wishlistBtn.querySelector('svg');
                            icon.classList.add('text-red-500');
                            icon.classList.remove('text-gray-500');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        @endauth
        });

        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className =
                `fixed top-20 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : type === 'info' ? 'bg-blue-500' : 'bg-red-500'} transition-opacity duration-300`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function updateCartCount(count) {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = count;
                if (count > 0) el.classList.remove('hidden');
                else el.classList.add('hidden');
            });
        }

        function updateWishlistCount(count) {
            document.querySelectorAll('.wishlist-count').forEach(el => el.textContent = count);
        }
    </script>
</x-frontend.layout>
