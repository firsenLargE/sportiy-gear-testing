<x-frontend.layout>

    <section class="py-12 bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-5">
            <div x-data="{ mobileFilterOpen: false }" class="flex flex-col lg:flex-row gap-10 lg:gap-4">

                <!-- ================= MOBILE FILTER TOGGLE ================= -->
                <div class="lg:hidden mb-4">
                    <button @click="mobileFilterOpen = !mobileFilterOpen"
                        class="w-full bg-orange-500 text-white py-3 rounded-xl font-semibold flex justify-between items-center px-4">
                        <span>Filters</span>
                        <svg :class="{ 'rotate-180': mobileFilterOpen }" class="w-5 h-5 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <!-- ================= SIDEBAR FILTER ================= -->
                <div :class="{ 'block': mobileFilterOpen, 'hidden': !mobileFilterOpen }"
                    class="lg:block w-full lg:w-60 xl:w-80 lg:ml-2 lg:sticky lg:top-24 lg:self-start">

                    <form method="GET" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-8">
                        <!-- FILTER HEADER -->
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                    </path>
                                </svg>
                                Filters
                            </h2>
                            <button type="button" onclick="clearAllFilters()"
                                class="text-sm font-medium text-orange-500 hover:text-orange-700 transition-all hover:scale-105">
                                Clear all
                            </button>
                        </div>


                        <!-- ================= CATEGORIES ================= -->
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                Categories
                            </h3>

                            <div class="space-y-2 max-h-80 overflow-y-auto pr-3 custom-scrollbar">

                                @php
                                    function hasActiveChild($category)
                                    {
                                        if (request('category') == $category->slug) {
                                            return true;
                                        }

                                        foreach ($category->childrenRecursive as $child) {
                                            if (hasActiveChild($child)) {
                                                return true;
                                            }
                                        }

                                        return false;
                                    }

                                    function renderCategories($categories)
                                    {
                                        foreach ($categories as $category) {
                                            $isActive = request('category') == $category->slug;
                                            $shouldOpen = hasActiveChild($category) ? 'true' : 'false';

                                            echo '<div x-data="{ open: ' . $shouldOpen . ' }" class="ml-1">';

                                            // ROW
                                            echo '<div class="flex items-center justify-between group">';

                                            // LINK
                                            echo '<a href="' .
                                                request()->fullUrlWithQuery(['category' => $category->slug]) .
                                                '" 
                class="flex-1 flex items-center gap-2 text-sm px-3 py-2.5 rounded-xl transition-all duration-200 ' .
                                                ($isActive
                                                    ? 'text-orange-600 bg-orange-50 font-medium shadow-sm'
                                                    : 'text-gray-600 hover:text-orange-600 hover:bg-orange-50/50') .
                                                '">';

                                            echo '<span>' . e($category->name) . '</span>';
                                            if ($category->childrenRecursive->count()) {
                                                echo '<span class="text-xs text-gray-400">(' .
                                                    $category->childrenRecursive->count() .
                                                    ')</span>';
                                            }
                                            echo '</a>';

                                            // TOGGLE
                                            if ($category->childrenRecursive->isNotEmpty()) {
                                                echo '<button type="button" @click="open = !open"
        class="text-gray-400 hover:text-orange-500 transition-all p-2 rounded-lg hover:bg-gray-100">

        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4v16m8-8H4" />
        </svg>

        <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 12H4" />
        </svg>

    </button>';
                                            }
                                            echo '</div>';

                                            // CHILDREN
                                            if ($category->childrenRecursive->isNotEmpty()) {
                                                echo '<div x-show="open" x-transition.duration.200ms class="ml-5 mt-1 space-y-1 border-l-2 border-gray-100 pl-2">';
                                                renderCategories($category->childrenRecursive);
                                                echo '</div>';
                                            }

                                            echo '</div>';
                                        }
                                    }
                                @endphp

                                @php renderCategories($categories); @endphp

                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">

                                Price Range
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rs</span>
                                    <input type="number" name="min" placeholder="Min" value="{{ request('min') }}"
                                        class="w-full border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 rounded-xl px-4 py-3 pl-8 text-sm outline-none transition">
                                </div>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rs</span>
                                    <input type="number" name="max" placeholder="Max" value="{{ request('max') }}"
                                        class="w-full border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 rounded-xl px-4 py-3 pl-8 text-sm outline-none transition">
                                </div>
                            </div>
                        </div>

                        <!-- Attributes -->
                        @foreach ($attributes as $attribute)
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    {{ $attribute->name }}
                                </h3>
                                <div class="space-y-2.5 max-h-56 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach ($attribute->values as $value)
                                        <label
                                            class="flex items-center gap-3 cursor-pointer group p-1 rounded-lg hover:bg-gray-50 transition">
                                            <input type="checkbox" name="attribute_{{ $attribute->id }}[]"
                                                value="{{ $value->id }}"
                                                {{ in_array($value->id, request()->get('attribute_' . $attribute->id, [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 focus:ring-offset-0 cursor-pointer">
                                            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">
                                                {{ $value->value }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Apply Filters
                        </button>
                    </form>
                </div>

                <!-- ================= PRODUCTS GRID ================= -->
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                        <div>
                            <div class="w-full text-center">
                                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                                    All Products
                                </h1>
                            </div>
                            <p class="text-gray-500 mt-2 flex items-center gap-1">

                                @if (request()->has('search') && request('search') != '')
                                    {{ $products->total() }} products of "{{ request('search') }}" available
                                @else
                                    {{ $products->total() }} products available
                                @endif
                            </p>
                        </div>

                        <!-- Sorting -->
                        <form method="GET" class="relative">
                            <select name="sort" onchange="this.form.submit()"
                                class="appearance-none bg-white border border-gray-200 rounded-xl px-5 py-2.5 pr-10 text-sm font-medium text-gray-700 hover:border-orange-300 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none transition cursor-pointer">
                                <option value="">Sort By</option>
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest
                                </option>
                                <option value="price_low_high"
                                    {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High
                                </option>
                                <option value="price_high_low"
                                    {{ request('sort') == 'price_high_low' ? 'selected' : '' }}> Price: High to Low
                                </option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}> Name:
                                    A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                                    Name: Z-A</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            <!-- Keep existing filters -->
                            @foreach (request()->except('sort', 'page') as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $v)
                                        <input type="hidden" name="{{ $key }}[]"
                                            value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                        </form>
                    </div>

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
                                class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-orange-100 transform hover:-translate-y-0.5 flex flex-col">

                                <!-- 🔴 DISCOUNT BADGE -->
                                @if ($discountLabel)
                                    <div
                                        class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md z-10">
                                        {{ $discountLabel }}
                                    </div>
                                @endif

                                <!-- Image Container -->
                                <div class="relative overflow-hidden bg-gray-100 h-40 sm:h-56">
                                    <img src="{{ $variantImage }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    <!-- Quick View Overlay -->
                                    <div
                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300">
                                    </div>
                                </div>

                                <div class="p-3 sm:p-4">
                                    <!-- Brand/Category Tag -->
                                    @if ($product->category)
                                        <div class="text-xs text-orange-500 font-medium mb-1 uppercase tracking-wide">
                                            {{ $product->category->name }}
                                        </div>
                                    @endif

                                    <!-- NAME -->
                                    <h3
                                        class="font-bold text-gray-800 line-clamp-2 group-hover:text-orange-600 transition">
                                        {{ $product->name }}
                                    </h3>

                                    <!-- VARIANT DESCRIPTION -->
                                    @if ($variant && $variant->description)
                                        <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">
                                            {!! Str::limit(strip_tags($variant->description), 120) !!}
                                        </p>
                                    @endif

                                    <!-- PRICE -->
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

                                    <!-- RATING & STOCK Row -->
                                    <div class="mt-3 flex items-center justify-between">
                                        <!-- ⭐ RATING -->
                                        @if ($product->reviews_count > 0)
                                            <div class="flex items-center gap-1.5">
                                                <div class="flex items-center gap-0.5">
                                                    @php
                                                        $rating = round($product->reviews_avg_rating * 2) / 2;
                                                    @endphp

                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($rating >= $i)
                                                            <svg class="w-3.5 h-3.5 text-yellow-400 fill-current"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.153c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.36-2.44a1 1 0 00-1.176 0l-3.36 2.44c-.784.57-1.838-.197-1.539-1.118l1.285-3.957a1 1 0 00-.364-1.118L2.042 9.384c-.783-.57-.38-1.81.588-1.81h4.152a1 1 0 00.951-.69l1.286-3.957z" />
                                                            </svg>
                                                        @elseif ($rating + 0.5 == $i)
                                                            <svg class="w-3.5 h-3.5 text-yellow-400"
                                                                viewBox="0 0 20 20" fill="currentColor">
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
                                                <span
                                                    class="text-gray-400 text-xs">({{ $product->reviews_count }})</span>
                                            </div>
                                        @else
                                            <div></div>
                                        @endif


                                        <!-- STOCK -->
                                        @if ($variant && $variant->stock_quantity > 0)
                                            <div class="flex items-center gap-1 text-xs text-green-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>{{ $variant->stock_quantity }} in stock</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1 text-xs text-red-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <span>Out of Stock</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12">
                        {{ $products->links('pagination::tailwind') }}
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function clearAllFilters() {
            window.location = "{{ route('products.index') }}";
        }
    </script>

    <script>
        function clearAllFilters() {
            window.location = "{{ route('products.index') }}";
        }

        function removeFilter(param) {
            let url = new URL(window.location.href);
            url.searchParams.delete(param);
            window.location.href = url.toString();
        }

        // Optional: Add smooth transitions for filter removal
        document.addEventListener('DOMContentLoaded', function() {
            // Add custom scrollbar styling if not present
            const style = document.createElement('style');
            style.textContent = `
                .custom-scrollbar::-webkit-scrollbar {
                    width: 5px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #fdba74;
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #f97316;
                }
            `;
            document.head.appendChild(style);
        });
    </script>

</x-frontend.layout>
