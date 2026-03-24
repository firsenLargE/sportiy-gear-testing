<x-app-layout>
    <div class="flex gap-6 p-6">

        <!-- Sidebar: Categories -->
        <aside class="w-1/4 bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4 text-lg">Categories</h3>
            <ul>
                @foreach ($categories as $category)
                    <li class="mb-2">
                        <a href="{{ route('user.products', ['category' => $category->id]) }}"
                            class="block px-2 py-1 rounded hover:bg-blue-100
                              {{ request('category') == $category->id ? 'bg-blue-200 font-bold' : '' }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <!-- Products Grid -->
        <main class="w-3/4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div class="bg-white shadow rounded overflow-hidden flex flex-col">
                    <!-- Product Image -->
                    <a href="{{ route('user.product.show', $product->slug) }}">
                        <img src="{{ optional($product->variants->first()->images->first())->url ?? 'https://via.placeholder.com/300x200' }}"
                            class="w-full h-48 object-cover">
                    </a>

                    <!-- Product Info -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h2 class="font-semibold text-lg mb-1">{{ $product->name }}</h2>
                            <p class="text-gray-600 mb-2">Rs. {{ $product->variants->first()->price ?? 'N/A' }}</p>
                        </div>

                        <a href="{{ route('user.product.show', $product->slug) }}"
                            class="mt-2 inline-block bg-blue-500 text-white text-center px-4 py-2 rounded hover:bg-blue-600">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center text-gray-500">No products found.</p>
            @endforelse
        </main>
    </div>

    <!-- Pagination -->
    <div class="p-6 flex justify-center">
        {{ $products->links() }}
    </div>
</x-app-layout>
