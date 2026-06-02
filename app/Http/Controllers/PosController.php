<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        // 1. Lấy tất cả danh mục
        $categories = DB::table('categories')->get();

        // 2. Tìm ID của danh mục "Topping"
        $toppingCategory = DB::table('categories')->where('name', 'like', '%Topping%')->first();
        $toppingCatId = $toppingCategory ? $toppingCategory->category_id : null;

        // 3. LẤY DANH SÁCH SẢN PHẨM (Nối với bảng product_variants để lấy giá)
        $productsQuery = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
            ->groupBy('products.product_id'); // Gom nhóm để hàm MIN hoạt động

        if ($toppingCatId) {
            $productsQuery->where('products.category_id', '!=', $toppingCatId);
        }
        $products = $productsQuery->get();

        // 4. LẤY DANH SÁCH TOPPING (Nối với bảng product_variants để lấy giá)
        $toppings = [];
        if ($toppingCatId) {
            $toppings = DB::table('products')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->select('products.*', DB::raw('MIN(product_variants.price) as price'))
                ->where('products.category_id', $toppingCatId)
                ->groupBy('products.product_id')
                ->get();
        }
        

        return view('staff.pos', compact('products', 'categories', 'toppings'));
    }
    // Hàm API kiểm tra đơn hàng mới liên tục
    public function checkNewOrders(Request $request)
    {
        // Lấy ID đơn hàng mới nhất mà trình duyệt đang biết (mặc định là 0)
        $lastOrderId = $request->query('last_order_id', 0);
        
        // Tìm trong bảng orders những đơn có order_id > lastOrderId và trạng thái là 'pending'
        $newOrders = \Illuminate\Support\Facades\DB::table('orders')
            ->where('order_id', '>', $lastOrderId)
            ->where('status', 'pending')
            ->orderBy('order_id', 'asc')
            ->get();
            
        return response()->json([
            'new_orders' => $newOrders,
            'count' => $newOrders->count()
        ]);
    }
    // Hàm Lưu đơn hàng từ POS vào Database
    public function storeOrder(Request $request)
    {
        // 1. Nhận dữ liệu JSON gửi lên từ Javascript
        $data = $request->validate([
            'customer_name' => 'nullable|string',
            'order_note'    => 'nullable|string',
            'total_amount'  => 'required|numeric',
            'items'         => 'required|array',
        ]);

        // 2. Insert vào bảng orders
        $orderId = DB::table('orders')->insertGetId([
            'customer_name'    => $data['customer_name'] ?? 'Khách Vãng Lai',
            'customer_phone'   => null,
            'shipping_address' => $data['order_note'], // Tạm dùng cột này lưu ghi chú
            'order_type'       => 'pos', // Đánh dấu là đơn tạo tại quầy
            'payment_method'   => 'cash',
            'total_amount'     => $data['total_amount'],
            'status'           => 'pending', // Quan trọng: Để pending thì hệ thống báo động mới kêu!
            'items'            => json_encode($data['items'], JSON_UNESCAPED_UNICODE),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // 3. Trả về kết quả Thành công
        return response()->json([
            'success' => true, 
            'order_id' => $orderId, 
            'message' => 'Tạo đơn thành công'
        ]);
    }
    // Hàm hiển thị trang Đơn hàng mới (Lấy dữ liệu thật)
    public function newOrders()
    {
        // Lấy danh sách đơn hàng đang 'pending', cũ nhất xếp lên trên
        $pendingOrders = \Illuminate\Support\Facades\DB::table('orders')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc') 
            ->get();
            
        // Truyền thêm danh mục Topping để lỡ cần hiển thị tên Topping (nếu có)
        $toppings = \Illuminate\Support\Facades\DB::table('products')
            ->where('category_id', function($query) {
                $query->select('category_id')->from('categories')->where('name', 'like', '%Topping%')->limit(1);
            })->get()->keyBy('product_id');

        return view('staff.new_orders', compact('pendingOrders', 'toppings'));
    }

    // Hàm cập nhật trạng thái đơn hàng thành "Đã hoàn thành"
    public function completeOrder($id)
    {
        \Illuminate\Support\Facades\DB::table('orders')->where('order_id', $id)->update([
            'status' => 'completed',
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }
}