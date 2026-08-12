<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    const CART_TTL_HOURS = 24;

    /**
     * Trả về cache key của giỏ hàng theo trạng thái đăng nhập.
     * - Đã đăng nhập : cart:u:{user_id}
     * - Khách vãng lai: cart:g:{uuid_cookie}   (cookie 'cart_token' tồn tại 24h)
     */
    public static function cartKey(): string
    {
        if (auth()->check()) {
            return 'cart:u:' . auth()->id();
        }

        $token = request()->cookie('cart_token');
        if (!$token) {
            $token = Str::uuid()->toString();
            Cookie::queue('cart_token', $token, 60 * self::CART_TTL_HOURS);
        }

        return 'cart:g:' . $token;
    }

    /** Đọc giỏ hàng từ Cache. */
    public static function getCart(): array
    {
        return Cache::get(self::cartKey(), []);
    }

    /** Lưu giỏ hàng vào Cache, gia hạn TTL 24h từ lần thao tác cuối. */
    public static function saveCart(array $cart): void
    {
        Cache::put(self::cartKey(), $cart, now()->addHours(self::CART_TTL_HOURS));
    }

    private function parseToppings($input)
    {
        $clean = [];
        if (is_string($input)) {
            $input = json_decode($input, true);
        }
        if (is_array($input) || is_object($input)) {
            foreach ($input as $id => $qty) {
                if (is_array($qty)) {
                    $qty = $qty['qty'] ?? 0;
                }
                $id = (int)$id;
                $qty = (int)$qty;
                if ($id > 0 && $qty > 0) {
                    $clean[$id] = $qty;
                }
            }
        }
        ksort($clean);
        return $clean;
    }

    public function getApplicableVouchers($subTotal)
    {
        \App\Http\Controllers\Admin\VoucherController::cleanupExpiredVouchers();

        $vouchers = collect();

        // 1. Lấy mã công khai
        $publicVouchers = DB::table('vouchers')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
            })
            ->whereNull('assigned_user_id')
            ->get();

        foreach ($publicVouchers as $v) {
            $vouchers->push($v);
        }

        // 2. Lấy mã cá nhân của user đang đăng nhập
        if (auth()->check()) {
            $userId = auth()->id() ?? auth()->user()->user_id;

            $myVouchers = DB::table('user_vouchers')
                ->join('vouchers', 'user_vouchers.voucher_id', '=', 'vouchers.voucher_id')
                ->where('user_vouchers.user_id', $userId)
                ->where('user_vouchers.is_used', 0)
                ->where(function ($q) {
                    $q->whereNull('vouchers.end_date')->orWhere('vouchers.end_date', '>=', now());
                })
                ->select('vouchers.*')
                ->get();

            foreach ($myVouchers as $mv) {
                if (!$vouchers->contains('voucher_id', $mv->voucher_id)) {
                    $vouchers->push($mv);
                }
            }
        }

        // Tính toán tính khả dụng cho mỗi voucher
        $vouchers->transform(function ($v) use ($subTotal) {
            $v->is_eligible = ($subTotal >= $v->min_order);
            $v->missing_amount = max(0, $v->min_order - $subTotal);

            $discount = $v->discount_type === 'percent' 
                ? $subTotal * ($v->discount_value / 100) 
                : $v->discount_value;
            $v->discount_amount = min($discount, $subTotal);
            return $v;
        });

        // Sắp xếp: Mã đủ điều kiện lên trên (giảm nhiều nhất lên đầu), mã chưa đủ điều kiện xuống dưới
        return $vouchers->sortBy([
            ['is_eligible', 'desc'],
            ['discount_amount', 'desc'],
            ['missing_amount', 'asc']
        ])->values();
    }

    // 1. Hiển thị trang Giỏ hàng
    public function index()
    {
        $cart = self::getCart();
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        $availableVouchers = $this->getApplicableVouchers($subTotal);

        // Tự động áp dụng mã tốt nhất ĐỦ ĐIỀU KIỆN nếu chưa có mã trong session VÀ chưa chọn bỏ dùng voucher
        if (!session()->has('voucher') && !session()->get('voucher_opt_out', false) && $subTotal > 0) {
            $bestEligible = $availableVouchers->firstWhere('is_eligible', true);
            if ($bestEligible) {
                session()->put('voucher', [
                    'voucher_id' => $bestEligible->voucher_id,
                    'code' => $bestEligible->code,
                    'discount_type' => $bestEligible->discount_type,
                    'discount_value' => $bestEligible->discount_value,
                    'discount_amount' => $bestEligible->discount_amount,
                    'min_order' => $bestEligible->min_order,
                    'auto_applied' => true
                ]);
            }
        }

        return view('cart.index', compact('cart', 'availableVouchers'));
    }

    // 2. Thêm sản phẩm vào giỏ
    public function add(Request $request)
    {
        $productId = $request->input('product_id', $request->input('productId', $request->input('id')));
        $variantId = $request->input('variant_id', $request->input('variantId'));
        $quantity  = (int)($request->input('quantity', 1));
        if ($quantity < 1) $quantity = 1;

        $iceLevel   = $request->input('ice_level', '100');
        $sugarLevel = $request->input('sugar_level', '100');
        
        $cleanToppings = $this->parseToppings($request->input('toppings', []));

        $product = DB::table('products')->where('product_id', $productId)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Món này hiện không còn tồn tại hoặc đã ngừng kinh doanh!']);
        }

        $variant = null;
        if ($variantId) {
            $variant = DB::table('product_variants')->where('variant_id', $variantId)->first();
        }

        if (!$variant) {
            $variant = DB::table('product_variants')->where('product_id', $product->product_id)->orderBy('price', 'asc')->first();
        }

        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm này chưa được thiết lập kích cỡ/giá bán!']);
        }

        $toppingDetails = [];
        $toppingTotal   = 0;
        $cleanToppings  = [];

        if (!empty($toppings)) {
            $parsed = $this->parseToppings($toppings);
            foreach ($parsed as $topId => $topQty) {
                $topInfo = DB::table('toppings')->where('topping_id', $topId)->first();
                if ($topInfo) {
                    $toppingDetails[$topId] = [
                        'name'  => $topInfo->name,
                        'price' => $topInfo->price,
                        'qty'   => $topQty,
                    ];
                    $toppingTotal += $topInfo->price * $topQty;
                    $cleanToppings[$topId] = $topQty;
                }
            }
        }

        if ($iceLevel === '0_full') {
            $toppingTotal += 3000;
        }

        ksort($cleanToppings);
        $cartKey = md5($productId . '_' . $variantId . '_' . $iceLevel . '_' . $sugarLevel . '_' . json_encode($cleanToppings));
        $cart    = self::getCart();

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id'    => $product->product_id,
                'name'          => $product->name,
                'image'         => $product->image_url,
                'variant_id'    => $variant->variant_id,
                'size_name'     => DB::table('sizes')->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định',
                'price'         => (float) $variant->price,
                'quantity'      => $quantity,
                'toppings'      => $toppingDetails,
                'topping_total' => $toppingTotal,
                'ice_level'     => $iceLevel,
                'sugar_level'   => $sugarLevel,
            ];
        }

        self::saveCart($cart);
        self::checkAndRecalculateVoucher();
        self::syncCartItemsToDatabase();

        $totalItems = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success'    => true,
            'message'    => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $totalItems,
        ]);
    }

    public function addCombo(Request $request)
    {
        $comboId = (int)$request->input('combo_id');
        $quantity = (int)$request->input('quantity', 1);
        if ($quantity < 1) $quantity = 1;

        $combo = \App\Models\Combo::with('products')->find($comboId);
        if (!$combo || !$combo->status) {
            return response()->json(['success' => false, 'message' => 'Gói Combo này không tồn tại hoặc đã tạm ngưng!']);
        }

        $itemsSummary = [];
        foreach ($combo->products as $p) {
            $itemsSummary[] = $p->name . ' x' . $p->pivot->quantity;
        }

        $cartKey = md5('combo_' . $comboId);
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => 0,
                'combo_id' => $combo->combo_id,
                'is_combo' => true,
                'name' => '[COMBO] ' . $combo->name,
                'image' => $combo->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=300&auto=format&fit=crop',
                'variant_id' => 0,
                'size_name' => implode(' + ', $itemsSummary),
                'price' => (float) $combo->price,
                'quantity' => $quantity,
                'toppings' => [],
                'topping_total' => 0,
                'ice_level' => null,
                'sugar_level' => null,
            ];
        }

        session()->put('cart', $cart);
        self::checkAndRecalculateVoucher();
        self::syncCartItemsToDatabase();

        $totalItems = array_sum(array_column($cart, 'quantity'));
        return response()->json(['success' => true, 'message' => 'Đã thêm gói Combo vào giỏ hàng!', 'cart_count' => $totalItems]);
    }

    // 3. Cập nhật số lượng
    public function update(Request $request)
    {
        $cartKey = $request->cart_key;
        $change  = (int)$request->change;
        $cart    = self::getCart();

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = max(1, $cart[$cartKey]['quantity'] + $change);
            self::saveCart($cart);
            self::checkAndRecalculateVoucher();
            self::syncCartItemsToDatabase();

            return response()->json([
                'success'    => true,
                'cart_count' => array_sum(array_column($cart, 'quantity')),
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // 4. Xóa khỏi giỏ
    public function remove(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart    = self::getCart();

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            self::saveCart($cart);
            self::checkAndRecalculateVoucher();
            self::syncCartItemsToDatabase();

            return response()->json([
                'success'    => true,
                'cart_count' => array_sum(array_column($cart, 'quantity')),
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // 5. Lấy thông tin 1 món (cho modal sửa topping)
    public function getItem(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart    = self::getCart();

        if (!isset($cart[$cartKey])) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
        }

        $item       = $cart[$cartKey];
        $product    = DB::table('products')->where('product_id', $item['product_id'])->first();
        $catName    = $product ? DB::table('categories')->where('category_id', $product->category_id)->value('name') : '';
        $isBanh     = $catName && (str_contains(mb_strtolower($catName), 'bánh') || str_contains(mb_strtolower($catName), 'cake'));
        $isTopping  = $catName && str_contains(mb_strtolower($catName), 'topping');

        $availableToppings = (!$isBanh && !$isTopping)
            ? DB::table('toppings')->where('status', 1)->get()
            : collect([]);

        $toppingList = [];
        foreach ($availableToppings as $top) {
            $toppingList[] = [
                'topping_id' => $top->topping_id,
                'name'       => $top->name,
                'price'      => (float) $top->price,
                'image'      => $top->image ?? '',
                'qty'        => $item['toppings'][$top->topping_id]['qty'] ?? 0,
            ];
        }

        return response()->json([
            'success'    => true,
            'item_name'  => $item['name'],
            'size_name'  => $item['size_name'],
            'item_price' => $item['price'],
            'ice_level'  => $item['ice_level'] ?? '100',
            'sugar_level'=> $item['sugar_level'] ?? '100',
            'toppings'   => $toppingList,
        ]);
    }

    // 6. Cập nhật topping cho 1 món
    public function updateToppings(Request $request)
    {
        $oldCartKey  = $request->cart_key;
        $iceLevel    = $request->input('ice_level', '100');
        $sugarLevel  = $request->input('sugar_level', '100');
        $newToppings = $request->toppings ?? [];
        $cart        = self::getCart();

        if (!isset($cart[$oldCartKey])) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
        }

        $oldItem        = $cart[$oldCartKey];
        $toppingDetails = [];
        $toppingTotal   = 0;
        $cleanToppings  = $this->parseToppings($newToppings);

        foreach ($cleanToppings as $topId => $topQty) {
            $topInfo = DB::table('toppings')->where('topping_id', $topId)->first();
            if ($topInfo) {
                $toppingDetails[$topId] = ['name' => $topInfo->name, 'price' => $topInfo->price, 'qty' => $topQty];
                $toppingTotal += $topInfo->price * $topQty;
            }
        }

        if ($iceLevel === '0_full') {
            $toppingTotal += 3000;
        }

        ksort($cleanToppings);
        $newCartKey = md5($oldItem['product_id'] . '_' . $oldItem['variant_id'] . '_' . $iceLevel . '_' . $sugarLevel . '_' . json_encode($cleanToppings));

        if ($newCartKey === $oldCartKey) {
            return response()->json(['success' => true]);
        }

        $newItem                  = $oldItem;
        $newItem['toppings']      = $toppingDetails;
        $newItem['topping_total'] = $toppingTotal;
        $newItem['ice_level']     = $iceLevel;
        $newItem['sugar_level']   = $sugarLevel;

        if (isset($cart[$newCartKey])) {
            $cart[$newCartKey]['quantity'] += $newItem['quantity'];
        } else {
            $cart[$newCartKey] = $newItem;
        }

        unset($cart[$oldCartKey]);
        self::saveCart($cart);
        self::checkAndRecalculateVoucher();

        return response()->json(['success' => true]);
    }

    // 7. Áp dụng voucher
    public function applyVoucher(Request $request)
    {
        $code = strtoupper(trim($request->voucher_code));
        if (empty($code)) return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá!']);

        $cart = self::getCart();
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống!']);
        }

        $subTotal = 0;
        foreach ($cart as $item) $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];

        $voucher = DB::table('vouchers')->where('code', $code)->first();
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ!']);
        }

        $now = now();
        if ($voucher->start_date && $now->lt(\Carbon\Carbon::parse($voucher->start_date))) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa có hiệu lực!']);
        }
        if ($voucher->end_date && $now->gt(\Carbon\Carbon::parse($voucher->end_date))) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']);
        }
        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!']);
        }
        if ($subTotal < $voucher->min_order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt tối thiểu ' . number_format($voucher->min_order, 0, ',', '.') . 'đ!',
            ]);
        }

        // Kiểm tra xem khách hàng có sở hữu voucher trong kho user_vouchers không
        $userId = auth()->id();
        $hasUserVoucherRecords = false;
        $unusedInWallet = 0;

        if ($userId && \Illuminate\Support\Facades\Schema::hasTable('user_vouchers')) {
            $userVoucherQuery = DB::table('user_vouchers')
                ->where('user_id', $userId)
                ->where('voucher_id', $voucher->voucher_id);
            
            $hasUserVoucherRecords = (clone $userVoucherQuery)->exists();
            if ($hasUserVoucherRecords) {
                $unusedInWallet = (clone $userVoucherQuery)->where('is_used', 0)->count();
            }
        }

        $isPointsExchange = isset($voucher->is_points_exchange) ? (bool)$voucher->is_points_exchange : false;

        if ($hasUserVoucherRecords) {
            if ($unusedInWallet <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã sử dụng hết số lượt của mã giảm giá này!'
                ]);
            }
        } elseif (!$isPointsExchange) {
            // Kiểm tra giới hạn lượt sử dụng / 1 khách hàng
            $usagePerUser = isset($voucher->usage_per_user) ? $voucher->usage_per_user : 1;
            if ($usagePerUser !== null && $usagePerUser > 0) {
                $customerPhone = auth()->check() ? auth()->user()->phone : trim($request->input('phone', ''));

                $userUsedCount = 0;
                if ($userId) {
                    $userUsedCount = DB::table('orders')
                        ->where('voucher_id', $voucher->voucher_id)
                        ->where('user_id', $userId)
                        ->where('status', '!=', 'cancelled')
                        ->count();
                } elseif (!empty($customerPhone)) {
                    $userUsedCount = DB::table('orders')
                        ->where('voucher_id', $voucher->voucher_id)
                        ->where('customer_phone', $customerPhone)
                        ->where('status', '!=', 'cancelled')
                        ->count();
                }

                if ($userUsedCount >= $usagePerUser) {
                    return response()->json([
                        'success' => false,
                        'message' => "Mã giảm giá này chỉ áp dụng tối đa {$usagePerUser} lần cho mỗi khách hàng. Bạn đã sử dụng mã này rồi!"
                    ]);
                }
            }
        }

        $discount = $voucher->discount_type === 'percent' ? $subTotal * ($voucher->discount_value / 100) : $voucher->discount_value;
        $discount = min($discount, $subTotal);

        session()->forget('voucher_opt_out');
        session()->put('voucher', [
            'voucher_id'      => $voucher->voucher_id,
            'code'            => $voucher->code,
            'discount_type'   => $voucher->discount_type,
            'discount_value'  => $voucher->discount_value,
            'discount_amount' => $discount,
            'min_order'       => $voucher->min_order,
        ]);

        return response()->json([
            'success'             => true,
            'message'             => 'Áp dụng mã giảm giá thành công!',
            'discount'            => $discount,
            'discount_formatted'  => number_format($discount, 0, ',', '.') . ' đ',
            'new_total'           => $subTotal - $discount,
            'new_total_formatted' => number_format($subTotal - $discount, 0, ',', '.') . ' đ',
        ]);
    }

    // 8. Hủy voucher
    public function removeVoucher(Request $request)
    {
        session()->forget('voucher');
        session()->put('voucher_opt_out', true);

        $cart     = self::getCart();
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        return response()->json([
            'success'         => true,
            'message'         => 'Đã hủy mã giảm giá.',
            'total'           => $subTotal,
            'total_formatted' => number_format($subTotal, 0, ',', '.') . ' đ',
        ]);
    }

    // 9. Helper: tính lại voucher sau mỗi thao tác giỏ hàng
    public static function checkAndRecalculateVoucher(): void
    {
        if (!session()->has('voucher')) return;

        $cart     = self::getCart();
        $subTotal = 0;
        foreach ($cart as $item) $subTotal += ($item['price'] + $item['topping_total']) * $item['quantity'];

        $voucherSession = session()->get('voucher');
        $voucher        = DB::table('vouchers')->where('voucher_id', $voucherSession['voucher_id'])->first();

        if (empty($cart) || !$voucher || $subTotal < $voucher->min_order) {
            session()->forget('voucher'); return;
        }

        $discount = $voucher->discount_type === 'percent'
            ? $subTotal * ($voucher->discount_value / 100)
            : $voucher->discount_value;

        $voucherSession['discount_amount'] = min($discount, $subTotal);
        session()->put('voucher', $voucherSession);
    }

    // 10. Số lượng giỏ hàng (AJAX)
    public function getCount()
    {
        $cart       = self::getCart();
        $totalItems = array_sum(array_column($cart, 'quantity'));
        return response()->json(['cart_count' => $totalItems]);
    }

    public static function syncCartItemsToDatabase()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('cart_items')) return;

        $cart = session()->get('cart', []);
        $userId = auth()->id() ?? (auth()->check() ? auth()->user()->user_id : null);

        if ($userId) {
            $cartId = DB::table('carts')->where('user_id', $userId)->value('cart_id');
            if (!$cartId) {
                $firstVariantId = DB::table('product_variants')->value('variant_id') ?? 1;
                $cartId = DB::table('carts')->insertGetId([
                    'user_id' => $userId,
                    'variant_id' => $firstVariantId,
                    'quantity' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::table('cart_items')->where('cart_id', $cartId)->delete();

            foreach ($cart as $item) {
                if (isset($item['product_id']) && $item['product_id'] > 0 && empty($item['is_combo'])) {
                    DB::table('cart_items')->insert([
                        'cart_id' => $cartId,
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                        'toppings' => json_encode($item['toppings'] ?? []),
                        'ice_level' => $item['ice_level'] ?? '100',
                        'sugar_level' => $item['sugar_level'] ?? '100',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    /**
     * NẠP GIỎ HÀNG TỪ CSDL (CHỐNG MẤT GIỎ KHI ĐĂNG NHẬP HOẶC MOUNT SESSION)
     */
    public static function loadCartFromDatabase()
    {
        if (!auth()->check() || !\Illuminate\Support\Facades\Schema::hasTable('cart_items')) return;

        $userId = auth()->id() ?? auth()->user()->user_id;
        $cartId = DB::table('carts')->where('user_id', $userId)->value('cart_id');
        if (!$cartId) return;

        $dbItems = DB::table('cart_items')->where('cart_id', $cartId)->get();
        if ($dbItems->isEmpty()) return;

        $cart = session()->get('cart', []);

        foreach ($dbItems as $dbItem) {
            $productId = $dbItem->product_id;
            $variantId = $dbItem->variant_id;
            $quantity = (int)$dbItem->quantity;
            $iceLevel = $dbItem->ice_level ?? '100';
            $sugarLevel = $dbItem->sugar_level ?? '100';
            $cleanToppings = json_decode($dbItem->toppings, true) ?? [];

            $product = DB::table('products')->where('product_id', $productId)->first();
            if (!$variantId && $product) {
                $firstVariant = DB::table('product_variants')->where('product_id', $product->product_id)->orderBy('price', 'asc')->first();
                if ($firstVariant) $variantId = $firstVariant->variant_id;
            }
            $variant = DB::table('product_variants')->where('variant_id', $variantId)->first();

            if ($product && $variant) {
                $toppingDetails = [];
                $toppingTotal = 0;

                foreach ($cleanToppings as $topId => $topVal) {
                    $topQty = is_array($topVal) ? ($topVal['qty'] ?? 0) : (int)$topVal;
                    $topProduct = DB::table('products')->where('product_id', $topId)->first();
                    $topPrice = DB::table('product_variants')->where('product_id', $topId)->min('price');
                    $topPrice = $topPrice ? (float) $topPrice : 0;

                    if ($topProduct && $topQty > 0) {
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

                $cartKey = md5($productId . '_' . $variantId . '_' . $iceLevel . '_' . $sugarLevel . '_' . json_encode($cleanToppings));

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
        }

        session()->put('cart', $cart);
    }

    /**
     * MUA LẠI TOÀN BỘ ĐƠN HÀNG
     */
    public function reorderAll($id)
    {
        $userId = auth()->user()->user_id ?? auth()->id();
        $order = DB::table('orders')
            ->where('order_id', $id)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng!');
        }

        $items = [];
        if (!empty($order->items)) {
            $decoded = json_decode($order->items, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            if (is_array($decoded)) {
                $items = $decoded;
            }
        }

        if (empty($items)) {
            return redirect()->back()->with('error', 'Đơn hàng không có sản phẩm để mua lại!');
        }

        $addedCount = 0;
        $skippedCount = 0;

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? $item['productId'] ?? $item['id'] ?? 0;
            $variantId = $item['variant_id'] ?? $item['variantId'] ?? null;
            $quantity = (int)($item['quantity'] ?? 1);
            $iceLevel = $item['ice_level'] ?? '100';
            $sugarLevel = $item['sugar_level'] ?? '100';
            $rawToppings = $item['toppings'] ?? [];

            $dummyReq = new Request([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'ice_level' => $iceLevel,
                'sugar_level' => $sugarLevel,
                'toppings' => $rawToppings
            ]);

            $res = $this->add($dummyReq);
            $resData = json_decode($res->getContent(), true);

            if (isset($resData['success']) && $resData['success']) {
                $addedCount++;
            } else {
                $skippedCount++;
            }
        }

        session()->save();

        if ($addedCount > 0) {
            $msg = "Đã mua lại thành công $addedCount món vào giỏ hàng!";
            if ($skippedCount > 0) {
                $msg .= " ($skippedCount món cũ đã ngừng kinh doanh nên hệ thống tự động bỏ qua)";
            }
            return redirect()->route('cart.index')->with('success', $msg);
        } else {
            return redirect()->back()->with('error', 'Rất tiếc! Tất cả các món trong đơn hàng này hiện đã bị xóa hoặc ngừng kinh doanh trên hệ thống.');
        }
    }
}
>>>>>>> 93d5a5baf4f7ec392b9f99808bc99fd21ddee2cc
