import './bootstrap';

// State
let cart = {};
let productsCache = [];
let activeCategory = 'All';

const COMMON_CATEGORIES = ['Mains', 'Sides', 'Drinks', 'Desserts', 'Specials', 'Breakfast'];

// Utils
function escapeHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Products
function displayProducts(products) {
    const productsContainer = document.getElementById('product-list');
    if (!productsContainer) return;
    productsContainer.innerHTML = '';

    products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'shadow-sm hover:shadow-md transition-shadow rounded-lg overflow-hidden bg-white';

        const imageUrl = product.image && product.image.startsWith('http')
            ? product.image
            : (product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/200?text=No+Image');

                card.innerHTML = `
                        <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(product.name)}" class="w-full h-48 object-cover">
                        <div class="p-3 space-y-1">
                            <div class="product-name font-semibold">${escapeHtml(product.name)}</div>
                            <div class="text-xs text-gray-500">${escapeHtml(product.category || '')}</div>
                            <div class="product-price font-medium">₱ ${parseFloat(product.price).toFixed(2)}</div>
                            <button class="add-btn mt-2 w-full px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700" data-id="${product.id}" title="Add to cart">Add to Cart</button>
                        </div>
                `;

        productsContainer.appendChild(card);
    });

    document.querySelectorAll('.add-btn').forEach(btn => {
        btn.addEventListener('click', () => addToCart(btn.dataset.id));
    });
}

// Cart
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
            <div class="cart-item-header">
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

async function removeFromCart(id) {
    try {
        const res = await fetch(window.App.routes.cartRemove, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.App.csrfToken
            },
            body: JSON.stringify({ product_id: id })
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

async function updateQuantity(id, action) {
    try {
        const res = await fetch(window.App.routes.cartUpdate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.App.csrfToken
            },
            body: JSON.stringify({ product_id: id, action })
        });
        const data = await res.json();
        if (data.success) {
            cart = data.cart || {};
            updateCartUI();
        }
    } catch (err) {
        console.error('Error updating quantity:', err);
    }
}

// Search helpers
function attachProductSearch() {
    const search = document.getElementById('search-box');
    if (!search) return;
    search.addEventListener('input', e => {
        const q = e.target.value.trim().toLowerCase();
        let list = productsCache;
        if (activeCategory && activeCategory !== 'All') {
            list = list.filter(p => (p.category || '').toLowerCase() === activeCategory.toLowerCase());
        }
        if (q) {
            list = list.filter(p => p.name.toLowerCase().includes(q));
        }
        displayProducts(list);
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

// Fetch products
async function fetchProducts(category = null) {
    try {
        const url = category && category !== 'All' ? `/api/products?category=${encodeURIComponent(category)}` : '/api/products';
        const res = await fetch(url);
        const data = await res.json();
        productsCache = Array.isArray(data) ? data : (data.products || []);
        displayProducts(productsCache);

        await loadCart();
        attachProductSearch();
        attachCartSearch();

        renderCategoryButtons();
    } catch (err) {
        console.error('Error fetching products:', err);
        const productList = document.getElementById('product-list');
        if (productList) {
            productList.innerHTML = "<p class='text-red-600'>Error loading products. Please try again.</p>";
        }
    }
}

function renderCategoryButtons() {
    const categoryButtons = document.getElementById('category-buttons');
    const categorySelect = document.getElementById('category-select');
    if (!categoryButtons && !categorySelect) return;
    const dynamic = Array.from(new Set(productsCache.map(p => p.category).filter(Boolean)));
    const unique = Array.from(new Set([ ...COMMON_CATEGORIES, ...dynamic ])).sort();

    if (categoryButtons) {
        categoryButtons.innerHTML = '';
        const allBtn = document.createElement('button');
        allBtn.className = 'px-3 py-1 rounded border' + (activeCategory === 'All' ? ' bg-indigo-600 text-white border-indigo-600' : ' hover:bg-indigo-50');
        allBtn.textContent = 'All';
        allBtn.addEventListener('click', () => { activeCategory = 'All'; attachProductSearch(); displayProducts(productsCache); highlightActive(); if (categorySelect) categorySelect.value = 'All'; });
        categoryButtons.appendChild(allBtn);
    }

    unique.forEach(cat => {
        if (categoryButtons) {
            const btn = document.createElement('button');
            btn.className = 'px-3 py-1 rounded border' + (activeCategory === cat ? ' bg-indigo-600 text-white border-indigo-600' : ' hover:bg-indigo-50');
            btn.textContent = cat;
            btn.addEventListener('click', () => {
                activeCategory = cat;
                const filtered = productsCache.filter(p => (p.category || '').toLowerCase() === cat.toLowerCase());
                displayProducts(filtered);
                highlightActive();
                if (categorySelect) categorySelect.value = cat;
            });
            categoryButtons.appendChild(btn);
        }
    });

    // Populate mobile dropdown
    if (categorySelect) {
        categorySelect.innerHTML = '';
        const opts = ['All', ...unique];
        opts.forEach(val => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.textContent = val;
            if (val === activeCategory) opt.selected = true;
            categorySelect.appendChild(opt);
        });
        categorySelect.onchange = () => {
            activeCategory = categorySelect.value;
            const list = activeCategory === 'All'
                ? productsCache
                : productsCache.filter(p => (p.category || '').toLowerCase() === activeCategory.toLowerCase());
            displayProducts(list);
            highlightActive();
        };
    }
}

function highlightActive() {
    const categoryButtons = document.getElementById('category-buttons');
    if (!categoryButtons) return;
    [...categoryButtons.querySelectorAll('button')].forEach(btn => {
        const isActive = btn.textContent === activeCategory || (activeCategory === 'All' && btn.textContent === 'All');
        btn.className = 'px-3 py-1 rounded border' + (isActive ? ' bg-indigo-600 text-white border-indigo-600' : ' hover:bg-indigo-50');
    });
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts(activeCategory);
});

// Subscribe to real-time events if Echo is available
if (window.Echo) {
    window.Echo.channel('products')
        .listen('.product.changed', (e) => {
            // Simple strategy: refetch products on any change
            fetchProducts(activeCategory);
        });

    window.Echo.channel('activity')
        .listen('.activity.logged', () => {
            // No-op here; admin logs page would subscribe itself
        });

    window.Echo.channel('orders')
        .listen('.order.status', () => {
            // Cashier/admin pages can add specific reactions; for client we ignore
        });
}

// Helpers for customer order status pill + polling
let orderPollTimer = null;
function ensureOrderPill() {
    let pill = document.getElementById('customer-order-pill');
    if (!pill) {
        const header = document.querySelector('header.header .flex.items-center.gap-4')
            || document.querySelector('header.header .flex.justify-between .flex');
        if (header) {
            pill = document.createElement('div');
            pill.id = 'customer-order-pill';
            pill.className = 'flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm';
            pill.innerHTML = '<span id="customer-order-code"></span> <span id="customer-order-status" class="text-xs px-2 py-0.5 rounded bg-indigo-100">UNAPPROVED</span>';
            header.prepend(pill);
        }
    }
    return pill;
}
function setOrderPill(code, statusLabel) {
    const pill = ensureOrderPill();
    if (!pill) return;
    pill.style.display = 'flex';
    const codeEl = document.getElementById('customer-order-code');
    const statusEl = document.getElementById('customer-order-status');
    if (codeEl) codeEl.textContent = code || '';
    if (statusEl) statusEl.textContent = statusLabel || '';
}

// Order submission (client)
async function submitOrder() {
    if (!window.App || !window.App.routes || !window.App.routes.submitOrder) return;
    if (!cart || Object.keys(cart).length === 0) {
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
            body: JSON.stringify({ items, total })
        });
        const data = await res.json();
        if (data.success) {
            // Persist and show pill
            localStorage.setItem('df_last_order', JSON.stringify({ number: data.order_number, code: data.code }));
            setOrderPill(data.code, 'UNAPPROVED');

            alert(`Order submitted! Your code: ${data.code}`);
            cart = {};
            updateCartUI();

            // Start resilient tracking
            startOrderTracking(data.order_number);
        } else {
            alert('Error submitting order');
        }
    } catch (e) {
        console.error('Error submitting order:', e);
        alert('Error submitting order');
    }
}

