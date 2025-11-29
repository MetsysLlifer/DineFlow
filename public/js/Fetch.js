// Fetch.js - Product and Cart Management (fallback client script)
// Requires window.App (routes + csrfToken) set by the Blade view.

(function () {
    // State
    let cart = {};
    let productsCache = [];

    // Helpers
    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Render products
    function displayProducts(products) {
        const productList = document.getElementById('product-list');
        if (!productList) return;
        productList.innerHTML = '';
        products.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';
            const img = product.image || 'https://via.placeholder.com/300x200?text=No+Image'; // defined
            card.innerHTML = `
                <img src="${escapeHtml(img)}" alt="${escapeHtml(product.name)}" class="product-img">
                <div class="product-name">${escapeHtml(product.name)}</div>
                <div class="product-price">₱ ${parseFloat(product.price).toFixed(2)}</div>
                <button class="add-btn" data-id="${product.id}" title="Add to cart">+</button>
            `;
            productList.appendChild(card);
        });
        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', () => addToCart(btn.dataset.id));
        });
    }

    // Fetch products
    async function fetchProducts() {
        try {
            const res = await fetch(window.App.routes.productsGet);
            if (!res.ok) throw new Error('Products fetch failed'); // fixed
            const data = await res.json();
            productsCache = data.products || [];
            displayProducts(productsCache);
            await loadCart();
            attachProductSearch();
            attachCartSearch();
        } catch (err) {
            console.error('Error fetching products:', err);
            const productList = document.getElementById('product-list');
            if (productList) productList.innerHTML = "<p class='text-red-600'>Error loading products.</p>";
        }
    }

    // Cart operations
    async function loadCart() {
        try {
            const res = await fetch(window.App.routes.cartGet);
            const data = await res.json();
            cart = data.cart || {};
            updateCartUI();
        } catch (err) {
            console.error('Error loading cart:', err);
        }
    }

    async function addToCart(productId) {
        try {
            const res = await fetch(window.App.routes.cartAdd, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.App.csrfToken
                },
                body: JSON.stringify({ product_id: productId })
            });
            const data = await res.json();
            if (data.success) {
                cart = data.cart || {};
                updateCartUI();
            }
        } catch (err) {
            console.error('Error adding to cart:', err);
        }
    }

    async function removeFromCart(productId) {
        try {
            const res = await fetch(window.App.routes.cartRemove, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.App.csrfToken
                },
                body: JSON.stringify({ product_id: productId })
            });
            const data = await res.json();
            if (data.success) {
                cart = data.cart || {};
                updateCartUI();
            }
        } catch (err) {
            console.error('Error removing from cart:', err);
        }
    }

    function updateQuantity(productId, action) {
        if (!cart[productId]) return;
        if (action === 'plus') cart[productId].quantity++;
        if (action === 'minus' && cart[productId].quantity > 1) cart[productId].quantity--;
        updateCartUI();
    }

    // Render cart
    function updateCartUI(filter = '') {
        const cartItemsEl = document.getElementById('cart-items');
        const subtotalEl = document.getElementById('subtotal');
        const totalEl = document.getElementById('total');
        if (!cartItemsEl || !subtotalEl || !totalEl) return;
        cartItemsEl.innerHTML = '';
        let subtotal = 0;
        Object.keys(cart).forEach(id => {
            const item = cart[id];
            if (filter && !item.name.toLowerCase().includes(filter)) return;
            subtotal += item.price * item.quantity;
            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item';
            itemEl.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <span class="cart-item-name">${escapeHtml(item.name)}</span>
                    <button class="cart-item-delete" data-id="${id}">Remove</button>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span>₱ ${parseFloat(item.price).toFixed(2)}</span>
                    <div class="cart-item-qty">
                        <button class="qty-btn" data-id="${id}" data-action="minus">−</button>
                        <span>${item.quantity}</span>
                        <button class="qty-btn" data-id="${id}" data-action="plus">+</button>
                    </div>
                </div>
            `;
            cartItemsEl.appendChild(itemEl);
        });
        subtotalEl.textContent = `₱ ${subtotal.toFixed(2)}`;
        totalEl.textContent = `₱ ${subtotal.toFixed(2)}`;
        document.querySelectorAll('.cart-item-delete').forEach(btn =>
            btn.addEventListener('click', () => removeFromCart(btn.dataset.id))
        );
        document.querySelectorAll('.qty-btn').forEach(btn =>
            btn.addEventListener('click', () => updateQuantity(btn.dataset.id, btn.dataset.action))
        );
    }

    // Search handlers
    function attachProductSearch() {
        const search = document.getElementById('search-box');
        if (!search) return;
        search.addEventListener('input', e => {
            const q = e.target.value.trim().toLowerCase();
            const filtered = productsCache.filter(p => p.name.toLowerCase().includes(q));
            displayProducts(filtered);
        });
    }

    function attachCartSearch() {
        const search = document.getElementById('search-existing');
        if (!search) return;
        search.addEventListener('input', e => {
            const q = e.target.value.trim().toLowerCase();
            updateCartUI(q);
        });
    }

    // Order submission
    async function submitOrder() {
        if (Object.keys(cart).length === 0) {
            alert('Cart is empty. Add items before checkout.');
            return;
        }
        const items = Object.keys(cart).map(id => {
            const item = cart[id];
            return { product_id: id, name: item.name, price: item.price, quantity: item.quantity };
        });
        let total = 0;
        items.forEach(i => { total += i.price * i.quantity; });

        try {
            const res = await fetch(window.App.routes.submitOrder, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.App.csrfToken },
                body: JSON.stringify({ items: items, total: total })
            });
            const data = await res.json();
            if (data.success) {
                alert(`Order submitted! Order #${data.order_number}`);
                cart = {};
                updateCartUI();
            }
        } catch (err) {
            console.error('Error submitting order:', err);
            alert('Error submitting order');
        }
    }

    // Expose API
    window.fetchProducts = fetchProducts;
    window.addToCart = addToCart;
    window.removeFromCart = removeFromCart;
    window.updateCartUI = updateCartUI;
    window.submitOrder = submitOrder;

    // Init
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fetchProducts);
        } else {
            fetchProducts();
        }
    }
    init();
})();