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

        if ($newStatus === 'cancelled') {
            $usedWallet = (float)($order->used_wallet_amount ?? 0);
            $isPaid = ($currentStatus === 'processing' || $order->payment_method === 'qr');
            $cashPaid = $isPaid ? (float)$order->total_amount : 0;
            $totalRefund = $usedWallet + $cashPaid;

            if ($totalRefund > 0 && !empty($order->user_id)) {
                DB::table('users')->where('user_id', $order->user_id)->increment('wallet_balance', $totalRefund);
            }

            if (!empty($order->voucher_id) && !empty($order->user_id) && \Illuminate\Support\Facades\Schema::hasTable('user_vouchers')) {
                DB::table('user_vouchers')
                    ->where('user_id', $order->user_id)
                    ->where('voucher_id', $order->voucher_id)
                    ->where('is_used', 1)
                    ->limit(1)
                    ->update(['is_used' => 0, 'updated_at' => now()]);

                DB::table('vouchers')->where('voucher_id', $order->voucher_id)->decrement('used_count');
            }
        }

        DB::table('orders')->where('order_id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now('Asia/Ho_Chi_Minh')
        ]);

        return back()->with('success', 'Đã cập nhật tiến độ cho đơn hàng #' . $id);
    }
}