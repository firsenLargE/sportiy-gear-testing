<x-frontend.layout>

    <div class="container mx-auto px-4 py-10">

        <div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow">

            <h2 class="text-2xl font-bold mb-6 text-center text-[var(--primary)]">
                Contact Us
            </h2>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-400">

                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-400">

                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium mb-1">Message</label>
                    <textarea name="message" rows="4"
                        class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('message') }}</textarea>

                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <button
                    class="w-full bg-[var(--primary)] text-white py-2 rounded-lg hover:opacity-90 transition font-semibold">
                    Send Message
                </button>

            </form>

        </div>

    </div>

</x-frontend.layout>
