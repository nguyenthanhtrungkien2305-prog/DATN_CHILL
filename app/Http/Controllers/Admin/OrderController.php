<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->orderBy('created_at', 'desc');

        // Search by order_id, customer_name, customer_phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by order_type
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->input('order_type'));
        }

        // Filter by user_id (retrieve specific user orders)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->where('order_id', $id)
            ->first();

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng!');
        }

        // Parse items
        $order->items = json_decode($order->items, true) ?? [];

        // Linked user information
        $member = null;
        $memberOrderCount = 0;
        $memberTotalSpent = 0;

        if ($order->user_id) {
            $member = DB::table('users')
                ->where('user_id', $order->user_id)
                ->first();

            if ($member) {
                // Get all other orders by this member
                $memberOrders = DB::table('orders')
                    ->where('user_id', $order->user_id)
                    ->get();

                $memberOrderCount = $memberOrders->count();
                $memberTotalSpent = $memberOrders->where('status', 'completed')->sum('total_amount');
                
                // Parse addresses if stored in user table
                if ($member->address) {
                    $decodedAddr = json_decode($member->address, true);
                    $member->addresses = is_array($decodedAddr) ? $decodedAddr : [$member->address];
                } else {
                    $member->addresses = [];
                }
            }
        }

        // Voucher details
        $voucher = null;
        if ($order->voucher_id) {
            $voucher = DB::table('vouchers')
                ->where('voucher_id', $order->voucher_id)
                ->first();
        }

        return view('admin.orders.show', compact('order', 'member', 'memberOrderCount', 'memberTotalSpent', 'voucher'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,canceled'
        ]);

        $status = $request->input('status');

        $order = DB::table('orders')
            ->where('order_id', $id)
            ->first();

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng!');
        }

        // Check if the order status is changing from non-completed to completed
        $shouldRewardPoints = ($status === 'completed' && $order->status !== 'completed' && $order->user_id);

        DB::table('orders')
            ->where('order_id', $id)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);

        // Reward loyalty points
        if ($shouldRewardPoints) {
            $pointsEarned = floor($order->total_amount / 10000);
            if ($pointsEarned > 0) {
                DB::table('users')
                    ->where('user_id', $order->user_id)
                    ->increment('point', $pointsEarned);
            }
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
}
