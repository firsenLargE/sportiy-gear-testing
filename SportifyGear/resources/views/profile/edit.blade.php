<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-orange-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">My Profile</h2>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', Auth::user()->name) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', Auth::user()->email) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_no" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone_no" id="phone_no"
                                value="{{ old('phone_no', Auth::user()->phone_no) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('phone_no')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select name="gender" id="gender"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Select Gender</option>
                                <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="other" {{ Auth::user()->gender == 'other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('home') }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Update
                            Profile</button>
                    </div>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                <div class="bg-gray-100 px-6 py-4">
                    <h2 class="text-xl font-bold text-gray-800">Change Password</h2>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current
                                Password</label>
                            <input type="password" name="current_password" id="current_password" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                                New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Update
                            Password</button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                <div class="bg-red-50 px-6 py-4">
                    <h2 class="text-xl font-bold text-red-600">Danger Zone</h2>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 mb-4">Once you delete your account, there is no going back. Please be
                        certain.</p>
                    <form method="POST" action="{{ route('profile.destroy') }}"
                        onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Delete
                            Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-frontend.layout>
