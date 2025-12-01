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
        <div class="flex items-center justify-between mb-4 gap-3">
            <h1 class="text-3xl font-bold text-gray-800">Orders</h1>
            <div class="flex items-center gap-2">
                <input id="approve-code-input" type="text" maxlength="6" placeholder="Enter code" class="w-36 px-3 py-2 border rounded" />
                <button id="approve-code-btn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Accept</button>
            </div>
        </div>

        <div class="flex border-b mb-4">
            <button id="tab-pending" class="px-4 py-2 border-b-2 border-indigo-600 text-indigo-700 font-semibold">Pending Queue</button>
            <button id="tab-unapproved" class="px-4 py-2 text-gray-600">Unapproved (codes)</button>
        </div>

        <div id="pending-pane" class="grid grid-cols-1 gap-4" aria-live="polite"></div>
        <div id="unapproved-pane" class="hidden grid grid-cols-1 gap-2" aria-live="polite"></div>
    </main>

    <script>
        const apiPending = '{{ route("cashier.orders") }}';
        const apiUnapproved = '{{ route("cashier.orders.unapproved") }}';
        const apiApproveCode = '{{ route("cashier.approve.code") }}';
        const approveUrl = '{{ route("cashier.approve", ":id") }}';
        const rejectUrl = '{{ route("cashier.reject", ":id") }}';
        const readyUrl = '{{ route("cashier.ready", ":id") }}';
        const csrfToken = '{{ csrf_token() }}';

        async function loadOrders() {
            try {
                const [resP, resU] = await Promise.all([fetch(apiPending), fetch(apiUnapproved)]);
                const dataP = await resP.json();
                const dataU = await resU.json();
                displayPending(dataP.orders || []);
                displayUnapproved(dataU.orders || []);
            } catch (err) {
                console.error('Error loading orders:', err);
                document.getElementById('pending-pane').innerHTML = "<p class='text-red-600'>Error loading orders.</p>";
            }
        }

        function displayPending(orders) {
            const container = document.getElementById('pending-pane');
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
                        <button class="ready-btn flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700" data-id="${order.id}">Serve</button>
                        <button class="reject-btn flex-1 bg-red-600 text-white py-2 rounded hover:bg-red-700" data-id="${order.id}">Reject</button>
                    </div>
                `;
                container.appendChild(orderCard);
            });

            // Wire buttons
            document.querySelectorAll('.ready-btn').forEach(btn =>
                btn.addEventListener('click', () => markReady(btn.dataset.id))
            );
            document.querySelectorAll('.reject-btn').forEach(btn =>
                btn.addEventListener('click', () => rejectOrder(btn.dataset.id))
            );
        }

        function displayUnapproved(orders) {
            const container = document.getElementById('unapproved-pane');
            container.innerHTML = '';
            if (orders.length === 0) {
                container.innerHTML = "<p class='text-gray-600'>No unapproved orders.</p>";
                return;
            }
            orders.forEach(o => {
                const card = document.createElement('div');
                card.className = 'bg-white p-4 rounded border flex items-center justify-between';
                card.innerHTML = `
                    <div>
                        <div class="font-semibold">${o.order_number}</div>
                        <div class="text-sm text-gray-600">Code: <span class="font-mono">${o.code}</span></div>
                    </div>
                    <div class="text-xs text-gray-500">${new Date(o.created_at).toLocaleString()}</div>
                `;
                container.appendChild(card);
            });
        }

        async function approveCode() {
            const code = (document.getElementById('approve-code-input').value || '').trim();
            if (code.length !== 6) { alert('Enter 6-digit code'); return; }
            try {
                const res = await fetch(apiApproveCode, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ code })
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('approve-code-input').value = '';
                    loadOrders();
                } else {
                    alert(data.message || 'Code not found');
                }
            } catch (err) {
                console.error('Error approving by code:', err);
                alert('Error approving by code');
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

        // Tabs and events
        document.addEventListener('DOMContentLoaded', () => {
            loadOrders();
            document.getElementById('approve-code-btn').addEventListener('click', approveCode);
            document.getElementById('tab-pending').addEventListener('click', () => {
                document.getElementById('pending-pane').classList.remove('hidden');
                document.getElementById('unapproved-pane').classList.add('hidden');
                document.getElementById('tab-pending').classList.add('border-indigo-600','text-indigo-700','font-semibold');
                document.getElementById('tab-unapproved').classList.remove('border-indigo-600','text-indigo-700','font-semibold');
            });
            document.getElementById('tab-unapproved').addEventListener('click', () => {
                document.getElementById('pending-pane').classList.add('hidden');
                document.getElementById('unapproved-pane').classList.remove('hidden');
                document.getElementById('tab-unapproved').classList.add('border-indigo-600','text-indigo-700','font-semibold');
                document.getElementById('tab-pending').classList.remove('border-indigo-600','text-indigo-700','font-semibold');
            });

            // Real-time refresh via Echo
            if (window.Echo) {
                window.Echo.channel('orders').listen('.order.status', () => loadOrders());
            } else {
                setInterval(loadOrders, 5000);
            }
        });
    </script>
</body>
</html>
