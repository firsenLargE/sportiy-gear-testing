<x-frontend.layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Choose Payment Method</h1>

            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold">Order #{{ $order->order_number }}</h2>
                <div class="flex justify-between border-b pb-2 mt-2">
                    <span>Total Amount:</span>
                    <span class="font-bold text-orange-600 text-xl">Rs. {{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('payment.process') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" class="mr-3" required>
                            <div>
                                <p class="font-medium">Cash on Delivery</p>
                                <p class="text-sm text-gray-500">Pay when you receive the order</p>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer">
                            <input type="radio" name="payment_method" value="esewa" class="mr-3">
                            <div>
                                <p class="font-medium">eSewa</p>
                                <p class="text-sm text-gray-500">Pay via eSewa wallet</p>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer">
                            <input type="radio" name="payment_method" value="khalti" class="mr-3">
                            <div>
                                <p class="font-medium">Khalti</p>
                                <p class="text-sm text-gray-500">Pay via Khalti wallet</p>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700">
                    Confirm & Pay
                </button>
            </form>
        </div>
    </div>
</x-frontend.layout>
