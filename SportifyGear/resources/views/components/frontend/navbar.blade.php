<nav class="bg-white/90 backdrop-blur-md shadow-sm border-b  z-40">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-center justify-center py-3 gap-4 relative">

            <!-- Menu -->
            <div class="flex flex-wrap items-center justify-center text-sm font-medium gap-6 text-gray-700">

                <!-- Home -->
                <a href="/" class="relative group py-1">
                    Home
                    <span
                        class="absolute left-0 -bottom-1 w-0 h-[2px] bg-[var(--primary)] transition-all duration-300 group-hover:w-full"></span>
                </a>

                <!-- Shop Dropdown -->
                <div class="group">
                    <a class="flex items-center gap-1 py-1 relative cursor-pointer">
                        Shop
                        <svg class="w-3 h-3 transition-transform duration-300 group-hover:rotate-180" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path d="M5.23 7.21L10 12l4.77-4.79L16 8l-6 6-6-6z" />
                        </svg>

                        <!-- underline -->
                        <span
                            class="absolute left-0 -bottom-1 w-0 h-[2px] bg-[var(--primary)] transition-all duration-300 group-hover:w-full"></span>
                    </a>

                    <!-- Mega Menu -->
                    <div
                        class="absolute left-1/2 -translate-x-1/2 top-full mt-3 px-4 sm:px-6 lg:px-8
                               opacity-0 invisible translate-y-4
                               group-hover:opacity-100 group-hover:visible group-hover:translate-y-0
                               transition-all duration-300 z-40">

                        <div class="max-w-6xl mx-auto bg-white border border-gray-100 shadow-2xl rounded-2xl p-8">

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8">

                                @foreach ($categories as $category)
                                    <div>
                                        <!-- Parent -->
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                            class="font-semibold text-gray-900 hover:text-[var(--primary)] block mb-3 transition">
                                            {{ $category->name }}
                                        </a>

                                        <!-- Children -->
                                        @if ($category->childrenRecursive->count())
                                            <div class="space-y-2">
                                                @foreach ($category->childrenRecursive as $child)
                                                    <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                                        class="block text-sm text-gray-600 hover:text-[var(--primary)] hover:translate-x-1 transition duration-200">
                                                        {{ $child->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>

                            <!-- Bottom -->
                            <div class="mt-8 flex justify-center">
                                <a href="{{ route('products.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full 
                                           text-gray-800 font-medium border border-gray-200
                                           hover:bg-[var(--primary)] hover:text-white hover:border-[var(--primary)]
                                           shadow-sm hover:shadow-md transition duration-300">
                                    View All Products
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 3l7 7-7 7-1.4-1.4L13.2 11H3v-2h10.2L8.6 4.4z" />
                                    </svg>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Flash Sale -->
                <a href="/flash-sale" class="relative group py-1">
                    Flash Sale
                    <span
                        class="absolute left-0 -bottom-1 w-0 h-[2px] bg-red-500 transition-all duration-300 group-hover:w-full"></span>
                </a>

                <!-- About -->
                <a href="/about" class="relative group py-1">
                    About Us
                    <span
                        class="absolute left-0 -bottom-1 w-0 h-[2px] bg-[var(--primary)] transition-all duration-300 group-hover:w-full"></span>
                </a>

                <!-- Contact -->
                <a href="/contact" class="relative group py-1">
                    Contact
                    <span
                        class="absolute left-0 -bottom-1 w-0 h-[2px] bg-[var(--primary)] transition-all duration-300 group-hover:w-full"></span>
                </a>

            </div>

        </div>
    </div>
</nav>
