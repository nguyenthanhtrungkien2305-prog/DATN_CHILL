<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * BỘ LỌC THÉP: Xử lý mảng Topping gửi lên từ Javascript
     * Đảm bảo luôn trả về mảng đúng định dạng [topping_id => số_lượng]
     */
    private function parseToppings($input)
    {
        $clean = [];
        if (is_string($input)) {
            $input = json_decode($input, true);
        }
        if (is_array($input) || is_object($input)) {
            foreach ($input as $id => $qty) {
                $id = (int)$id;
                $qty = (int)$qty;
                if ($id > 0 && $qty > 0) {
                    $clean[$id] = $qty;
                }
            }
        }
        ksort($clean); // Sắp xếp để tạo Cart Key luôn chuẩn xác
        return $clean;
    }

    // 1. Hiển thị trang Giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    // 2. Thêm sản phẩm vào giỏ (Đã dùng Bộ lọc)
    public function add(Request $request)
    {
        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $quantity = (int)($request->quantity ?? 1);
        $iceLevel = $request->input('ice_level', '100');
        $sugarLevel = $request->input('sugar_level', '100');
        
        // Gọi bộ lọc xử lý Topping
        $cleanToppings = $this->parseToppings($request->input('toppings', []));

        $product = DB::table('products')->where('product_id', $productId)->first();

        if (!$variantId && $product) {
            $firstVariant = DB::table('product_variants')->where('product_id', $product->product_id)->orderBy('price', 'asc')->first();
            if ($firstVariant) $variantId = $firstVariant->variant_id;
        }

        $variant = DB::table('product_variants')->where('variant_id', $variantId)->first();

        if (!$product || !$variant) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm hoặc kích cỡ không hợp lệ!']);
        }

        $toppingDetails = [];
        $toppingTotal = 0;
        
        // Xử lý lưu chi tiết Topping
        foreach ($cleanToppings as $topId => $topQty) {
            $topProduct = DB::table('products')->where('product_id', $topId)->first();
            $topPrice = DB::table('product_variants')->where('product_id', $topId)->min('price');
            $topPrice = $topPrice ? (float) $topPrice : 0;
            
            if ($topProduct) {
                $toppingDetails[$topId] = [
                    'name' => $topProduct->name,
                    'price' => $topPrice,
                    'qty' => $topQty
                ];
                $toppingTotal += ($topPrice * $topQty);
            }
        }

        // Tính phụ thu
        if ($iceLevel === '0_full') {
            $toppingTotal += 3000;
        }

        // Tạo khóa giỏ hàng bằng json_encode để tránh lỗi Serialize
        $cartKey = md5($productId . '_' . $variantId . '_' . $iceLevel . '_' . $sugarLevel . '_' . json_encode($cleanToppings));
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'image' => $product->image_url,
                'variant_id' => $variant->variant_id,
                'size_name' => DB::table('sizes')->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định',
                'price' => (float) $variant->price,
                'quantity' => $quantity,
                'toppings' => $toppingDetails,
                'topping_total' => $toppingTotal,
                'ice_level' => $iceLevel,
                'sugar_level' => $sugarLevel,
            ];
        }

        session()->put('cart', $cart);
        self::checkAndRecalculateVoucher();

        $totalItems = array_sum(array_column($cart, 'quantity'));
        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!', 'cart_count' => $totalItems]);
    }

    // 3. Cập nhật số lượng
    public function update(Request $request)
    {
        $cartKey = $request->cart_key;
        $change = (int) $request->change;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $change;
            if ($cart[$cartKey]['quantity'] < 1) $cart[$cartKey]['quantity'] = 1;
            
            session()->put('cart', $cart);
            self::checkAndRecalculateVoucher();
            
            $totalItems = array_sum(array_column($cart, 'quantity'));
            return response()->json(['success' => true, 'cart_count' => $totalItems]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // 4. Xóa khỏi giỏ
    public function remove(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            self::checkAndRecalculateVoucher();
            
            $totalItems = array_sum(array_column($cart, 'quantity'));
            return response()->json(['success' => true, 'cart_count' => $totalItems]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // 5. Lấy dữ liệu đẩy lên Bảng (Modal)
    public function getItem(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);

        if (!isset($cart[$cartKey])) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
        }

        $item = $cart[$cartKey];
        $productId = $item['product_id'];

        $product = DB::table('products')->where('product_id', $productId)->first();
        $categoryName = $product ? DB::table('categories')->where('category_id', $product->category_id)->value('name') : '';
        $isBanhNgot = $categoryName && (str_contains(mb_strtolower($categoryName), 'bánh') || str_contains(mb_strtolower($categoryName), 'cake'));
        $isToppingCategory = $categoryName && str_contains(mb_strtolower($categoryName), 'topping');

        if (!$isBanhNgot && !$isToppingCategory) {
            $toppingCategory = DB::table('categories')->where('name', 'LIKE', '%topping%')->orWhere('name', 'LIKE', '%Topping%')->first();
            if ($toppingCategory) {
                $availableToppings = DB::table('products')
                    ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                    ->where('products.category_id', $toppingCategory->category_id)
                    ->where('products.status', 1)
                    ->select('products.product_id as topping_id', 'products.name', 'products.image_url as image', DB::raw('MIN(product_variants.price) as price'))
                    ->groupBy('products.product_id', 'products.name', 'products.image_url')
                    ->get();
            } else {
                $availableToppings = collect([]);
            }
        } else {
            $availableToppings = collect([]);
        }

        $toppingList = [];
        foreach ($availableToppings as $top) {
            $currentQty = isset($item['toppings'][$top->topping_id]) ? $item['toppings'][$top->topping_id]['qty'] : 0;
            $toppingList[] = [
                'topping_id' => $top->topping_id,
                'name' => $top->name,
                'price' => (float) $top->price,
                'image' => $top->image,
                'qty' => $currentQty
            ];
        }

        return response()->json([
            'success' => true,
            'item_name' => $item['name'],
            'size_name' => $item['size_name'],
            'item_price' => $item['price'],
            'ice_level' => $item['ice_level'] ?? '100',
            'sugar_level' => $item['sugar_level'] ?? '100',
            'toppings' => $toppingList
        ]);
    }

    // 6. Cập nhật lại thay đổi Topping trong giỏ
    public function updateToppings(Request $request)
    {
        $oldCartKey = $request->cart_key;
        $iceLevel = $request->input('ice_level', '100');
        $sugarLevel = $request->input('sugar_level', '100');
        
        // Gọi bộ lọc xử lý Topping
        $cleanToppings = $this->parseToppings($request->input('toppings', []));
        
        $cart = session()->get('cart', []);

        if (!isset($cart[$oldCartKey])) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
        }

        $oldItem = $cart[$oldCartKey];
        $toppingDetails = [];
        $toppingTotal = 0;

        foreach ($cleanToppings as $topId => $topQty) {
            $topProduct = DB::table('products')->where('product_id', $topId)->first();
            $topPrice = DB::table('product_variants')->where('product_id', $topId)->min('price');
            $topPrice = $topPrice ? (float) $topPrice : 0;
            
            if ($topProduct) {
                $toppingDetails[$topId] = [
                    'name' => $topProduct->name,
                    'price' => $topPrice,
                    'qty' => $topQty
                ];
                $toppingTotal += ($topPrice * $topQty);
            }
        }

        if ($iceLevel === '0_full') {
            $toppingTotal += 3000;
        }

        // Tạo key mới
        $newCartKey = md5($oldItem['product_id'] . '_' . $oldItem['variant_id'] . '_' . $iceLevel . '_' . $sugarLevel . '_' . json_encode($cleanToppings));

        // Nếu khách lưu mà không thay đổi gì thì bỏ qua
        if ($newCartKey === $oldCartKey) {
            return response()->json(['success' => true]);
        }

        $newItem = $oldItem;
        $newItem['toppings'] = $toppingDetails;
        $newItem['topping_total'] = $toppingTotal;
        $newItem['ice_level'] = $iceLevel;
        $newItem['sugar_level'] = $sugarLevel;

        if (isset($cart[$newCartKey])) {
            $cart[$newCartKey]['quantity'] += $newItem['quantity'];
        } else {
            $cart[$newCartKey] = $newItem;
        }

        unset($cart[$oldCartKey]); // Xóa khóa cũ
        session()->put('cart', $cart); // Lưu khóa mới
        self::checkAndRecalculateVoucher();

        return response()->json(['success' => true]);
    }

    // 7. Áp dụng mã giảm giá
    public function applyVoucher(Request $request)
    {
        $code = strtoupper(trim($request->voucher_code));
        if (empty($code)) return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá!']);

        $cart = session()->get('cart', []);
        if (empty($cart)) return response()->json(['success' => false, 'message' => 'Giỏ hàng đang trống!']);

        $subTotal = 0;
        foreach ($cart as $item) $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];

        $voucher = DB::table('vouchers')->where('code', $code)->first();
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ!']);

        $now = now();
        if ($voucher->start_date && $now->lt(\Carbon\Carbon::parse($voucher->start_date))) return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa có hiệu lực!']);
        if ($voucher->end_date && $now->gt(\Carbon\Carbon::parse($voucher->end_date))) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']);
        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) return response()->json(['success' => false, 'message' => 'Mã đã hết lượt sử dụng!']);
        if ($subTotal < $voucher->min_order) return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt tối thiểu ' . number_format($voucher->min_order, 0, ',', '.') . 'đ!']);

        $discount = $voucher->discount_type === 'percent' ? $subTotal * ($voucher->discount_value / 100) : $voucher->discount_value;
        $discount = min($discount, $subTotal);

        session()->put('voucher', [
            'voucher_id' => $voucher->voucher_id, 'code' => $voucher->code, 'discount_type' => $voucher->discount_type,
            'discount_value' => $voucher->discount_value, 'discount_amount' => $discount, 'min_order' => $voucher->min_order
        ]);

        return response()->json(['success' => true, 'message' => 'Áp dụng mã thành công!']);
    }

    // 8. Hủy áp dụng mã giảm giá
    public function removeVoucher(Request $request)
    {
        session()->forget('voucher');
        return response()->json(['success' => true, 'message' => 'Đã hủy mã giảm giá.']);
    }

    // 9. Cập nhật lại voucher khi giỏ đổi
    public static function checkAndRecalculateVoucher()
    {
        if (!session()->has('voucher')) return;

        $cart = session()->get('cart', []);
        $subTotal = 0;
        foreach ($cart as $item) $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];

        $voucherSession = session()->get('voucher');
        $voucher = DB::table('vouchers')->where('voucher_id', $voucherSession['voucher_id'])->first();

        if (empty($cart) || !$voucher || $subTotal < $voucher->min_order) {
            session()->forget('voucher'); return;
        }

        $discount = $voucher->discount_type === 'percent' ? $subTotal * ($voucher->discount_value / 100) : $voucher->discount_value;
        $discount = min($discount, $subTotal);
        $voucherSession['discount_amount'] = $discount;
        session()->put('voucher', $voucherSession);
    }

    public function getCount()
    {
        $cart = session()->get('cart', []);
        $totalItems = array_sum(array_column($cart, 'quantity'));
        return response()->json(['cart_count' => $totalItems]);
    }
}