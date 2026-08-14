<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')->get();

        $toppingCategory = DB::table('categories')->where('name', 'like', '%Topping%')->first();
        $toppingCatId = $toppingCategory ? $toppingCategory->category_id : null;

        $productsQuery = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
            ->groupBy('products.product_id');

        if ($toppingCatId) {
            $productsQuery->where('products.category_id', '!=', $toppingCatId);
        }
        $products = $productsQuery->get();

        $toppings = [];
        if ($toppingCatId) {
            $toppings = DB::table('products')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
                ->where('products.category_id', $toppingCatId)
                ->groupBy('products.product_id')
                ->get();
        }

        $combos = DB::table('combos')
            ->where(function ($q) {
                $q->where('status', 1)->orWhere('status', 'active')->orWhereNull('status');
            })
            ->get();
        
        return view('staff.pos', compact('products', 'categories', 'toppings', 'combos'));
    }

    public function searchCustomers(Request $request)
    {
        $q = trim($request->query('q', ''));

        $query = DB::table('users')
            ->select('user_id', 'name', 'email', 'phone', 'point', 'role');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $customers = $query->limit(20)->get();

        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);
    }

    public function checkNewOrders(Request $request)
    {
        $lastOrderId = $request->query('last_order_id', 0);
        
        $newOrders = DB::table('orders')
            ->where('order_id', '>', $lastOrderId)
            ->where('status', 'pending')
            ->orderBy('order_id', 'asc')
            ->get();
            
        return response()->json([
            'new_orders' => $newOrders,
            'count' => $newOrders->count()
        ]);
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'user_id'        => 'nullable|integer',
            'customer_name'  => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'order_note'     => 'nullable|string',
            'total_amount'   => 'required|numeric',
            'items'          => 'required|array',
        ]);

        // 👉 TỰ ĐỘNG TÌM CA LÀM VIỆC CỦA NGÀY HÔM NAY ĐỂ GẮN VÀO ĐƠN HÀNG
        $today = now()->format('Y-m-d');
        $shift = \App\Models\Shift::where('date', $today)->first();
        $shiftId = $shift ? $shift->id : null;

        $orderId = DB::table('orders')->insertGetId([
            'user_id'          => $data['user_id'] ?? null,
            'customer_name'    => $data['customer_name'] ?? 'Khách Vãng Lai',
            'customer_phone'   => $data['customer_phone'] ?? null,
            'shift_id'         => $shiftId, // LƯU VÀO CA LÀM VIỆC
            'shipping_address' => $data['order_note'] ?? null,
            'order_type'       => 'pos',
            'payment_method'   => 'cash',
            'total_amount'     => $data['total_amount'],
            'status'           => 'pending',
            'items'            => json_encode($data['items'], JSON_UNESCAPED_UNICODE),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return response()->json([
            'success' => true, 
            'order_id' => $orderId, 
            'message' => 'Tạo đơn thành công'
        ]);
    }

    public function newOrders()
    {
        $pendingOrders = DB::table('orders')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc') 
            ->get();
            
        $toppings = DB::table('products')
            ->where('category_id', function($query) {
                $query->select('category_id')->from('categories')->where('name', 'like', '%Topping%')->limit(1);
            })->get()->keyBy('product_id');

        return view('staff.new_orders', compact('pendingOrders', 'toppings'));
    }

    // Hàm cập nhật trạng thái đơn hàng thành "Đã hoàn thành" và Ghi nhận Hoa Hồng + Tích Điểm Khách Hàng
    public function completeOrder($id)
    {
        $now = now('Asia/Ho_Chi_Minh');
        
        // 1. Lấy giờ thực tế lúc nhân viên bấm nút để xác định Ca
        $shiftIndex = floor($now->hour / 4) + 1;
        $startHour = ($shiftIndex - 1) * 4;
        $endHour = $shiftIndex * 4;
        
        $startTime = sprintf('%02d:00:00', $startHour);
        $endTime = $endHour == 24 ? '23:59:59' : sprintf('%02d:00:00', $endHour);
        
        // 2. Tìm Ca làm việc hiện tại (hoặc tạo mới nếu hệ thống chưa có)
        $shift = \App\Models\Shift::firstOrCreate(
            ['date' => $now->format('Y-m-d'), 'start_time' => $startTime],
            [
                'name' => "Ca $shiftIndex (" . sprintf('%02d:00', $startHour) . " - " . sprintf('%02d:00', $endHour == 24 ? 0 : $endHour) . ")",
                'end_time' => $endTime
            ]
        );

        $order = DB::table('orders')->where('order_id', $id)->first();

        // 3. Gắn đơn hàng này vào Ca hiện tại & Đánh dấu hoàn thành
        DB::table('orders')->where('order_id', $id)->update([
            'status' => 'completed',
            'shift_id' => $shift->id, // 👉 LƯU DỮ LIỆU THẬT ĐỂ TÍNH HOA HỒNG TẠI ĐÂY
            'updated_at' => $now
        ]);

        // 4. Cộng điểm tích lũy cho khách hàng (10.000đ = 1 điểm)
        if ($order && $order->user_id && $order->status !== 'completed') {
            $pointsEarned = (int) floor($order->total_amount / 10000);
            if ($pointsEarned > 0) {
                DB::table('users')->where('user_id', $order->user_id)->increment('point', $pointsEarned);
            }
        }
        
        return response()->json(['success' => true]);
    }
}