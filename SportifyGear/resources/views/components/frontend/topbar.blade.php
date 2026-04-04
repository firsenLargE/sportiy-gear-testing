<nav class="bg-[var(--primary)] text-white shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <!-- Logo -->
        <a href="/"
            class="flex items-center gap-2 text-2xl font-extrabold tracking-wide hover:opacity-90 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 8h14l-1 12H6L5 8zm3 0V6a4 4 0 118 0v2" />
            </svg>
            SpotifyGear
        </a>

        <!-- Desktop Search -->
        <div class="hidden md:block w-1/2">
            <form method="GET" action="{{ route('products.index') }}" class="relative">
                <input type="text" name="search" placeholder="Search for products..."
                    value="{{ request('search') }}"
                    class="w-full pl-5 pr-12 py-2 rounded-full text-black focus:outline-none focus:ring-2 focus:ring-white transition">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Right Side Icons & Menu -->
        <div class="flex items-center gap-2">
            <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}" class="relative p-2 hover:bg-white/10 rounded-full transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 21v-6" />
                </svg>
                <span
                    class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center hidden">
                    0
                </span>
            </a>

            <!-- Wishlist Icon -->
            @auth
                <a href="{{ route('wishlist.index') }}" class="relative p-2 hover:bg-white/10 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span
                        class="wishlist-count absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center hidden">
                        0
                    </span>
                </a>
            @endauth

            <!-- User Menu / Auth Links -->
            @auth
                <!-- User Dropdown for Desktop -->
                <div class="hidden md:block relative">
                    <button @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-white/10 rounded-full transition">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 z-50" style="display: none;">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            My Profile
                        </a>
                        <a href="{{ route('orders.my') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            My Orders
                        </a>
                        <a href="{{ route('wishlist.index') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Wishlist
                        </a>
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile User Info (shown in mobile menu) -->
            @else
                <!-- Desktop Auth Links -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="flex items-center gap-1 px-4 py-1.5 border border-white rounded-full hover:bg-white hover:text-[var(--primary)] transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="flex items-center gap-1 px-4 py-1.5 bg-white text-[var(--primary)] rounded-full font-semibold hover:bg-gray-200 transition">
                        Register
                    </a>
                </div>
            @endauth

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2 hover:bg-white/10 rounded-full transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden px-4 pb-3 space-y-2"
        style="display: none;">
        <form method="GET" action="{{ route('products.index') }}" class="relative">
            <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                class="w-full pl-4 pr-10 py-2 rounded-full text-black focus:outline-none">
            <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </button>
        </form>

        @auth
            <!-- Mobile User Info -->
            <div class="flex items-center gap-3 px-4 py-3 border-t border-white/20">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-sm opacity-80">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}"
                class="block px-4 py-2 border border-white/30 rounded-full text-center hover:bg-white hover:text-[var(--primary)] transition">
                My Profile
            </a>
            <a href="{{ route('orders.my') }}"
                class="block px-4 py-2 border border-white/30 rounded-full text-center hover:bg-white hover:text-[var(--primary)] transition">
                My Orders
            </a>
            <a href="{{ route('wishlist.index') }}"
                class="block px-4 py-2 border border-white/30 rounded-full text-center hover:bg-white hover:text-[var(--primary)] transition">
                Wishlist
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-2 bg-red-500 rounded-full text-center hover:bg-red-600 transition">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('contact.index') }}"
                class="block px-4 py-2 border border-white rounded-full text-center hover:bg-white hover:text-[var(--primary)] transition">
                Contact
            </a>
            <a href="{{ route('login') }}"
                class="block px-4 py-2 border border-white rounded-full text-center hover:bg-white hover:text-[var(--primary)] transition">
                Login
            </a>
            <a href="{{ route('register') }}"
                class="block px-4 py-2 bg-white text-[var(--primary)] rounded-full text-center font-semibold hover:bg-gray-200 transition">
                Register
            </a>
        @endauth
    </div>
</nav>

<script>
    // Update cart count function
    function updateCartCount() {
        fetch('{{ route('cart.count') }}')
            .then(response => response.json())
            .then(data => {
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(el => {
                    if (data.count > 0) {
                        el.textContent = data.count;
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });
            })
            .catch(error => console.error('Error fetching cart count:', error));
    }

    // Update wishlist count function
    function updateWishlistCount() {
        fetch('{{ route('wishlist.index') }}')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const count = doc.querySelectorAll('.wishlist-item').length;
                const wishlistCountElements = document.querySelectorAll('.wishlist-count');
                wishlistCountElements.forEach(el => {
                    if (count > 0) {
                        el.textContent = count;
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });
            })
            .catch(error => console.error('Error fetching wishlist count:', error));
    }

    // Update counts on page load
    document.addEventListener('DOMContentLoaded', function() {
        @auth
        updateCartCount();
        updateWishlistCount();
    @endauth
    });

    // Make functions available globally for other pages
    window.updateCartCount = updateCartCount;
    window.updateWishlistCount = updateWishlistCount;
</script>
