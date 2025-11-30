<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class OrderController extends Controller
{
    /**
     * Submit a new order from the customer cart.
     * Expects JSON: { items: [{product_id, quantity, price, name}, ...], total }
     */
    public function submit(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'total' => 'required|numeric|min:0'
        ]);

        $items = $request->input('items');
        $total = (float) $request->input('total');

        // Generate unique order number
        $orderNumber = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);

        // Create the order
        $order = Order::create([
            'order_number' => $orderNumber,
            'subtotal' => $total,
            'discount' => 0,
            'total' => $total,
            'status' => 'pending'
        ]);

        // Add items to order
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['name'],
                'price' => (float) $item['price'],
                'quantity' => (int) $item['quantity'],
                'subtotal' => (float) $item['price'] * (int) $item['quantity']
            ]);
        }

        // Clear session cart
        session()->forget('cart');

        ActivityLogger::log('order.submit', Order::class, $order->id, [
            'order_number' => $orderNumber,
            'total' => $total,
            'items_count' => count($items),
        ]);

        return response()->json([
            'success' => true,
            'order_number' => $orderNumber,
            'order_id' => $order->id
        ], 201);
    }

    /**
     * Get order status by order number (customer tracking).
     */
    public function getStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'items' => $order->items
        ], 200);
    }
}
