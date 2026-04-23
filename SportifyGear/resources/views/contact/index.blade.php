<x-frontend.layout>

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-100 py-12 px-4">

        <!-- HEADER -->
        <div class="text-center mb-12 animate-fade-in-down">
            <h1 class="text-5xl font-extrabold text-gray-800 tracking-wide">
                Contact <span class="text-[var(--primary)]">Us</span>
            </h1>
        </div>

        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10">

            <!-- LEFT SIDE -->
            <div class="space-y-6">

                <!-- Info Card -->
                <div
                    class="bg-white/70 backdrop-blur-lg p-6 rounded-3xl shadow-lg hover:shadow-2xl transition duration-500 animate-fade-in-left">

                    <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-headset text-[var(--primary)] animate-bounce"></i>
                        Get in Touch
                    </h2>

                    <p class="text-gray-500 text-sm mb-6">
                        Have questions about orders, products, or support? Contact us anytime.
                    </p>

                    <div class="space-y-5 text-gray-600">

                        <div class="flex items-center gap-4 group">
                            <i
                                class="fa-solid fa-location-dot text-xl text-[var(--primary)] group-hover:scale-125 transition"></i>
                            <span>Kathmandu, Nepal</span>
                        </div>

                        <div class="flex items-center gap-4 group">
                            <i
                                class="fa-solid fa-envelope text-xl text-[var(--primary)] group-hover:scale-125 transition"></i>
                            <span>sportifygear@gmail.com</span>
                        </div>

                        <div class="flex items-center gap-4 group">
                            <i
                                class="fa-solid fa-phone text-xl text-[var(--primary)] group-hover:scale-125 transition"></i>
                            <span>+977-9811294545</span>
                        </div>

                        <div class="flex items-center gap-4 group">
                            <i
                                class="fa-solid fa-clock text-xl text-[var(--primary)] group-hover:scale-125 transition"></i>
                            <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                        </div>

                    </div>
                </div>

                <!-- Social Media -->
                <div
                    class="bg-white/70 backdrop-blur-lg p-6 rounded-3xl shadow-lg hover:shadow-2xl transition duration-500 animate-fade-in-left">

                    <h2 class="text-lg font-semibold mb-4">Follow Us</h2>

                    <div class="flex gap-5 text-2xl">

                        <a href="#"
                            class="text-blue-600 hover:text-white hover:bg-blue-600 p-3 rounded-full shadow-md transition duration-300 hover:scale-110">
                            <i class="fa-brands fa-facebook"></i>
                        </a>

                        <a href="#"
                            class="text-sky-500 hover:text-white hover:bg-sky-500 p-3 rounded-full shadow-md transition duration-300 hover:scale-110">
                            <i class="fa-brands fa-twitter"></i>
                        </a>

                        <a href="#"
                            class="text-pink-500 hover:text-white hover:bg-pink-500 p-3 rounded-full shadow-md transition duration-300 hover:scale-110">
                            <i class="fa-brands fa-instagram"></i>
                        </a>

                        <a href="#"
                            class="text-red-500 hover:text-white hover:bg-red-500 p-3 rounded-full shadow-md transition duration-300 hover:scale-110">
                            <i class="fa-brands fa-youtube"></i>
                        </a>

                    </div>
                </div>

            </div>

            <!-- RIGHT SIDE FORM -->
            <div
                class="bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-xl hover:shadow-2xl transition duration-500 animate-fade-in-right">

                <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane text-[var(--primary)]"></i>
                    Send Message
                </h2>

                @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-3 rounded-xl animate-pulse">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Full Name</label>
                        <div class="relative mt-1">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border focus:ring-2 focus:ring-orange-300 focus:border-[var(--primary)] outline-none transition">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <div class="relative mt-1">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border focus:ring-2 focus:ring-orange-300 focus:border-[var(--primary)] outline-none transition">
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Message</label>
                        <div class="relative mt-1">
                            <i class="fa-solid fa-message absolute left-4 top-4 text-gray-400"></i>
                            <textarea name="message" rows="5"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border focus:ring-2 focus:ring-orange-300 focus:border-[var(--primary)] outline-none transition"
                                placeholder="Write your message..."></textarea>
                        </div>
                    </div>

                    <!-- Button -->
                    <button
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-semibold hover:scale-105 transition duration-300 shadow-lg hover:shadow-xl">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Send Message
                    </button>

                </form>

            </div>

        </div>

        <!-- MAP SECTION -->
        <div class="max-w-6xl mx-auto mt-16 animate-fade-in-up">
            <h1 class="text-4xl font-bold text-gray-800 text-center mb-6">Our Location</h1>

            <div class="rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition duration-500">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.4974041639237!2d85.3240!3d27.7172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb1900f0b0b0b0%3A0x0!2sKathmandu!5e0!3m2!1sen!2snp!4v0000000000"
                    class="w-full h-[400px] hover:scale-105 transition duration-500 border-0"></iframe>
            </div>
        </div>

    </div>

    <!-- CUSTOM ANIMATIONS -->
    <style>
        .animate-fade-in-down {
            animation: fadeDown 1s ease;
        }

        .animate-fade-in-left {
            animation: fadeLeft 1s ease;
        }

        .animate-fade-in-right {
            animation: fadeRight 1s ease;
        }

        .animate-fade-in-up {
            animation: fadeUp 1s ease;
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
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
    </style>

</x-frontend.layout>
