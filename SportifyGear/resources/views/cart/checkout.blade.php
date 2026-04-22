<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Flash Messages (success/error) --}}
@if (session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
        {{ session('error') }}
    </div>
@endif
@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
        {{ session('success') }}
    </div>
@endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('orders.prepare') }}" id="checkoutForm">
                        @csrf
                        @foreach ($selectedItems as $item)
                            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
                        @endforeach
                        <input type="hidden" name="shipping_fee" id="selected_shipping_fee" value="{{ $shipping ?? 0 }}">

                        <!-- Address Section -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Shipping Address</h2>
                            <div class="mb-4">
                                <label for="address_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Address
                                </label>
                                <!-- ✅ PRE‑SELECT FIRST ADDRESS DIRECTLY IN BLADE -->
                                <select name="address_id" id="address_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Select an address</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $address->address_line1 }}
                                            @if ($address->address_line2), {{ $address->address_line2 }} @endif
                                            , {{ $address->district?->name }}, {{ $address->province?->name }}
                                            @if ($address->nearest_landmark) (Near: {{ $address->nearest_landmark }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-center flex justify-center gap-4">
                                <a href="#" id="openAddressModalBtn" class="text-orange-600 hover:text-orange-700 text-sm">
                                    + Add New Address
                                </a>
                                <a href="#" id="manageAddressesBtn" class="text-blue-600 hover:text-blue-700 text-sm">
                                    Manage Addresses
                                </a>
                            </div>
                            <div id="addressSuccessMessage" class="mt-3 text-green-600 text-sm hidden"></div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>
                        <div class="space-y-3 max-h-96 overflow-y-auto mb-4">
                            @forelse ($selectedItems ?? [] as $item)
                                @php $price = $item->final_price ?? ($item->variant->price ?? 0); @endphp
                                <div class="flex gap-3 pb-3 border-b">
                                    <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                        @php
                                            $image = $item->variant->images->where('is_primary', true)->first() ??
                                                     $item->variant->images->first();
                                        @endphp
                                        <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/64' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm">{{ $item->variant->product->name }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                        <p class="text-orange-600 font-bold text-sm">
                                            Rs. {{ number_format($price * $item->quantity, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-red-600 text-center py-4">No items selected.
                                    <a href="{{ route('cart.index') }}" class="underline">Go back</a>
                                </p>
                            @endforelse
                        </div>
                        <div class="space-y-2 pt-3 border-t">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rs. {{ number_format($subtotal ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span id="shipping_amount_display" class="font-semibold">
                                    {{ ($shipping ?? 0) > 0 ? 'Rs. ' . number_format($shipping, 2) : 'Free' }}
                                </span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-gray-800">Total</span>
                                    <span id="total_amount_display" class="text-xl font-bold text-orange-600">
                                        Rs. {{ number_format($total ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" form="checkoutForm"
                            class="w-full mt-6 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700">
                            Pay Now
                        </button>
                        <a href="{{ route('cart.index') }}"
                            class="block text-center mt-3 text-gray-500 hover:text-gray-700 text-sm">
                            ← Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Modal (scrollable) -->
    <div id="addAddressModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-5 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Add New Address</h3>
                    <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto px-5 py-3">
                    <div id="modalErrorMessage" class="mb-3 text-red-600 text-sm hidden"></div>
                    <form id="newAddressForm" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone_no" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                            <select name="province_id" id="province_select" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <option value="">Select Province</option>
                                @foreach (\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                            <select name="district_id" id="district_select" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" disabled>
                                <option value="">First select province</option>
                            </select>
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                            <input type="text" name="address_line1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="address_line2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nearest Landmark</label>
                            <input type="text" name="nearest_landmark" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 p-5 border-t flex-shrink-0">
                    <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" form="newAddressForm" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Save Address</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Addresses Modal (scrollable) -->
    <div id="manageAddressesModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center p-5 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Manage Addresses</h3>
                    <button type="button" class="close-manage-modal text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="manageAddressesList" class="flex-1 overflow-y-auto p-5 space-y-3">
                    <div class="text-center text-gray-500 py-4">Loading...</div>
                </div>
                <div class="p-5 border-t flex-shrink-0">
                    <div class="flex justify-end">
                        <button type="button" class="close-manage-modal px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal (scrollable) -->
    <div id="editAddressModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-5 border-b flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Edit Address</h3>
                    <button type="button" class="close-edit-modal text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto px-5 py-3">
                    <div id="editModalErrorMessage" class="mb-3 text-red-600 text-sm hidden"></div>
                    <form id="editAddressForm" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="address_id" id="edit_address_id">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone_no" id="edit_phone_no" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="edit_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                            <select name="province_id" id="edit_province_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <option value="">Select Province</option>
                                @foreach (\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                            <select name="district_id" id="edit_district_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" disabled>
                                <option value="">First select province</option>
                            </select>
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                            <input type="text" name="address_line1" id="edit_address_line1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="address_line2" id="edit_address_line2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nearest Landmark</label>
                            <input type="text" name="nearest_landmark" id="edit_nearest_landmark" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <span class="edit-error-message text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 p-5 border-t flex-shrink-0">
                    <button type="button" class="close-edit-modal px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" form="editAddressForm" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Update Address</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal (fixed size, no scroll needed) -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full">
                <div class="p-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Delete Address</h3>
                    <p class="text-gray-500">Are you sure you want to delete this address? This action cannot be undone.</p>
                    <div class="flex justify-center gap-3 mt-6">
                        <button type="button" id="confirmCancelBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                        <button type="button" id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM elements
            const modal = document.getElementById('addAddressModal');
            const manageModal = document.getElementById('manageAddressesModal');
            const editModal = document.getElementById('editAddressModal');
            const confirmModal = document.getElementById('confirmModal');
            const openBtn = document.getElementById('openAddressModalBtn');
            const manageBtn = document.getElementById('manageAddressesBtn');
            const closeBtns = document.querySelectorAll('.close-modal');
            const closeManageBtns = document.querySelectorAll('.close-manage-modal');
            const closeEditBtns = document.querySelectorAll('.close-edit-modal');
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const provinceSelect = document.getElementById('province_select');
            const districtSelect = document.getElementById('district_select');
            const addressSelect = document.getElementById('address_id');
            const form = document.getElementById('newAddressForm');
            const editForm = document.getElementById('editAddressForm');
            const shippingFeeInput = document.getElementById('selected_shipping_fee');
            const shippingDisplay = document.getElementById('shipping_amount_display');
            const totalDisplay = document.getElementById('total_amount_display');
            const modalErrorDiv = document.getElementById('modalErrorMessage');
            const editModalErrorDiv = document.getElementById('editModalErrorMessage');
            const addressSuccessDiv = document.getElementById('addressSuccessMessage');
            const manageListDiv = document.getElementById('manageAddressesList');

            const subtotal = {{ $subtotal ?? 0 }};
            let pendingDeleteId = null;

            // ✅ Immediately fetch shipping fee for the pre-selected address
            if (addressSelect.value) {
                fetchShippingFee(addressSelect.value);
            }

            // Helper functions
            function clearInlineErrors() {
                document.querySelectorAll('.error-message').forEach(el => {
                    el.classList.add('hidden');
                    el.textContent = '';
                });
                if (modalErrorDiv) modalErrorDiv.classList.add('hidden');
            }

            function clearEditErrors() {
                document.querySelectorAll('.edit-error-message').forEach(el => {
                    el.classList.add('hidden');
                    el.textContent = '';
                });
                if (editModalErrorDiv) editModalErrorDiv.classList.add('hidden');
            }

            function displayErrors(errors, container = modalErrorDiv, errorClass = '.error-message') {
                if (typeof errors === 'object') {
                    for (const field in errors) {
                        const fieldErrorSpan = document.querySelector(`[name="${field}"]`)?.closest('div')?.querySelector(errorClass);
                        if (fieldErrorSpan) {
                            fieldErrorSpan.textContent = errors[field][0];
                            fieldErrorSpan.classList.remove('hidden');
                        } else if (container) {
                            container.textContent = errors[field][0];
                            container.classList.remove('hidden');
                        }
                    }
                } else if (typeof errors === 'string' && container) {
                    container.textContent = errors;
                    container.classList.remove('hidden');
                }
            }

            function showSuccessMessage(message) {
                if (addressSuccessDiv) {
                    addressSuccessDiv.textContent = message;
                    addressSuccessDiv.classList.remove('hidden');
                    setTimeout(() => addressSuccessDiv.classList.add('hidden'), 3000);
                }
            }

            function updateShippingAndTotal(shippingFee) {
                const fee = parseFloat(shippingFee);
                const validFee = isNaN(fee) ? 0 : fee;
                if (shippingDisplay) {
                    shippingDisplay.textContent = validFee > 0 ? 'Rs. ' + validFee.toFixed(2) : 'Free';
                }
                const total = subtotal + validFee;
                if (totalDisplay) {
                    totalDisplay.textContent = 'Rs. ' + total.toFixed(2);
                }
                if (shippingFeeInput) {
                    shippingFeeInput.value = validFee;
                }
            }

            function fetchShippingFee(addressId) {
                if (!addressId) return;
                fetch(`/addresses/${addressId}/shipping-fee`)
                    .then(res => res.json())
                    .then(data => {
                        const fee = data.shipping_fee ?? data.fee ?? 0;
                        updateShippingAndTotal(fee);
                    })
                    .catch(err => console.error('Error fetching shipping fee:', err));
            }

            function refreshAddressDropdown(selectedId = null) {
                fetch('/addresses/user-addresses')
                    .then(res => res.json())
                    .then(data => {
                        if (data.addresses) {
                            addressSelect.innerHTML = '<option value="">Select an address</option>';
                            data.addresses.forEach(addr => {
                                let displayText = addr.address_line1;
                                if (addr.address_line2) displayText += `, ${addr.address_line2}`;
                                displayText += `, ${addr.district?.name || ''}, ${addr.province?.name || ''}`;
                                if (addr.nearest_landmark) displayText += ` (Near: ${addr.nearest_landmark})`;
                                const option = document.createElement('option');
                                option.value = addr.id;
                                option.textContent = displayText;
                                addressSelect.appendChild(option);
                            });
                            if (selectedId && addressSelect.querySelector(`option[value="${selectedId}"]`)) {
                                addressSelect.value = selectedId;
                                localStorage.setItem('checkout_selected_address_id', selectedId);
                                fetchShippingFee(selectedId);
                            } else {
                                localStorage.removeItem('checkout_selected_address_id');
                            }
                        }
                    })
                    .catch(err => console.error('Error refreshing addresses:', err));
            }

            function loadManageAddresses() {
                fetch('/addresses/user-addresses')
                    .then(res => res.json())
                    .then(data => {
                        if (data.addresses && data.addresses.length > 0) {
                            manageListDiv.innerHTML = '';
                            data.addresses.forEach(addr => {
                                const addrDiv = document.createElement('div');
                                addrDiv.className = 'border rounded-lg p-3 flex justify-between items-center hover:bg-gray-50 transition';
                                addrDiv.innerHTML = `
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">${escapeHtml(addr.name)}</p>
                                        <p class="text-sm text-gray-600">${escapeHtml(addr.address_line1)}${addr.address_line2 ? ', ' + escapeHtml(addr.address_line2) : ''}, ${escapeHtml(addr.district?.name || '')}, ${escapeHtml(addr.province?.name || '')}</p>
                                        <p class="text-sm text-gray-500">Phone: ${escapeHtml(addr.phone_no)} ${addr.email ? '| Email: ' + escapeHtml(addr.email) : ''}</p>
                                        ${addr.nearest_landmark ? `<p class="text-sm text-gray-500">Near: ${escapeHtml(addr.nearest_landmark)}</p>` : ''}
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        <button type="button" class="edit-address-btn text-blue-600 hover:text-blue-800 font-medium" data-id="${addr.id}">Edit</button>
                                        <button type="button" class="delete-address-btn text-red-600 hover:text-red-800 font-medium" data-id="${addr.id}">Delete</button>
                                    </div>
                                `;
                                manageListDiv.appendChild(addrDiv);
                            });
                            document.querySelectorAll('.edit-address-btn').forEach(btn => {
                                btn.addEventListener('click', () => openEditModal(btn.dataset.id));
                            });
                            document.querySelectorAll('.delete-address-btn').forEach(btn => {
                                btn.addEventListener('click', () => {
                                    pendingDeleteId = btn.dataset.id;
                                    confirmModal.classList.remove('hidden');
                                });
                            });
                        } else {
                            manageListDiv.innerHTML = '<div class="text-center text-gray-500 py-4">No addresses found. Add one using the "+ Add New Address" button.</div>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        manageListDiv.innerHTML = '<div class="text-center text-red-500 py-4">Failed to load addresses.</div>';
                    });
            }

            function openEditModal(addressId) {
                fetch(`/addresses/${addressId}/edit-data`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.address) {
                            const addr = data.address;
                            document.getElementById('edit_address_id').value = addr.id;
                            document.getElementById('edit_name').value = addr.name;
                            document.getElementById('edit_phone_no').value = addr.phone_no;
                            document.getElementById('edit_email').value = addr.email || '';
                            document.getElementById('edit_address_line1').value = addr.address_line1;
                            document.getElementById('edit_address_line2').value = addr.address_line2 || '';
                            document.getElementById('edit_nearest_landmark').value = addr.nearest_landmark || '';
                            document.getElementById('edit_province_id').value = addr.province_id;
                            const provinceId = addr.province_id;
                            fetch(`/districts/${provinceId}`)
                                .then(res => res.json())
                                .then(districts => {
                                    const districtSelectEdit = document.getElementById('edit_district_id');
                                    districtSelectEdit.disabled = false;
                                    districtSelectEdit.innerHTML = '<option value="">Select District</option>';
                                    districts.forEach(district => {
                                        const option = document.createElement('option');
                                        option.value = district.id;
                                        option.textContent = district.name;
                                        if (district.id == addr.district_id) option.selected = true;
                                        districtSelectEdit.appendChild(option);
                                    });
                                });
                            editModal.classList.remove('hidden');
                            clearEditErrors();
                        }
                    })
                    .catch(err => console.error(err));
            }

            function deleteAddress(addressId) {
                fetch(`/addresses/${addressId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage('Address deleted successfully');
                        if (addressSelect.value == addressId) {
                            addressSelect.value = '';
                            localStorage.removeItem('checkout_selected_address_id');
                            updateShippingAndTotal(0);
                        }
                        refreshAddressDropdown();
                        if (!manageModal.classList.contains('hidden')) {
                            loadManageAddresses();
                        }
                    } else {
                        showSuccessMessage(data.message || 'Failed to delete address');
                    }
                })
                .catch(err => console.error(err));
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            // Persist selected address
            const STORAGE_KEY = 'checkout_selected_address_id';
            function saveSelectedAddress() {
                const selectedValue = addressSelect.value;
                if (selectedValue) localStorage.setItem(STORAGE_KEY, selectedValue);
                else localStorage.removeItem(STORAGE_KEY);
            }
            function restoreSelectedAddress() {
                const savedId = localStorage.getItem(STORAGE_KEY);
                if (savedId && addressSelect.querySelector(`option[value="${savedId}"]`)) {
                    addressSelect.value = savedId;
                    fetchShippingFee(savedId);
                } else {
                    localStorage.removeItem(STORAGE_KEY);
                }
            }
            addressSelect.addEventListener('change', function() {
                saveSelectedAddress();
                fetchShippingFee(this.value);
            });
            restoreSelectedAddress();

            // Open add modal
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.remove('hidden');
                clearInlineErrors();
                form.reset();
                districtSelect.disabled = true;
                districtSelect.innerHTML = '<option value="">First select province</option>';
            });

            // Open manage modal
            manageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loadManageAddresses();
                manageModal.classList.remove('hidden');
            });

            // Close modals
            [...closeBtns, ...closeManageBtns, ...closeEditBtns].forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    manageModal.classList.add('hidden');
                    editModal.classList.add('hidden');
                    confirmModal.classList.add('hidden');
                    pendingDeleteId = null;
                });
            });

            // Confirmation modal actions
            confirmCancelBtn.addEventListener('click', () => {
                confirmModal.classList.add('hidden');
                pendingDeleteId = null;
            });
            confirmDeleteBtn.addEventListener('click', () => {
                if (pendingDeleteId) {
                    deleteAddress(pendingDeleteId);
                    confirmModal.classList.add('hidden');
                    pendingDeleteId = null;
                }
            });

            // Province change for add modal
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                if (!provinceId) {
                    districtSelect.disabled = true;
                    districtSelect.innerHTML = '<option value="">First select province</option>';
                    return;
                }
                fetch(`/districts/${provinceId}`)
                    .then(res => res.json())
                    .then(data => {
                        districtSelect.disabled = false;
                        districtSelect.innerHTML = '<option value="">Select District</option>';
                        if (data.length === 0) {
                            districtSelect.innerHTML = '<option value="">No shipping available</option>';
                            districtSelect.disabled = true;
                            return;
                        }
                        data.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.id;
                            option.textContent = district.name;
                            districtSelect.appendChild(option);
                        });
                    })
                    .catch(err => console.error(err));
            });

            // Province change for edit modal
            const editProvinceSelect = document.getElementById('edit_province_id');
            const editDistrictSelect = document.getElementById('edit_district_id');
            editProvinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                if (!provinceId) {
                    editDistrictSelect.disabled = true;
                    editDistrictSelect.innerHTML = '<option value="">First select province</option>';
                    return;
                }
                fetch(`/districts/${provinceId}`)
                    .then(res => res.json())
                    .then(data => {
                        editDistrictSelect.disabled = false;
                        editDistrictSelect.innerHTML = '<option value="">Select District</option>';
                        data.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.id;
                            option.textContent = district.name;
                            editDistrictSelect.appendChild(option);
                        });
                    })
                    .catch(err => console.error(err));
            });

            // Submit new address
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearInlineErrors();
                const formData = new FormData(form);
                fetch('{{ route('addresses.store') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                })
                .then(async res => {
                    const text = await res.text();
                    let data;
                    try { data = JSON.parse(text); } catch(e) { throw new Error('Invalid response'); }
                    if (!res.ok) {
                        if (res.status === 422 && data.errors) displayErrors(data.errors);
                        else displayErrors(data.message || 'Failed to add address');
                        throw new Error(data.message);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        refreshAddressDropdown(data.address.id);
                        showSuccessMessage('Address added successfully');
                        modal.classList.add('hidden');
                        form.reset();
                        districtSelect.disabled = true;
                        districtSelect.innerHTML = '<option value="">First select province</option>';
                    } else {
                        displayErrors(data.message);
                    }
                })
                .catch(err => { console.error(err); if (modalErrorDiv) { modalErrorDiv.textContent = err.message || 'Network error'; modalErrorDiv.classList.remove('hidden'); } });
            });

            // Submit edit address
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearEditErrors();
                const addressId = document.getElementById('edit_address_id').value;
                const formData = new FormData(editForm);
                fetch(`/addresses/${addressId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                })
                .then(async res => {
                    const text = await res.text();
                    let data;
                    try { data = JSON.parse(text); } catch(e) { throw new Error('Invalid response'); }
                    if (!res.ok) {
                        if (res.status === 422 && data.errors) displayErrors(data.errors, editModalErrorDiv, '.edit-error-message');
                        else displayErrors(data.message || 'Failed to update address', editModalErrorDiv, '.edit-error-message');
                        throw new Error(data.message);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        refreshAddressDropdown(addressId);
                        showSuccessMessage('Address updated successfully');
                        editModal.classList.add('hidden');
                        if (!manageModal.classList.contains('hidden')) loadManageAddresses();
                    } else {
                        displayErrors(data.message, editModalErrorDiv, '.edit-error-message');
                    }
                })
                .catch(err => { console.error(err); if (editModalErrorDiv) { editModalErrorDiv.textContent = err.message || 'Network error'; editModalErrorDiv.classList.remove('hidden'); } });
            });
            
        });
    </script>
</x-frontend.layout>