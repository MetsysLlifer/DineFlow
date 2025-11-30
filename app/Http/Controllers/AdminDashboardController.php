<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use Carbon\CarbonPeriod;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        $usersCount = User::count();
        $ordersCount = Order::count();
        $revenue = Order::sum('total');
        return view('admin.dashboard', compact('usersCount','ordersCount','revenue'));
    }

    public function logs(Request $request)
    {
        $tab = $request->query('tab','users');
        if ($tab === 'food') {
            $logs = ActivityLog::whereIn('action', [
                'products.view','products.list','cart.add','cart.remove','product.update','order.submit','order.approve','order.reject','order.ready'
            ])->latest()->paginate(25)->withQueryString();
        } else {
            $logs = ActivityLog::whereIn('action', [
                'cashier.dashboard.view','cashier.orders.list','admin.login','order.approve','order.reject','order.ready'
            ])->latest()->paginate(25)->withQueryString();
        }
        return view('admin.logs', compact('logs','tab'));
    }

    public function profit()
    {
        return view('admin.profit');
    }

    public function profitData()
    {
        // Last 14 days revenue (gross total)
        $period = CarbonPeriod::create(now()->subDays(13)->startOfDay(), now()->endOfDay());
        $data = [];
        foreach ($period as $day) {
            $total = Order::whereDate('created_at', $day)->sum('total');
            $data[] = [
                'date' => $day->format('Y-m-d'),
                'total' => (float) $total
            ];
        }
        return response()->json(['days' => $data]);
    }

    public function roles()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.roles', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:admin,manager,cashier,kitchen,host,analyst,customer'
        ]);
        $user->update(['role' => $request->input('role')]);
        return redirect()->back()->with('status','Role updated');
    }
}
