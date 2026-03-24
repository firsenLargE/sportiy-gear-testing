<x-app-layout>
    <div class="p-6 grid grid-cols-2 gap-6">

        <!-- Images -->
        <div>
            @foreach ($product->variants as $variant)
                @foreach ($variant->images as $image)
                    <img src="{{ $image->url }}" class="mb-2 rounded">
                @endforeach
            @endforeach
        </div>

        <!-- Product Details -->
        <div>
            <h1 class="text-2xl font-bold">{{ $product->name }}</h1>

            <p class="text-xl text-green-600 mt-2">
                Rs. {{ $product->variants->first()->price ?? 'N/A' }}
            </p>

            <!-- Attributes (example: color, size) -->
            @foreach ($product->variants->first()->attributeValues as $attr)
                <p>
                    <strong>{{ $attr->attribute->name }}:</strong>
                    {{ $attr->value->value ?? '' }}
                </p>
            @endforeach

            <button class="mt-4 bg-green-500 text-white px-6 py-2 rounded">
                Add to Cart
            </button>
        </div>

    </div>
</x-app-layout>
