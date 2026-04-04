<nav class="bg-white shadow-md border-t">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-center justify-center py-3 gap-2">

            <!-- Left Menu -->
            <div class="flex flex-wrap items-center justify-center text-sm font-semibold gap-4">

                <a href="/" class="hover:text-[var(--primary)] py-1">Home</a>

                <!-- Shop Dropdown -->
                <div class="relative group">
                    <a href="{{ route('products.index') }}"
                        class="hover:text-[var(--primary)] flex items-center gap-1 py-1">
                        Shop
                        <svg class="w-3 h-3 inline-block" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.23 7.21L10 12l4.77-4.79L16 8l-6 6-6-6z" />
                        </svg>
                    </a>

                    <!-- Dropdown -->
                    <div
                        class="absolute left-0 top-full mt-2 bg-white shadow-lg rounded-lg opacity-0 group-hover:opacity-100 transition-all invisible group-hover:visible z-50 w-48">
                        <a href="{{ route('products.index') }}?category=electronics"
                            class="block px-4 py-2 hover:bg-gray-100">Electronics</a>
                        <a href="{{ route('products.index') }}?category=fashion"
                            class="block px-4 py-2 hover:bg-gray-100">Fashion</a>
                        <a href="{{ route('products.index') }}?category=home"
                            class="block px-4 py-2 hover:bg-gray-100">Home & Living</a>
                        <a href="{{ route('products.index') }}?category=toys"
                            class="block px-4 py-2 hover:bg-gray-100">Toys & Games</a>
                        <a href="{{ route('products.index') }}?category=books"
                            class="block px-4 py-2 hover:bg-gray-100">Books</a>
                    </div>
                </div>

                <a href="/deals" class="hover:text-[var(--primary)] py-1">Deals</a>
                <a href="/flash-sale" class="hover:text-[var(--primary)] py-1">Flash Sale</a>
                <a href="/blog" class="hover:text-[var(--primary)] py-1">Blog</a>
                <a href="/about" class="hover:text-[var(--primary)] py-1">About</a>

            </div>

        </div>
    </div>
</nav>
