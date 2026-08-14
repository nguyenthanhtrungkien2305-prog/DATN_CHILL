<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = now('Asia/Ho_Chi_Minh');

        // Xác định chính xác Ca làm việc của NGAY LÚC NÀY
        $shiftIndex = floor($now->hour / 4) + 1;
        $startHour = ($shiftIndex - 1) * 4;
        $startTime = sprintf('%02d:00:00', $startHour);
        $endTime = ($startHour + 4 == 24) ? '23:59:59' : sprintf('%02d:00:00', $startHour + 4);

        $shift = Shift::firstOrCreate(
            ['date' => $now->format('Y-m-d'), 'start_time' => $startTime],
            [
                'name' => "Ca $shiftIndex (" . sprintf('%02d:00', $startHour) . " - " . sprintf('%02d:00', $startHour + 4 > 23 ? 0 : $startHour + 4) . ")",
                'end_time' => $endTime
            ]
        );
        $userId = $user->user_id ?? $user->id;

        $shift->load('users');
        $staffCount = $shift->users->count();
        $isUserInShift = $shift->users->contains($userId);

        // CHỈ LẤY ĐƠN HÀNG CỦA CA HIỆN TẠI
        $totalRevenue = DB::table('orders')
            ->where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $totalOrders = DB::table('orders')
            ->where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->count();

        // Tính quỹ hoa hồng 2.0% (Chỉ những nhân viên đã Check-in ca này mới được chia hoa hồng)
        $commissionRate = 0.02; 
        $shiftCommissionPool = $totalRevenue * $commissionRate;
        $myCommission = ($isUserInShift && $staffCount > 0) ? ($shiftCommissionPool / $staffCount) : 0;

        // Thống kê món
        $orders = DB::table('orders')->where('shift_id', $shift->id)->where('status', 'completed')->get();
        $productsSold = [];
        
        foreach($orders as $order) {
            $items = json_decode($order->items, true);
            if(is_array($items)) {
                foreach($items as $item) {
                    $pId = $item['productId'] ?? $item['product_id'] ?? 0;
                    if(!isset($productsSold[$pId])) {
                        $productsSold[$pId] = ['name' => $item['name'] ?? 'Sản phẩm', 'quantity' => 0, 'total' => 0];
                    }
                    $productsSold[$pId]['quantity'] += $item['quantity'];
                    $productsSold[$pId]['total'] += ($item['price'] * $item['quantity']); 
                }
            }
        }

        return view('staff.commission', compact('shift', 'staffCount', 'totalRevenue', 'totalOrders', 'shiftCommissionPool', 'myCommission', 'productsSold'));
    }
}