<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSalesController extends Controller
{
    /**
     * Hiển thị bảng theo dõi lượt bán và quản lý giảm giá kích cầu
     */
    public function index(Request $request)
    {
        // 1. LẤY TẤT CẢ DANH MỤC ĐỂ LÀM BỘ LỌC
        $categories = DB::table('categories')->orderBy('name', 'asc')->get();

        // 2. TỔNG HỢP LƯỢT BÁN VÀ DOANH THU TỪNG SẢN PHẨM TỪ BẢNG ORDERS
        $salesStats = []; // [product_id => ['sold_count' => X, 'revenue' => Y]]
        
        $orders = DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('items')
            ->select('items')
            ->get();

        foreach ($orders as $order) {
            $items = json_decode($order->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $qty = (int)($item['quantity'] ?? 1);
                    $price = (float)($item['price'] ?? 0);

                    if ($productId) {
                        if (!isset($salesStats[$productId])) {
                            $salesStats[$productId] = ['sold_count' => 0, 'revenue' => 0];
                        }
                        $salesStats[$productId]['sold_count'] += $qty;
                        $salesStats[$productId]['revenue'] += ($price * $qty);
                    }
                }
            }
        }

        // 3. LẤY DANH SÁCH TẤT CẢ SẢN PHẨM KÈM GIÁ BIẾN THỂ VÀ DANH MỤC
        $productsQuery = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select(
                'products.product_id',
                'products.name',
                'products.slug',
                'products.image_url',
                'products.category_id',
                'products.status',
                'products.is_featured',
                'products.discount_percent',
                'products.created_at',
                'categories.name as category_name',
                DB::raw('MIN(product_variants.price) as min_price'),
                DB::raw('MAX(product_variants.price) as max_price')
            )
            ->groupBy(
                'products.product_id',
                'products.name',
                'products.slug',
                'products.image_url',
                'products.category_id',
                'products.status',
                'products.is_featured',
                'products.discount_percent',
                'products.created_at',
                'categories.name'
            );

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $productsQuery->where('products.category_id', $request->category_id);
        }

        // Tìm kiếm theo tên sản phẩm
        if ($request->filled('search')) {
            $productsQuery->where('products.name', 'like', '%' . trim($request->search) . '%');
        }

        $allProducts = $productsQuery->get();

        // 4. BỔ SUNG CÁC CHỈ SỐ BÁN HÀNG VÀO TỪNG SẢN PHẨM
        $enrichedProducts = $allProducts->map(function ($p) use ($salesStats) {
            $sold = $salesStats[$p->product_id]['sold_count'] ?? 0;
            $rev = $salesStats[$p->product_id]['revenue'] ?? 0;
            $discount = (int)($p->discount_percent ?? 0);
            $minPrice = (float)($p->min_price ?? 0);
            $maxPrice = (float)($p->max_price ?? $minPrice);

            // Tính giá sau giảm
            $saleMinPrice = $discount > 0 ? round($minPrice * (100 - $discount) / 100) : $minPrice;
            $saleMaxPrice = $discount > 0 ? round($maxPrice * (100 - $discount) / 100) : $maxPrice;

            // Đánh giá cấp độ bán
            $tier = 'low'; // Lượt mua thấp <= 5
            $tierLabel = 'Lượt mua thấp';
            if ($sold > 20) {
                $tier = 'hot';
                $tierLabel = 'Bán chạy';
            } elseif ($sold > 5) {
                $tier = 'normal';
                $tierLabel = 'Ổn định';
            }

            $p->sold_count = $sold;
            $p->total_revenue = $rev;
            $p->discount_percent = $discount;
            $p->sale_min_price = $saleMinPrice;
            $p->sale_max_price = $saleMaxPrice;
            $p->performance_tier = $tier;
            $p->tier_label = $tierLabel;

            return $p;
        });

        // 5. TÍNH TOÁN CÁC THỐNG KÊ TỔNG QUAN (METRICS)
        $totalProductsCount = $enrichedProducts->count();
        $lowSalesCount = $enrichedProducts->where('performance_tier', 'low')->count();
        $discountedCount = $enrichedProducts->where('discount_percent', '>', 0)->count();
        $hotSalesCount = $enrichedProducts->where('performance_tier', 'hot')->count();
        $totalSoldVolume = $enrichedProducts->sum('sold_count');
        $totalRevenueVolume = $enrichedProducts->sum('total_revenue');

        // 6. LỌC THEO TAB TRẠNG THÁI (FILTER TAB)
        $currentFilter = $request->input('filter', 'all');
        if ($currentFilter === 'low') {
            $filteredList = $enrichedProducts->where('performance_tier', 'low');
        } elseif ($currentFilter === 'normal') {
            $filteredList = $enrichedProducts->where('performance_tier', 'normal');
        } elseif ($currentFilter === 'hot') {
            $filteredList = $enrichedProducts->where('performance_tier', 'hot');
        } elseif ($currentFilter === 'discounted') {
            $filteredList = $enrichedProducts->where('discount_percent', '>', 0);
        } else {
            $filteredList = $enrichedProducts;
        }

        // 7. SẮP XẾP DANH SÁCH (SORTING)
        $sortBy = $request->input('sort_by', 'sold_asc');
        switch ($sortBy) {
            case 'sold_desc':
                $filteredList = $filteredList->sortByDesc('sold_count');
                break;
            case 'sold_asc':
                $filteredList = $filteredList->sortBy('sold_count');
                break;
            case 'revenue_desc':
                $filteredList = $filteredList->sortByDesc('total_revenue');
                break;
            case 'discount_desc':
                $filteredList = $filteredList->sortByDesc('discount_percent');
                break;
            case 'name_asc':
                $filteredList = $filteredList->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            default:
                $filteredList = $filteredList->sortBy('sold_count');
                break;
        }

        // Phân trang thủ công Collection
        $perPage = 15;
        $currentPage = (int)$request->input('page', 1);
        $pagedData = $filteredList->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $filteredList->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.products.sales_tracker', compact(
            'products',
            'categories',
            'currentFilter',
            'totalProductsCount',
            'lowSalesCount',
            'discountedCount',
            'hotSalesCount',
            'totalSoldVolume',
            'totalRevenueVolume'
        ));
    }

    /**
     * Cập nhật mức giảm giá cho 1 sản phẩm
     */
    public function updateDiscount(Request $request, $id)
    {
        $request->validate([
            'discount_percent' => 'required|integer|min:0|max:90'
        ], [
            'discount_percent.required' => 'Vui lòng nhập mức giảm giá %',
            'discount_percent.min' => 'Mức giảm giá tối thiểu là 0%',
            'discount_percent.max' => 'Mức giảm giá tối đa là 90%'
        ]);

        $discount = (int)$request->discount_percent;

        DB::table('products')->where('product_id', $id)->update([
            'discount_percent' => $discount,
            'updated_at' => now()
        ]);

        $productName = DB::table('products')->where('product_id', $id)->value('name');

        $msg = $discount > 0 
            ? "Đã áp dụng giảm giá {$discount}% cho món '{$productName}' thành công!"
            : "Đã hủy giảm giá cho món '{$productName}', khôi phục về giá gốc!";

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'discount' => $discount]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Áp dụng giảm giá hàng loạt cho các sản phẩm được chọn
     */
    public function bulkDiscount(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'discount_percent' => 'required|integer|min:1|max:90'
        ], [
            'product_ids.required' => 'Vui lòng chọn ít nhất 1 sản phẩm để áp dụng giảm giá!',
            'discount_percent.required' => 'Vui lòng nhập mức giảm giá %',
            'discount_percent.min' => 'Mức giảm giá tối thiểu là 1%',
            'discount_percent.max' => 'Mức giảm giá tối đa là 90%'
        ]);

        $ids = $request->product_ids;
        $discount = (int)$request->discount_percent;

        DB::table('products')->whereIn('product_id', $ids)->update([
            'discount_percent' => $discount,
            'updated_at' => now()
        ]);

        return back()->with('success', "Đã áp dụng giảm giá {$discount}% đồng loạt cho " . count($ids) . " sản phẩm thành công!");
    }

    /**
     * Hủy giảm giá hàng loạt (đưa về 0%)
     */
    public function bulkResetDiscount(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1'
        ], [
            'product_ids.required' => 'Vui lòng chọn ít nhất 1 sản phẩm để hủy giảm giá!'
        ]);

        $ids = $request->product_ids;

        DB::table('products')->whereIn('product_id', $ids)->update([
            'discount_percent' => 0,
            'updated_at' => now()
        ]);

        return back()->with('success', "Đã khôi phục giá gốc (0% giảm giá) cho " . count($ids) . " sản phẩm đã chọn!");
    }

    /**
     * Tự động quét và áp dụng giảm giá cho N sản phẩm có lượt mua thấp nhất (giảm từ 2% - 10%)
     */
    public static function autoApplyWeeklyDiscount($count = 5, $minDiscount = 2, $maxDiscount = 10, $resetOthers = true)
    {
        // 1. Tổng hợp lượt bán từ bảng orders (không tính đơn hủy)
        $salesStats = [];
        $orders = DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('items')
            ->select('items')
            ->get();

        foreach ($orders as $order) {
            $items = json_decode($order->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $pId = $item['product_id'] ?? null;
                    $qty = (int)($item['quantity'] ?? 1);
                    if ($pId) {
                        $salesStats[$pId] = ($salesStats[$pId] ?? 0) + $qty;
                    }
                }
            }
        }

        // 2. Lấy tất cả sản phẩm đang bán (status = 1), loại bỏ Topping
        $toppingCatIds = DB::table('categories')
            ->where('name', 'LIKE', '%topping%')
            ->orWhere('name', 'LIKE', '%Topping%')
            ->pluck('category_id')
            ->toArray();

        $query = DB::table('products')->where('status', 1);
        if (!empty($toppingCatIds)) {
            $query->whereNotIn('category_id', $toppingCatIds);
        }
        $products = $query->get();

        if ($products->isEmpty()) {
            return [];
        }

        // Gắn sold_count và sắp xếp tăng dần theo lượt bán (ít nhất đứng trước)
        $sorted = $products->map(function ($p) use ($salesStats) {
            $p->sold_count = $salesStats[$p->product_id] ?? 0;
            return $p;
        })->sortBy('sold_count')->values();

        // Lấy N sản phẩm đầu tiên (lượt mua ít nhất)
        $selected = $sorted->take($count);
        $selectedIds = $selected->pluck('product_id')->toArray();

        // 3. Đưa các sản phẩm khác về giá gốc (0% discount) nếu resetOthers = true
        if ($resetOthers) {
            DB::table('products')->whereNotIn('product_id', $selectedIds)->update(['discount_percent' => 0]);
        }

        // 4. Áp dụng mức giảm giá từ 2% đến 10% cho từng món
        $results = [];
        foreach ($selected as $p) {
            $discount = rand($minDiscount, $maxDiscount);
            DB::table('products')->where('product_id', $p->product_id)->update([
                'discount_percent' => $discount,
                'updated_at' => now()
            ]);
            $results[] = [
                'product_id' => $p->product_id,
                'name' => $p->name,
                'sold_count' => $p->sold_count,
                'discount_percent' => $discount
            ];
        }

        return $results;
    }

    /**
     * Kích hoạt tự động giảm giá cho 5 món bán thấp nhất từ giao diện Admin
     */
    public function autoDiscount(Request $request)
    {
        $count = (int)$request->input('count', 5);
        $min = (int)$request->input('min', 2);
        $max = (int)$request->input('max', 10);

        if ($count < 1) $count = 5;
        if ($min < 1) $min = 2;
        if ($max > 90) $max = 10;
        if ($min > $max) $min = $max;

        $results = self::autoApplyWeeklyDiscount($count, $min, $max, true);

        if (empty($results)) {
            return back()->with('error', 'Không tìm thấy sản phẩm nào để áp dụng giảm giá!');
        }

        $itemsSummary = collect($results)->map(function ($item) {
            return "{$item['name']} (Đã bán: {$item['sold_count']} ly ➔ Giảm {$item['discount_percent']}%)";
        })->implode(', ');

        $msg = "Đã kích hoạt giảm giá kích cầu tuần cho 5 món bán thấp nhất: " . $itemsSummary;

        return back()->with('success', $msg);
    }
}
