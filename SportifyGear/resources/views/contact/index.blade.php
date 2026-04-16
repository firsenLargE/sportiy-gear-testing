<x-frontend.layout>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-12 px-4">

        <!-- HEADER -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-800">Contact Us</h1>
            <p class="text-gray-500 mt-2">We’re here to help you anytime</p>
        </div>

        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8">

            <!-- LEFT SIDE -->
            <div class="space-y-6">

                <!-- Info Card -->
                <div class="bg-white p-6 rounded-2xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Get in Touch</h2>
                    <p class="text-gray-500 text-sm mb-6">
                        Have questions about orders, products, or support? Contact us anytime.
                    </p>

                    <div class="space-y-4 text-gray-600">

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-location-dot text-[var(--primary)]"></i>
                            <span>Kathmandu, Nepal</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-[var(--primary)]"></i>
                            <span>sportifygear@gmail.com</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-[var(--primary)]"></i>
                            <span>+977-9811294545</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clock text-[var(--primary)]"></i>
                            <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                        </div>

                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white p-6 rounded-2xl shadow-md">
                    <h2 class="text-lg font-semibold mb-4">Follow Us</h2>

                    <div class="flex gap-4 text-xl">
                        <a href="#" class="text-blue-600 hover:scale-110 transition"><i
                                class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-sky-500 hover:scale-110 transition"><i
                                class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="text-pink-500 hover:scale-110 transition"><i
                                class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-red-500 hover:scale-110 transition"><i
                                class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

            </div>

            <!-- RIGHT SIDE FORM -->
            <div class="bg-white p-8 rounded-2xl shadow-xl">

                <h2 class="text-2xl font-bold mb-6 text-gray-800">Send Message</h2>

                @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-3 rounded-xl">
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
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border focus:ring-2 focus:ring-orange-200 focus:border-[var(--primary)] outline-none">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <div class="relative mt-1">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="you@example.com"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border focus:ring-2 focus:ring-orange-200 focus:border-[var(--primary)] outline-none">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Message</label>
                        <div class="relative mt-1">
                            <i class="fa-solid fa-message absolute left-4 top-4 text-gray-400"></i>
                            <textarea name="message" rows="5" placeholder="Write your message..."
                                class="w-full pl-11 pr-4 py-3 rounded-xl border focus:ring-2 focus:ring-orange-200 focus:border-[var(--primary)] outline-none"></textarea>
                        </div>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Button -->
                    <button
                        class="w-full bg-[var(--primary)] text-white py-3 rounded-xl font-semibold hover:scale-[1.02] transition shadow-md">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Send Message
                    </button>

                </form>

            </div>

        </div>

        <!-- MAP SECTION -->
        <div class="max-w-6xl mx-auto mt-12">
            <div class="bg-white p-6 rounded-2xl shadow-xl">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Our Location</h2>

                <div class="w-full h-[350px] rounded-xl overflow-hidden border">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.4974041639237!2d85.3240!3d27.7172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb1900f0b0b0b0%3A0x0!2sKathmandu!5e0!3m2!1sen!2snp!4v0000000000"
                        class="w-full h-full" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

    </div>

</x-frontend.layout>
