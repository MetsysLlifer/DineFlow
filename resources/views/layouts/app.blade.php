<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍽️</text></svg>">

    {{-- Page title (can be overridden by child views) --}}
    <title>@yield('title', 'DineFlow')</title>

    {{-- Runtime config for app.js (must load before Vite) --}}
    <script>
        window.App = window.App || {};
        @if(Route::has('products.get'))
        window.App.routes = {
            productsGet: '{{ route("products.get") }}',
            cartGet: '{{ route("cart.get") }}',
            cartAdd: '{{ route("cart.add") }}',
            cartRemove: '{{ route("cart.remove") }}',
            cartUpdate: '{{ route("cart.update") }}',
            submitOrder: '{{ route("orders.submit") }}'
        };
        window.App.csrfToken = '{{ csrf_token() }}';
        @endif
    </script>

    {{-- Centralized styles via Vite (Tailwind v4 + app styles) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Optional icon set (keep if used) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    {{-- Header --}}
    <x-header />

    {{-- Main: products (left) and cart (right) --}}
    <main class="flex flex-col md:flex-row md:h-screen bg-gray-50" role="main" aria-label="Main content">
        {{-- Products column --}}
        <section class="flex-1 p-6 overflow-auto pb-[22rem] md:pb-6" aria-label="Products">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Menu</h1>
            </div>

            <div class="mb-4 flex items-center justify-between gap-3">
                <div id="category-buttons" class="hidden md:flex flex-wrap gap-2"></div>
                <div class="md:hidden w-full">
                    <label for="category-select" class="sr-only">Select category</label>
                    <select id="category-select" class="w-full border rounded px-3 py-2">
                        <option value="All">All</option>
                    </select>
                </div>
            </div>
            <input
                id="search-box"
                class="search-box"
                type="text"
                placeholder="🔍 Search item ..."
                aria-label="Search products"
            >

            <div id="product-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" aria-live="polite"></div>
            {{-- Spacer to prevent overlap with floating cart on mobile --}}
            <div class="md:hidden h-72"></div>
        </section>

        {{-- Cart column (fixed floating bar on small screens, static sidebar on desktop) --}}
        <aside class="md:w-80 w-full bg-white p-4 md:p-6 md:border-l border-t md:border-t-0 border-gray-300 md:static fixed bottom-0 left-0 right-0 z-10 shadow-lg flex flex-col" aria-label="Order summary">
            <div class="mb-4">
                <input
                    id="search-existing"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                    placeholder="🔍 Search in Existing..."
                    aria-label="Search cart"
                >
            </div>

            <div id="cart-items" class="space-y-2 mb-4 overflow-y-auto pr-2" style="max-height: 30vh;" aria-live="polite"></div>

            <div class="border-t border-gray-300 pt-4 space-y-2 text-sm bg-white">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span id="subtotal" class="font-bold">₱ 00.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Product Discount:</span>
                    <span class="font-bold">- ₱ 00.00</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total:</span>
                    <span id="total">₱ 00.00</span>
                </div>
            </div>

            <button class="checkout-btn mt-4 md:mt-6" onclick="window.submitOrder()">PROCEED TO CHECKOUT</button>
        </aside>
    </main>

    {{-- Page-specific scripts --}}
    @yield('content')
</body>
</html>