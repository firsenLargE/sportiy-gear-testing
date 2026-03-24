<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">Products</h1>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($products as $product)
                <a href="{{ route('user.product.show', $product->slug) }}" class="border rounded p-4 hover:shadow">

                    @php
                        $image = $product->images->where('is_primary', 1)->first();
                    @endphp

                    @if ($image)
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                            class="w-full h-40 object-cover mb-3 rounded">
                    @endif

                    <h2 class="font-semibold">{{ $product->name }}</h2>

                </a>
            @endforeach
        </div>

    </div>
</x-app-layout>
