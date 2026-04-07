<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

            @if ($cartItems->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 21v-6"></path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Looks like you haven't added any items to your cart yet.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                        Continue Shopping
                    </a>
                </div>
            @else
                <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="GET">
                    @csrf
                    <!-- Hidden flag to indicate this is a cart selection checkout -->
                    <input type="hidden" name="selection_mode" value="cart">

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Cart Items -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-orange-600 rounded">
                                        <span class="text-sm font-medium text-gray-700">Select All</span>
                                    </label>
                                    <button type="button" id="removeSelectedBtn"
                                        class="text-red-600 text-sm hover:text-red-800 disabled:opacity-50" disabled>
                                        Remove Selected
                                    </button>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    @foreach ($cartItems as $item)
                                        <div class="p-6 cart-item" data-item-id="{{ $item->id }}">
                                            <div class="flex gap-4">
                                                <!-- Checkbox -->
                                                <div class="flex-shrink-0 pt-2">
                                                    <input type="checkbox" name="selected_items[]"
                                                        value="{{ $item->id }}"
                                                        class="item-checkbox w-4 h-4 text-orange-600 rounded">
                                                </div>

                                                <!-- Product Image -->
                                                <div
                                                    class="w-32 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                    @php
                                                        $image =
                                                            $item->variant->images
                                                                ->where('is_primary', true)
                                                                ->first() ?? $item->variant->images->first();
                                                    @endphp
                                                    <img src="{{ $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/128' }}"
                                                        alt="{{ $item->variant->product->name }}"
                                                        class="w-full h-full object-cover">
                                                </div>

                                                <!-- Product Details -->
                                                <div class="flex-1">
                                                    <div class="flex flex-col sm:flex-row sm:justify-between gap-2">
                                                        <div>
                                                            <h3 class="font-semibold text-gray-800">
                                                                {{ $item->variant->product->name }}
                                                            </h3>
                                                            <p class="text-sm text-gray-500">Variant:
                                                                {{ $item->variant->name ?? 'Standard' }}
                                                            </p>
                                                            @if ($item->variant->attributeValues->isNotEmpty())
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    @foreach ($item->variant->attributeValues as $attr)
                                                                        {{ $attr->attribute->name ?? 'Attribute' }}:
                                                                        {{ $attr->value }}@if (!$loop->last)
                                                                            ,
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="text-right">
                                                            @php
                                                                $originalPrice = $item->variant->price ?? 0;
                                                                $finalPrice = $item->final_price ?? $originalPrice;
                                                            @endphp
                                                            @if ($originalPrice > $finalPrice)
                                                                <p class="text-sm text-gray-400 line-through">Rs.
                                                                    {{ number_format($originalPrice, 2) }}</p>
                                                                <p class="text-lg font-bold text-orange-600 item-price"
                                                                    data-unit-price="{{ $finalPrice }}">
                                                                    Rs.
                                                                    {{ number_format($finalPrice * $item->quantity, 2) }}
                                                                </p>
                                                                <p class="text-xs text-green-600">✓ Discount applied</p>
                                                            @else
                                                                <p class="text-lg font-bold text-orange-600 item-price"
                                                                    data-unit-price="{{ $finalPrice }}">
                                                                    Rs.
                                                                    {{ number_format($finalPrice * $item->quantity, 2) }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Quantity and Actions -->
                                                    <div class="flex items-center justify-between mt-6">
                                                        <div
                                                            class="flex items-center border border-gray-300 rounded-lg">
                                                            <button type="button"
                                                                class="quantity-btn decrement px-4 py-2 text-gray-600 hover:bg-gray-100"
                                                                data-id="{{ $item->id }}">−</button>
                                                            <input type="number"
                                                                class="quantity-input w-16 text-center border-0 focus:ring-0 no-spinner"
                                                                value="{{ $item->quantity }}" min="1"
                                                                max="{{ $item->variant->stock_quantity ?? 10 }}"
                                                                data-id="{{ $item->id }}" readonly>
                                                            <button type="button"
                                                                class="quantity-btn increment px-4 py-2 text-gray-600 hover:bg-gray-100"
                                                                data-id="{{ $item->id }}">+</button>
                                                        </div>
                                                        <button type="button"
                                                            class="remove-item flex items-center gap-1 text-red-500 hover:text-red-700 text-sm font-medium"
                                                            data-id="{{ $item->id }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="lg:col-span-1 mt-6 lg:mt-0 space-y-3">
                            <button type="submit" id="checkoutBtn"
                                class="block w-full bg-orange-600 text-white text-center py-4 rounded-lg hover:bg-orange-700 transition font-semibold text-lg disabled:bg-gray-400 disabled:cursor-not-allowed"
                                disabled>
                                Proceed to Checkout
                            </button>
                            <a href="{{ route('products.index') }}"
                                class="block w-full border border-orange-600 text-orange-600 text-center py-4 rounded-lg hover:bg-orange-50 transition font-medium">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <style>
        .no-spinner::-webkit-outer-spin-button,
        .no-spinner::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .no-spinner {
            -moz-appearance: textfield;
        }
    </style>

    <script>
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const removeSelectedBtn = document.getElementById('removeSelectedBtn');

        function updateCheckoutButton() {
            const anyChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
            checkoutBtn.disabled = !anyChecked;
            removeSelectedBtn.disabled = !anyChecked;
        }

        function updateSelectAll() {
            const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        }

        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateCheckoutButton();
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateCheckoutButton();
                updateSelectAll();
            });
        });

        // Remove selected items
        if (removeSelectedBtn) {
            removeSelectedBtn.addEventListener('click', function() {
                const selectedIds = Array.from(itemCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                if (selectedIds.length === 0) return;
                if (confirm(`Remove ${selectedIds.length} item(s) from cart?`)) {
                    selectedIds.forEach(id => removeItem(id, true));
                }
            });
        }

        // Quantity update logic
        document.querySelectorAll('.increment').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.querySelector(`.quantity-input[data-id="${this.dataset.id}"]`);
                if (input) {
                    let newVal = parseInt(input.value) + 1;
                    if (newVal <= parseInt(input.max)) {
                        updateQuantity(this.dataset.id, newVal, input);
                    }
                }
            });
        });

        document.querySelectorAll('.decrement').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.querySelector(`.quantity-input[data-id="${this.dataset.id}"]`);
                if (input) {
                    let newVal = parseInt(input.value) - 1;
                    if (newVal >= 1) {
                        updateQuantity(this.dataset.id, newVal, input);
                    }
                }
            });
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Remove this item from cart?')) {
                    removeItem(this.dataset.id, false);
                }
            });
        });

        function updateQuantity(itemId, quantity, inputEl) {
            fetch(`/cart/update/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        inputEl.value = quantity;
                        const cartItem = inputEl.closest('.cart-item');
                        const priceEl = cartItem.querySelector('.item-price');
                        if (priceEl) {
                            const unitPrice = parseFloat(priceEl.dataset.unitPrice);
                            priceEl.textContent = 'Rs. ' + (unitPrice * quantity).toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    } else {
                        alert(data.message || 'Failed to update quantity');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function removeItem(itemId, isBulk = false) {
            fetch(`/cart/remove/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                        if (cartItem) cartItem.remove();
                        const remaining = document.querySelectorAll('.cart-item');
                        if (remaining.length === 0) {
                            location.reload();
                        } else if (!isBulk) {
                            updateCheckoutButton();
                            updateSelectAll();
                        }
                    } else {
                        alert('Failed to remove item');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>


    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.item-checkbox:checked');

            if (checked.length === 0) {
                e.preventDefault();
                alert('Please select at least one item to checkout');
            }
        });
    </script>

</x-frontend.layout>
