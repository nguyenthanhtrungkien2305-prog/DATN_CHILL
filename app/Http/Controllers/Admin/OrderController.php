<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // 1. Thống kê nhanh WIDGETS
        $countPending = DB::table('orders')->where('status', 'pending')->count();
        $countProcessing = DB::table('orders')->where('status', 'processing')->count();
        $countCompletedToday = DB::table('orders')
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->count();

        // 2. Xử lý Logic Truy vấn và Sắp xếp
        $query = DB::table('orders');
        $statusFilter = $request->get('status', 'all');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            if ($request->input('status') === 'incomplete') {
                $query->whereIn('status', ['pending', 'processing']);
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->input('order_type'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $query->orderBy('created_at', 'desc');

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact(
            'orders', 'countPending', 'countProcessing', 'countCompletedToday', 'statusFilter'
        ));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->where('order_id', $id)
            ->first();

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng!');
        }

        $order->items = json_decode($order->items, true) ?? [];

        $member = null;
        $memberOrderCount = 0;
        $memberTotalSpent = 0;

        if ($order->user_id) {
            $member = DB::table('users')
                ->where('user_id', $order->user_id)
                ->first();

            if ($member) {
                $memberOrders = DB::table('orders')
                    ->where('user_id', $order->user_id)
                    ->get();

                $memberOrderCount = $memberOrders->count();
                $memberTotalSpent = $memberOrders->where('status', 'completed')->sum('total_amount');
                
                if ($member->address) {
                    $decodedAddr = json_decode($member->address, true);
                    $member->addresses = is_array($decodedAddr) ? $decodedAddr : [$member->address];
                } else {
                    $member->addresses = [];
                }
            }
        }

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
            'status' => 'required|in:pending,processing,completed,canceled,cancelled'
        ]);

        $newStatus = $request->input('status');
        if ($newStatus === 'cancelled') {
            $newStatus = 'canceled';
        }

        $order = DB::table('orders')
            ->where('order_id', $id)
            ->first();

        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng!');
        }

        $shouldRewardPoints = ($newStatus === 'completed' && $order->status !== 'completed' && $order->user_id);

        DB::table('orders')
            ->where('order_id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now('Asia/Ho_Chi_Minh')
            ]);

        if ($shouldRewardPoints) {
            $pointsEarned = floor($order->total_amount / 10000);
            if ($pointsEarned > 0) {
                DB::table('users')
                    ->where('user_id', $order->user_id)
                    ->increment('point', $pointsEarned);
            }
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng #' . $id . ' thành công!');
    }
}
