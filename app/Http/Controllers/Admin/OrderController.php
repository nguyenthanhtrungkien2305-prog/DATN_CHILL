<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> main

class OrderController extends Controller
{
    public function index(Request $request)
    {
<<<<<<< HEAD
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
=======
        // 1. Thống kê nhanh WIDGETS
        $countPending = DB::table('orders')->where('status', 'pending')->count();
        $countProcessing = DB::table('orders')->where('status', 'processing')->count();
        $countCompletedToday = DB::table('orders')
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->count();

        // 2. Xử lý Logic Truy vấn và Sắp xếp
        $query = DB::table('orders');
        $statusFilter = $request->get('status', 'incomplete'); // Mặc định hiển thị Chưa hoàn thành

        // Lọc theo Tab trạng thái
        if ($statusFilter === 'incomplete') {
            // Đơn chưa hoàn thành -> Lọc Pending & Processing -> Xếp Cũ nhất lên trước (asc)
            $query->whereIn('status', ['pending', 'processing'])
                  ->orderBy('created_at', 'asc');
        } elseif ($statusFilter !== 'all') {
            // Các Tab khác (Đã hoàn thành, Đã hủy) -> Xếp Mới nhất lên trước (desc)
            $query->where('status', $statusFilter)
                  ->orderBy('created_at', 'desc');
        } else {
            // Tab Tất cả -> Xếp Mới nhất lên trước
            $query->orderBy('created_at', 'desc');
        }

        // Tìm kiếm theo ID hoặc Tên khách
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%');
            });
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact(
            'orders', 'countPending', 'countProcessing', 'countCompletedToday', 'statusFilter'
        ));
    }

   // Cập nhật tiến độ đơn hàng (Đã khóa quy tắc luồng)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order = DB::table('orders')->where('order_id', $id)->first();
        
        if (!$order) {
            return back()->with('error', 'Không tìm thấy đơn hàng!');
        }

        $currentStatus = $order->status;
        $newStatus = $request->status;

        // BẢNG QUY TẮC LUỒNG TRẠNG THÁI (State Machine)
        $validTransitions = [
            // Từ "Chờ xác nhận": Chỉ được sang "Đang pha chế" hoặc "Hủy"
            'pending'    => ['processing', 'cancelled'], 
            
            // Từ "Đang pha chế": Bắt buộc phải tiến tới "Hoàn thành" (Không được lùi, không được hủy)
            'processing' => ['completed'],               
            
            // Đã "Hoàn thành": Trạng thái đóng băng (Khóa vĩnh viễn)
            'completed'  => [],                          
            
            // Đã "Hủy": Trạng thái đóng băng (Khóa vĩnh viễn)
            'cancelled'  => []                           
        ];

        // Kiểm tra xem trạng thái mới có nằm trong danh sách được phép nhảy tới không
        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            return back()->with('error', 'Thao tác từ chối! Bạn không thể nhảy cóc, lùi bước hoặc tự ý hủy đơn hàng đang xử lý.');
        }

        DB::table('orders')->where('order_id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now('Asia/Ho_Chi_Minh')
        ]);

        return back()->with('success', 'Đã cập nhật tiến độ cho đơn hàng #' . $id);
    }
}
>>>>>>> main
