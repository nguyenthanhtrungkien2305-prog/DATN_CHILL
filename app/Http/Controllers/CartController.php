<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // 1. Hiển thị trang Giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    // 2. Thêm sản phẩm vào giỏ
    public function add(Request $request)
    {
        $productId = $request->product_id;
        $variantId = $request->variant_id; // Size
        $quantity = $request->quantity ?? 1;
        $toppings = $request->toppings ?? []; // Mảng topping: ['id' => qty, ...]

        // Truy vấn thông tin Sản phẩm & Size
        $product = DB::table('products')->where('product_id', $productId)->first();

        // Nếu không truyền variant_id, tự động lấy variant đầu tiên (thường là size S có giá thấp nhất)
        if (!$variantId && $product) {
            $firstVariant = DB::table('product_variants')
                ->where('product_id', $product->product_id)
                ->orderBy('price', 'asc')
                ->first();
            if ($firstVariant) {
                $variantId = $firstVariant->variant_id;
            }
        }

        $variant = DB::table('product_variants')->where('variant_id', $variantId)->first();

        if (!$product || !$variant) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm hoặc kích cỡ không hợp lệ!']);
        }

        // Truy vấn chi tiết Topping và tính tổng tiền topping
        $toppingDetails = [];
        $toppingTotal = 0;
        
        if (!empty($toppings)) {
            foreach ($toppings as $topId => $topQty) {
                if ($topQty > 0) {
                    $topInfo = DB::table('toppings')->where('topping_id', $topId)->first();
                    if ($topInfo) {
                        $toppingDetails[$topId] = [
                            'name' => $topInfo->name,
                            'price' => $topInfo->price,
                            'qty' => $topQty
                        ];
                        $toppingTotal += ($topInfo->price * $topQty);
                    }
                }
            }
        }

        // Tạo một "Khóa duy nhất" cho tổ hợp này để kiểm tra trùng lặp
        // Nếu chọn cùng SP, cùng Size, cùng bộ Topping -> Cộng dồn số lượng
        ksort($toppings); // Sắp xếp mảng topping để tạo key chuẩn xác
        $cartKey = md5($productId . '_' . $variantId . '_' . serialize($toppings));

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            // Đã có trong giỏ -> Cộng dồn số lượng
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Thêm mới vào giỏ
            $cart[$cartKey] = [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'image' => $product->image_url,
                'variant_id' => $variant->variant_id,
                'size_name' => DB::table('sizes')->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định',
                'price' => $variant->price,
                'quantity' => $quantity,
                'toppings' => $toppingDetails,
                'topping_total' => $toppingTotal,
            ];
        }

        session()->put('cart', $cart);
        self::checkAndRecalculateVoucher();

        // Tính tổng số lượng món trong giỏ để update icon giỏ hàng
        $totalItems = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true, 
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $totalItems
        ]);
    }
    // 3. Cập nhật số lượng sản phẩm trong giỏ
    public function update(Request $request)
    {
        $cartKey = $request->cart_key; // Mã của món hàng trong Session
        $change = $request->change;    // Số lượng thay đổi (+1 hoặc -1)

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $change;
            
            // Giới hạn số lượng tối thiểu là 1 (Nếu muốn xóa thì dùng nút Thùng rác)
            if ($cart[$cartKey]['quantity'] < 1) {
                $cart[$cartKey]['quantity'] = 1;
            }
            
            session()->put('cart', $cart);
            self::checkAndRecalculateVoucher();
            
            $totalItems = array_sum(array_column($cart, 'quantity'));
            return response()->json([
                'success' => true,
                'cart_count' => $totalItems
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // 4. Xóa sản phẩm khỏi giỏ
    public function remove(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]); // Xóa khỏi mảng
            session()->put('cart', $cart);
            self::checkAndRecalculateVoucher();
            
            $totalItems = array_sum(array_column($cart, 'quantity'));
            return response()->json([
                'success' => true,
                'cart_count' => $totalItems
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }
    // 5. Lấy thông tin 1 món trong giỏ để đưa lên Modal sửa Topping
    public function getItem(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);

        if (!isset($cart[$cartKey])) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
        }

        $item = $cart[$cartKey];
        $productId = $item['product_id'];

        // Lấy tất cả topping được phép áp dụng cho sản phẩm này
        $product = DB::table('products')->where('product_id', $productId)->first();
        $categoryName = $product ? DB::table('categories')->where('category_id', $product->category_id)->value('name') : '';
        $isBanhNgot = $categoryName && (str_contains(mb_strtolower($categoryName), 'bánh') || str_contains(mb_strtolower($categoryName), 'cake'));
        $isToppingCategory = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');

        if (!$isBanhNgot && !$isToppingCategory) {
            $availableToppings = DB::table('toppings')->where('status', 1)->get();
        } else {
            $availableToppings = collect([]);
        }

        // Lắp số lượng hiện tại đang có trong giỏ vào danh sách
        $toppingList = [];
        foreach ($availableToppings as $top) {
            // Kiểm tra xem topping này đã được chọn chưa, nếu có thì lấy số lượng đang có
            $currentQty = isset($item['toppings'][$top->topping_id]) ? $item['toppings'][$top->topping_id]['qty'] : 0;
            
            $toppingList[] = [
                'topping_id' => $top->topping_id,
                'name' => $top->name,
                'price' => $top->price,
                'image' => $top->image,
                'qty' => $currentQty
            ];
        }

        return response()->json([
            'success' => true,
            'item_name' => $item['name'],
            'size_name' => $item['size_name'],
            'toppings' => $toppingList
        ]);
    }

    // 6. Cập nhật lại Topping cho món trong giỏ
    public function updateToppings(Request $request)
    {
        $oldCartKey = $request->cart_key;
        $newToppings = $request->toppings ?? []; // format: ['id' => qty]
        
        $cart = session()->get('cart', []);

        if (!isset($cart[$oldCartKey])) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
        }

        $oldItem = $cart[$oldCartKey];

        // 1. Tính toán lại chi tiết và tổng tiền Topping mới
        $toppingDetails = [];
        $toppingTotal = 0;
        $cleanToppings = [];

        if (!empty($newToppings)) {
            foreach ($newToppings as $topId => $topQty) {
                if ($topQty > 0) {
                    $topInfo = DB::table('toppings')->where('topping_id', $topId)->first();
                    if ($topInfo) {
                        $toppingDetails[$topId] = [
                            'name' => $topInfo->name,
                            'price' => $topInfo->price,
                            'qty' => $topQty
                        ];
                        $toppingTotal += ($topInfo->price * $topQty);
                        $cleanToppings[$topId] = $topQty;
                    }
                }
            }
        }

        // 2. Tạo khóa giỏ hàng mới (Vì topping thay đổi -> mã md5 bắt buộc thay đổi)
        ksort($cleanToppings);
        $newCartKey = md5($oldItem['product_id'] . '_' . $oldItem['variant_id'] . '_' . serialize($cleanToppings));

        // Nếu khóa mới hoàn toàn giống khóa cũ (khách bật lên xem nhưng ko đổi gì)
        if ($newCartKey === $oldCartKey) {
            return response()->json(['success' => true]);
        }

        // 3. Chuyển dữ liệu sang khóa mới
        $newItem = $oldItem;
        $newItem['toppings'] = $toppingDetails;
        $newItem['topping_total'] = $toppingTotal;

        if (isset($cart[$newCartKey])) {
            // Nếu vô tình trùng với 1 món khác đã có sẵn trong giỏ -> Cộng dồn số lượng
            $cart[$newCartKey]['quantity'] += $newItem['quantity'];
        } else {
            // Thêm như 1 dòng mới
            $cart[$newCartKey] = $newItem;
        }

        // 4. Xóa dòng cũ đi và Lưu session
        unset($cart[$oldCartKey]);
        session()->put('cart', $cart);
        self::checkAndRecalculateVoucher();

        return response()->json(['success' => true]);
    }

    // 7. Áp dụng mã giảm giá (Voucher)
    public function applyVoucher(Request $request)
    {
        $code = strtoupper(trim($request->voucher_code));
        if (empty($code)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá!']);
        }

        // Kiểm tra xem giỏ hàng có trống không
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống!']);
        }

        // Tính tạm tính
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        // Tìm mã giảm giá
        $voucher = DB::table('vouchers')->where('code', $code)->first();
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc không tồn tại!']);
        }

        // Kiểm tra thời hạn hiệu lực
        $now = now();
        if ($voucher->start_date && $now->lt(\Carbon\Carbon::parse($voucher->start_date))) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa có hiệu lực sử dụng!']);
        }
        if ($voucher->end_date && $now->gt(\Carbon\Carbon::parse($voucher->end_date))) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn sử dụng!']);
        }

        // Kiểm tra giới hạn số lượt sử dụng
        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!']);
        }

        // Kiểm tra đơn hàng tối thiểu
        if ($subTotal < $voucher->min_order) {
            return response()->json([
                'success' => false, 
                'message' => 'Đơn hàng của bạn chưa đạt giá trị tối thiểu ' . number_format($voucher->min_order, 0, ',', '.') . 'đ để sử dụng mã này!'
            ]);
        }

        // Tính số tiền giảm giá
        $discount = 0;
        if ($voucher->discount_type === 'percent') {
            $discount = $subTotal * ($voucher->discount_value / 100);
        } else {
            $discount = $voucher->discount_value;
        }

        // Đảm bảo số tiền giảm giá không vượt quá số tiền đơn hàng
        $discount = min($discount, $subTotal);

        // Lưu thông tin voucher vào session
        session()->put('voucher', [
            'voucher_id' => $voucher->voucher_id,
            'code' => $voucher->code,
            'discount_type' => $voucher->discount_type,
            'discount_value' => $voucher->discount_value,
            'discount_amount' => $discount,
            'min_order' => $voucher->min_order
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount' => $discount,
            'discount_formatted' => number_format($discount, 0, ',', '.') . ' đ',
            'new_total' => $subTotal - $discount,
            'new_total_formatted' => number_format($subTotal - $discount, 0, ',', '.') . ' đ'
        ]);
    }

    // 8. Hủy áp dụng mã giảm giá
    public function removeVoucher(Request $request)
    {
        session()->forget('voucher');
        
        $cart = session()->get('cart', []);
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy áp dụng mã giảm giá.',
            'total' => $subTotal,
            'total_formatted' => number_format($subTotal, 0, ',', '.') . ' đ'
        ]);
    }

    // 9. Helper cập nhật voucher khi giỏ hàng thay đổi
    public static function checkAndRecalculateVoucher()
    {
        if (!session()->has('voucher')) {
            return;
        }

        $cart = session()->get('cart', []);
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        $voucherSession = session()->get('voucher');
        $voucher = DB::table('vouchers')->where('voucher_id', $voucherSession['voucher_id'])->first();

        // Nếu giỏ hàng rỗng hoặc không đủ đơn hàng tối thiểu
        if (empty($cart) || !$voucher || $subTotal < $voucher->min_order) {
            session()->forget('voucher');
            return;
        }

        // Tính lại số tiền giảm
        $discount = 0;
        if ($voucher->discount_type === 'percent') {
            $discount = $subTotal * ($voucher->discount_value / 100);
        } else {
            $discount = $voucher->discount_value;
        }
        $discount = min($discount, $subTotal);

        // Lưu lại session cập nhật
        $voucherSession['discount_amount'] = $discount;
        session()->put('voucher', $voucherSession);
    }

    // 10. Lấy số lượng giỏ hàng hiện tại (cho AJAX đồng bộ)
    public function getCount()
    {
        $cart = session()->get('cart', []);
        $totalItems = array_sum(array_column($cart, 'quantity'));
        return response()->json(['cart_count' => $totalItems]);
    }
}