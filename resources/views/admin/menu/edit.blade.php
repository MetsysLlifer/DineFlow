<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <header class="header">
        <div class="flex justify-between items-center">
            <div class="logo">Admin • Edit Menu Item</div>
            <a href="{{ url('/products') }}" class="text-sm hover:underline">Back to Products</a>
        </div>
    </header>

    <main class="p-6 max-w-3xl mx-auto">
        @if (session('status'))
            <div class="bg-green-100 border border-green-200 text-green-800 p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h1 class="text-2xl font-bold mb-4 text-gray-900">Edit: {{ $product->name }}</h1>

            <form action="{{ route('admin.menu-items.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="search-box" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (₱)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="search-box" required>
                    @error('price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (optional)</label>
                    <input type="file" name="image" accept="image/*" class="search-box">
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    @if ($product->image)
                        @php
                            $imageSrc = \Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://'])
                                ? $product->image
                                : asset('storage/'.$product->image);
                        @endphp
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 mb-2">Current image preview:</p>
                            <img src="{{ $imageSrc }}" alt="{{ $product->name }}" class="w-40 h-40 object-cover rounded-lg border border-gray-200">
                        </div>
                    @endif
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="checkout-btn">Save Changes</button>
                    <a href="{{ url('/products') }}" class="qty-btn inline-flex items-center justify-center">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
