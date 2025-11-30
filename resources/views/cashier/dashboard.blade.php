<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="header bg-blue-600 text-white">
        <div class="flex justify-between items-center">
            <div class="logo">Cashier Dashboard</div>
            <a href="{{ url('/') }}" class="text-sm hover:underline">Back to Menu</a>
        </div>
    </header>

    <!-- Main content -->
    <main class="p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Pending Orders</h1>

        <div id="orders-container" class="grid grid-cols-1 gap-4">
            <p class="text-gray-600">Loading orders...</p>
        </div>
    </main>

    <script>
        const apiBaseUrl = '{{ route("cashier.orders") }}';
        const approveUrl = '{{ route("cashier.approve", ":id") }}';
        const rejectUrl = '{{ route("cashier.reject", ":id") }}';
        const readyUrl = '{{ route("cashier.ready", ":id") }}';
        const csrfToken = '{{ csrf_token() }}';

        async function loadOrders() {
            try {
                const res = await fetch(apiBaseUrl);
                const data = await res.json();
                displayOrders(data.orders || []);
            } catch (err) {
                console.error('Error loading orders:', err);
                document.getElementById('orders-container').innerHTML = "<p class='text-red-600'>Error loading orders.</p>";
            }
        }

        function displayOrders(orders) {
            const container = document.getElementById('orders-container');
            container.innerHTML = '';

            if (orders.length === 0) {
                container.innerHTML = "<p class='text-gray-600 text-lg'>No pending orders.</p>";
                return;
            }

            orders.forEach(order => {
                const itemsHtml = order.items.map(item =>
                    `<tr><td class="py-2">${item.product_name}</td><td>${item.quantity}x</td><td>₱${parseFloat(item.price).toFixed(2)}</td><td>₱${parseFloat(item.subtotal).toFixed(2)}</td></tr>`
                ).join('');

                const orderCard = document.createElement('div');
                orderCard.className = 'bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500';
                orderCard.innerHTML = `
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">${order.order_number}</h2>
                            <p class="text-sm text-gray-600">${new Date(order.created_at).toLocaleString()}</p>
                        </div>
                        <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm font-bold">${order.status.toUpperCase()}</span>
                    </div>

                    <table class="w-full mb-4 text-sm border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-2">Item</th>
                                <th class="text-left py-2">Qty</th>
                                <th class="text-left py-2">Price</th>
                                <th class="text-left py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>

                    <div class="flex justify-end mb-4">
                        <p class="text-lg font-bold">Total: <span class="text-blue-600">₱${parseFloat(order.total).toFixed(2)}</span></p>
                    </div>

                    <div class="flex gap-2">
                        <button class="approve-btn flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700" data-id="${order.id}">Approve</button>
                        <button class="ready-btn flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700" data-id="${order.id}">Ready</button>
                        <button class="reject-btn flex-1 bg-red-600 text-white py-2 rounded hover:bg-red-700" data-id="${order.id}">Reject</button>
                    </div>
                `;
                container.appendChild(orderCard);
            });

            // Wire buttons
            document.querySelectorAll('.approve-btn').forEach(btn =>
                btn.addEventListener('click', () => approveOrder(btn.dataset.id))
            );
            document.querySelectorAll('.ready-btn').forEach(btn =>
                btn.addEventListener('click', () => markReady(btn.dataset.id))
            );
            document.querySelectorAll('.reject-btn').forEach(btn =>
                btn.addEventListener('click', () => rejectOrder(btn.dataset.id))
            );
        }

        async function approveOrder(orderId) {
            try {
                const res = await fetch(approveUrl.replace(':id', orderId), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    loadOrders();
                }
            } catch (err) {
                console.error('Error approving order:', err);
                alert('Error approving order');
            }
        }

        async function markReady(orderId) {
            try {
                const res = await fetch(readyUrl.replace(':id', orderId), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    loadOrders();
                }
            } catch (err) {
                console.error('Error marking ready:', err);
                alert('Error marking order as ready');
            }
        }

        async function rejectOrder(orderId) {
            const reason = prompt('Enter rejection reason (optional):');
            try {
                const res = await fetch(rejectUrl.replace(':id', orderId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ reason: reason || '' })
                });
                const data = await res.json();
                if (data.success) {
                    loadOrders();
                }
            } catch (err) {
                console.error('Error rejecting order:', err);
                alert('Error rejecting order');
            }
        }

        // Load orders on page load and refresh every 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            loadOrders();
            setInterval(loadOrders, 5000);
        });
    </script>
</body>
</html>
