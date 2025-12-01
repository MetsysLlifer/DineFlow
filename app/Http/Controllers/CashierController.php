<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Events\OrderStatusChanged;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class CashierController extends Controller
{
    /**
     * Show the cashier dashboard with pending orders.
     */
    public function dashboard()
    {
        ActivityLogger::log('cashier.dashboard.view');
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
        ActivityLogger::log('cashier.orders.list', Order::class, null, ['count' => $orders->count()]);
        return response()->json(['orders' => $orders], 200);
    }

    /**
     * Get all unapproved orders as JSON.
     */
    public function getUnapprovedOrders()
    {
        $orders = Order::where('status', 'unapproved')
            ->orderBy('created_at', 'desc')
            ->get(['id','order_number','code','status','created_at']);
        return response()->json(['orders' => $orders], 200);
    }

    /**
     * Approve an unapproved order by code -> moves to pending (queue).
     */
    public function approveByCode(Request $request)
    {
        $data = $request->validate(['code' => ['required','string','size:6']]);
        $order = Order::where('code', $data['code'])->where('status', 'unapproved')->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Code not found or already processed'], 404);
        }
        $order->status = 'pending';
        $order->save();
        event(new OrderStatusChanged($order));
        ActivityLogger::log('order.accept', Order::class, $order->id, ['from' => 'unapproved', 'to' => 'pending']);
        return response()->json(['success' => true, 'order' => $order], 200);
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

            $order->status = 'approved';
            $order->save();
            event(new OrderStatusChanged($order));
        ActivityLogger::log('order.approve', Order::class, $order->id, ['status' => 'approved']);
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

            $order->status = 'rejected';
            $order->notes = $request->input('reason');
            $order->save();
            event(new OrderStatusChanged($order));
        ActivityLogger::log('order.reject', Order::class, $order->id, ['reason' => $request->input('reason')]);
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

            $order->status = 'ready';
            $order->save();
            event(new OrderStatusChanged($order));
        ActivityLogger::log('order.ready', Order::class, $order->id);
        return response()->json(['success' => true, 'order' => $order], 200);
    }
}
