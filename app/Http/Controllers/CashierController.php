<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    /**
     * Show the cashier dashboard with pending orders.
     */
    public function dashboard()
    {
        return view('cashier.dashboard');
    }

    /**
     * Get all pending orders as JSON (for dashboard refresh).
     */
    public function getPendingOrders()
    {
        $orders = Order::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->with('items')
            ->get();

        return response()->json(['orders' => $orders], 200);
    }

    /**
     * Approve an order.
     */
    public function approveOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $order->update(['status' => 'approved']);

        return response()->json(['success' => true, 'order' => $order], 200);
    }

    /**
     * Reject an order.
     */
    public function rejectOrder(Request $request, $orderId)
    {
        $request->validate(['reason' => 'nullable|string']);

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $order->update([
            'status' => 'rejected',
            'notes' => $request->input('reason')
        ]);

        return response()->json(['success' => true, 'order' => $order], 200);
    }

    /**
     * Mark order as ready for pickup.
     */
    public function markReady($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $order->update(['status' => 'ready']);

        return response()->json(['success' => true, 'order' => $order], 200);
    }
}
