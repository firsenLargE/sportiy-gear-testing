<x-frontend.layout>

    <section class="bg-gradient-to-b from-gray-50 to-white overflow-hidden">

        <!-- HERO -->
        <div class="relative bg-gradient-to-r from-[var(--primary)] to-purple-600 text-white py-24">
            <div class="max-w-6xl mx-auto px-4 text-center relative z-10 animate-fadeIn">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
                    About <span class="text-yellow-300">SpotifyGear</span>
                </h1>
                <p class="text-lg md:text-xl max-w-2xl mx-auto opacity-90">
                    Powering your digital lifestyle with premium gadgets, smart accessories, and unbeatable value.
                </p>
            </div>

            <!-- Glow Effects -->
            <div class="absolute -top-20 -left-20 w-72 h-72 bg-purple-400 rounded-full blur-3xl opacity-30"></div>
            <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-pink-400 rounded-full blur-3xl opacity-30"></div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">

            <!-- STORY -->
            <div class="grid md:grid-cols-2 gap-14 items-center mb-24">

                <div class="space-y-5 animate-slideLeft">
                    <h2 class="text-4xl font-bold text-gray-900">
                        Who We Are
                    </h2>

                    <p class="text-gray-600 leading-relaxed text-lg">
                        At <span class="font-semibold text-[var(--primary)]">SpotifyGear</span>, we believe technology
                        should be stylish, reliable, and accessible.
                    </p>

                    <p class="text-gray-600 leading-relaxed">
                        We bring carefully selected gadgets that elevate your everyday life,
                        combining innovation with affordability.
                    </p>

                    <div class="flex gap-4 mt-4">
                        <span class="px-4 py-2 bg-[var(--primary)]/10 text-[var(--primary)] rounded-full text-sm">
                            Innovation
                        </span>
                        <span class="px-4 py-2 bg-purple-100 text-purple-600 rounded-full text-sm">
                            Quality
                        </span>
                        <span class="px-4 py-2 bg-pink-100 text-pink-600 rounded-full text-sm">
                            Trust
                        </span>
                    </div>
                </div>

                <div class="relative group animate-slideRight">
                    <img src="/images/about.jpg"
                        class="rounded-3xl shadow-2xl w-full object-cover transform group-hover:scale-105 transition duration-700">

                    <!-- Glass overlay -->
                    <div
                        class="absolute inset-0 bg-white/10 backdrop-blur-sm rounded-3xl opacity-0 group-hover:opacity-100 transition">
                    </div>
                </div>

            </div>

            <!-- STATS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center mb-24">

                @foreach ([['icon' => 'fa-users', 'value' => '10K+', 'label' => 'Happy Customers'], ['icon' => 'fa-box', 'value' => '5K+', 'label' => 'Products Delivered'], ['icon' => 'fa-star', 'value' => '4.8/5', 'label' => 'Customer Rating'], ['icon' => 'fa-globe', 'value' => 'Nationwide', 'label' => 'Delivery']] as $stat)
                    <div
                        class="bg-white/70 backdrop-blur-xl p-6 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300 animate-fadeUp">
                        <i class="fa-solid {{ $stat['icon'] }} text-3xl text-[var(--primary)] mb-3"></i>
                        <h3 class="text-2xl font-bold">{{ $stat['value'] }}</h3>
                        <p class="text-gray-500 text-sm">{{ $stat['label'] }}</p>
                    </div>
                @endforeach

            </div>

            <!-- MISSION / VISION -->
            <div class="grid md:grid-cols-2 gap-10 mb-24">

                <div
                    class="bg-gradient-to-br from-white to-gray-100 p-8 rounded-2xl shadow-xl hover:scale-105 transition animate-slideLeft">
                    <i class="fa-solid fa-bullseye text-2xl text-[var(--primary)] mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Our Mission</h3>
                    <p class="text-gray-600">
                        Deliver high-quality, innovative tech products that improve everyday life while staying
                        affordable.
                    </p>
                </div>

                <div
                    class="bg-gradient-to-br from-white to-gray-100 p-8 rounded-2xl shadow-xl hover:scale-105 transition animate-slideRight">
                    <i class="fa-solid fa-eye text-2xl text-[var(--primary)] mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Our Vision</h3>
                    <p class="text-gray-600">
                        Become a trusted destination for tech lovers with exceptional service and seamless shopping.
                    </p>
                </div>

            </div>

            <!-- WHY CHOOSE US -->
            <div class="mb-10">
                <h2 class="text-4xl font-bold text-center text-gray-900 mb-12 animate-fadeIn">
                    Why Choose SpotifyGear?
                </h2>

                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">

                    @foreach ([['icon' => 'fa-truck-fast', 'title' => 'Fast Delivery'], ['icon' => 'fa-lock', 'title' => 'Secure Payments'], ['icon' => 'fa-gem', 'title' => 'Premium Quality'], ['icon' => 'fa-headset', 'title' => '24/7 Support']] as $item)
                        <div
                            class="bg-white p-6 rounded-xl shadow-md text-center hover:-translate-y-3 hover:shadow-xl transition duration-300 group animate-fadeUp">
                            <i
                                class="fa-solid {{ $item['icon'] }} text-3xl text-[var(--primary)] mb-3 group-hover:scale-110 transition"></i>
                            <h4 class="font-semibold mb-2">{{ $item['title'] }}</h4>
                            <p class="text-gray-600 text-sm">Reliable & trusted service.</p>
                        </div>
                    @endforeach

                </div>
            </div>



        </div>

        <!-- CTA -->
        <div
            class="relative bg-gradient-to-r from-[var(--primary)] to-purple-600 text-white py-20 mt-10 overflow-hidden mb-0">

            <!-- Blur Background -->
            <div class="absolute inset-0 bg-white/10 backdrop-blur-xl"></div>

            <!-- Content (centered but section full width) -->
            <div class="relative z-10 max-w-6xl mx-auto px-4 text-center">

                <h2 class="text-4xl font-bold mb-4">
                    Upgrade Your Tech Today
                </h2>

                <p class="mb-6 opacity-90 text-lg">
                    Explore the latest gadgets and experience the SpotifyGear difference.
                </p>

                <a href="{{ route('products.index') }}"
                    class="bg-white text-[var(--primary)] px-8 py-3 rounded-xl font-semibold hover:scale-110 transition duration-300 shadow-lg">
                    Shop Now
                </a>

            </div>

            <!-- Glow Effects -->
            <div class="absolute -top-20 -left-20 w-72 h-72 bg-purple-400 rounded-full blur-3xl opacity-30"></div>
            <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-pink-400 rounded-full blur-3xl opacity-30"></div>

        </div>

    </section>

    <!-- ANIMATIONS -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease forwards;
        }

        .animate-fadeUp {
            animation: fadeUp 1s ease forwards;
        }

        .animate-slideLeft {
            animation: slideLeft 1s ease forwards;
        }

        .animate-slideRight {
            animation: slideRight 1s ease forwards;
        }
    </style>

</x-frontend.layout>
