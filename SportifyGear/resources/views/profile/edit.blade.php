<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Messages --}}
            @if (session('success'))
                <div
                    class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-2xl mb-8 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('status') == 'profile-updated')
                <div
                    class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-2xl mb-8 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">Profile updated successfully!</span>
                </div>
            @endif

            <!-- Profile Header -->
            <div class="flex items-center gap-5 mb-10">
                <div
                    class="w-24 h-24 bg-orange-100 rounded-3xl flex items-center justify-center ring-4 ring-white shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-orange-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-semibold tracking-tight text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-500 text-lg">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-8 border border-gray-100">
                <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-8 py-7 flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7" />
                    </svg>
                    <h2 class="text-2xl font-semibold text-white">Profile Information</h2>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-7">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Full
                                Name</label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', $user->name) }}"
                                class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email
                                Address</label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', $user->email) }}"
                                class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="phone_no" class="block text-sm font-semibold text-gray-700 mb-1.5">Phone
                                Number</label>
                            <input type="tel" name="phone_no" id="phone_no"
                                value="{{ old('phone_no', $user->phone_no) }}"
                                class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            @error('phone_no')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender"
                                class="block text-sm font-semibold text-gray-700 mb-1.5">Gender</label>
                            <select name="gender" id="gender"
                                class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Select Gender</option>
                                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-12">
                        <a href="{{ route('home') }}"
                            class="px-7 py-4 text-gray-700 font-medium border border-gray-300 rounded-2xl hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-2xl flex items-center gap-2 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-8 border border-gray-100">
                <div class="bg-gray-100 px-8 py-7 flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v-2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-800">Change Password</h2>
                </div>

                <form method="POST" action="{{ route('profile.password.update') }}" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-7">

                        <!-- Current Password -->
                        <div>
                            <label for="current_password"
                                class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" required
                                    class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all pr-12">
                                <button type="button" onclick="togglePasswordVisibility('current_password')"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <!-- Eye (Show) -->
                                    <svg id="current_password_eye" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5 16.477 5 20.268 7.943 21.542 12 20.268 16.057 16.477 19 12 19 7.523 19 3.732 16.057 2.458 12z" />
                                    </svg>
                                    <!-- Eye Slash (Hide) -->
                                    <svg id="current_password_eye_slash" xmlns="http://www.w3.org/2000/svg"
                                        class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908l3.42 3.42M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">New
                                Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required
                                    class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all pr-12">
                                <button type="button" onclick="togglePasswordVisibility('password')"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <!-- Eye (Show) -->
                                    <svg id="password_eye" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5 16.477 5 20.268 7.943 21.542 12 20.268 16.057 16.477 19 12 19 7.523 19 3.732 16.057 2.458 12z" />
                                    </svg>
                                    <!-- Eye Slash (Hide) -->
                                    <svg id="password_eye_slash" xmlns="http://www.w3.org/2000/svg"
                                        class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908l3.42 3.42M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    required
                                    class="w-full px-5 py-4 text-base border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all pr-12">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation')"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <!-- Eye (Show) -->
                                    <svg id="password_confirmation_eye" xmlns="http://www.w3.org/2000/svg"
                                        class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5 16.477 5 20.268 7.943 21.542 12 20.268 16.057 16.477 19 12 19 7.523 19 3.732 16.057 2.458 12z" />
                                    </svg>
                                    <!-- Eye Slash (Hide) -->
                                    <svg id="password_confirmation_eye_slash" xmlns="http://www.w3.org/2000/svg"
                                        class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908l3.42 3.42M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-12">
                        <button type="submit"
                            class="px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-2xl flex items-center gap-2 transition">
                            Update Password
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '_eye');
            const eyeSlash = document.getElementById(fieldId + '_eye_slash');

            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.add('hidden');
                eyeSlash.classList.remove('hidden');
            } else {
                input.type = 'password';
                eye.classList.remove('hidden');
                eyeSlash.classList.add('hidden');
            }
        }
    </script>
</x-frontend.layout>
