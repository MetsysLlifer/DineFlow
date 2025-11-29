<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Page title (can be overridden by child views) --}}
    <title>@yield('title', 'POS Menu System')</title>

    {{-- External styles (Tailwind used for utility classes) --}}
    <link
        href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Compiled application CSS (public/css/app.css) --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">

    {{-- Header --}}
    <header class="header" role="banner">
        <div class="logo"></div>
    </header>

    {{-- Main: products (left) and cart (right) --}}
    <main class="flex h-screen bg-gray-50" role="main" aria-label="Main content">
        {{-- Products column --}}
        <section class="flex-1 p-6 overflow-auto" aria-label="Products">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Menu</h1>
            </div>

            <input
                id="search-box"
                class="search-box"
                type="text"
                placeholder="🔍 Search item ..."
                aria-label="Search products"
            >

            <div id="product-list" class="grid grid-cols-4 gap-4" aria-live="polite"></div>
        </section>

        {{-- Cart column --}}
        <aside class="w-80 bg-white p-6 border-l border-gray-300 overflow-auto" aria-label="Order summary">
            <div class="mb-4">
                <input
                    id="search-existing"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                    placeholder="🔍 Search in Existing..."
                    aria-label="Search cart"
                >
            </div>

            <div id="cart-items" class="space-y-2 mb-4" aria-live="polite"></div>

            <div class="border-t border-gray-300 pt-4 space-y-2 text-sm">
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

            <button class="checkout-btn mt-6" onclick="window.submitOrder()">PROCEED TO CHECKOUT</button>
        </aside>
    </main>

    {{-- Page-specific scripts --}}
    @yield('content')
</body>
</html>