window.submitOrder = submitOrder;

async function watchOrderStatus(orderNumber) {
    try {
        const url = `/api/orders/${encodeURIComponent(orderNumber)}/status`;
        const res = await fetch(url);
        if (!res.ok) return null;
        const data = await res.json();
        if (!data.success) return null;
        const status = (data.order.status || '').toLowerCase();
        const saved = JSON.parse(localStorage.getItem('df_last_order') || '{}');

        if (status === 'pending') {
            setOrderPill(saved.code || '', 'APPROVED');
        } else if (status === 'ready' || status === 'served' || status === 'rejected') {
            const pill = document.getElementById('customer-order-pill');
            if (pill) pill.style.display = 'none';
            localStorage.removeItem('df_last_order');
        }
        return status;
    } catch (_) {
        return null;
    }
}

// Subscribe to order status changes to update customer pill
if (window.Echo) {
    window.Echo.channel('orders')
        .listen('.order.status', (e) => {
            const saved = JSON.parse(localStorage.getItem('df_last_order') || '{}');
            if (e && e.order && saved && saved.number && e.order.order_number === saved.number) {
                watchOrderStatus(saved.number);
            }
        });
}

function startOrderTracking(orderNumber) {
    if (orderPollTimer) {
        clearInterval(orderPollTimer);
        orderPollTimer = null;
    }
    // Immediate check
    watchOrderStatus(orderNumber);
    // Poll every 4s as fallback to broadcasts
    orderPollTimer = setInterval(async () => {
        const status = await watchOrderStatus(orderNumber);
        if (status === 'ready' || status === 'served' || status === 'rejected') {
            clearInterval(orderPollTimer);
            orderPollTimer = null;
        }
    }, 4000);
}

// Restore tracking on page load (SSR/session or localStorage cases)
document.addEventListener('DOMContentLoaded', () => {
    const saved = JSON.parse(localStorage.getItem('df_last_order') || '{}');
    if (saved && saved.number && saved.code) {
        setOrderPill(saved.code, 'UNAPPROVED');
        startOrderTracking(saved.number);
        return;
    }
    if (window.CustomerOrder && window.CustomerOrder.order_number && window.CustomerOrder.code) {
        localStorage.setItem('df_last_order', JSON.stringify({ number: window.CustomerOrder.order_number, code: window.CustomerOrder.code }));
        setOrderPill(window.CustomerOrder.code, 'UNAPPROVED');
        startOrderTracking(window.CustomerOrder.order_number);
    }
});
