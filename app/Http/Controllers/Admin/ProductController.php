<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Xóa file ảnh khỏi Storage/Public để tránh rác bộ nhớ
     */
    protected function deleteImageFile(?string $imageUrl): void
    {
        if (empty($imageUrl)) {
            return;
        }

        // Không xóa ảnh placeholder hoặc domain bên ngoài
        if (Str::startsWith($imageUrl, ['http://', 'https://']) && !Str::contains($imageUrl, request()->getHost())) {
            return;
        }

        // Đường dẫn tương đối trong disk 'public'
        $relativePath = ltrim($imageUrl, '/');
        if (Str::startsWith($relativePath, 'storage/')) {
            $relativePath = Str::after($relativePath, 'storage/');
        }

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $publicPath = public_path(ltrim($imageUrl, '/'));
        if (file_exists($publicPath) && is_file($publicPath)) {
            @unlink($publicPath);
        }
    }

    public function index(Request $request)
    {
        $query = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.*', 'categories.name as category_name', DB::raw('MIN(product_variants.price) as price'))
            ->groupBy('products.product_id', 'products.name', 'products.slug', 'products.description', 'products.status', 'products.image_url', 'products.category_id', 'products.created_at', 'products.updated_at', 'categories.name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")->orWhere('products.product_id', $search);
            });
        }
        $products = $query->orderBy('products.created_at', 'desc')->paginate(10)->appends($request->all());
        
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = DB::table('categories')->get();
        $sizes = Schema::hasTable('sizes') 
            ? DB::table('sizes')->get()->sortBy(function($s) {
                return match(trim($s->name)) {
                    'Mặc định' => 1,
                    'Size S' => 2,
                    'Size M' => 3,
                    'Size L' => 4,
                    default => 99,
                };
            }) 
            : collect([]);

        $toppingCategory = DB::table('categories')->where('name', 'LIKE', '%topping%')->orWhere('name', 'LIKE', '%Topping%')->first();
        $allToppings = $toppingCategory ? DB::table('products')->where('category_id', $toppingCategory->category_id)->select('product_id as topping_id', 'name')->get() : collect([]);

        return view('admin.products.create', compact('categories', 'sizes', 'allToppings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'status' => 'required',
        ]);

        // XỬ LÝ ẢNH CHÍNH (Ưu tiên File Upload, nếu không có thì lấy Link URL, mặc định ảnh mẫu)
        $mainImageUrl = $request->image_url;
        if ($request->hasFile('image_file')) {
            $mainImageUrl = '/storage/' . $request->file('image_file')->store('products', 'public');
        }

        $productId = DB::table('products')->insertGetId([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'category_id' => $request->category_id,
            'status' => $request->status,
            'image_url' => $mainImageUrl ?: '/images/trasuaccdd.jpg',
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Lưu Giá Size
        $this->syncProductVariants($productId, $request->input('prices'), $request->input('variants'), $request->input('custom_sizes'));

        // XỬ LÝ ẢNH PHỤ
        if (Schema::hasTable('product_images')) {
            for ($i = 0; $i < 3; $i++) {
                $extraUrl = $request->extra_images[$i] ?? null;
                if ($request->hasFile("extra_image_files.$i")) {
                    $extraUrl = '/storage/' . $request->file("extra_image_files.$i")->store('products', 'public');
                }
                
                if (!empty($extraUrl)) {
                    DB::table('product_images')->insert([
                        'product_id' => $productId, 'image_url' => $extraUrl, 'created_at' => now(), 'updated_at' => now()
                    ]);
                }
            }
        }

        // Lưu Topping
        if (Schema::hasTable('product_toppings') && $request->has('toppings')) {
            foreach ($request->toppings as $toppingId) {
                DB::table('product_toppings')->insert(['product_id' => $productId, 'topping_id' => $toppingId]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Đã thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) abort(404);

        $categories = DB::table('categories')->get();
        $sizes = Schema::hasTable('sizes') 
            ? DB::table('sizes')->get()->sortBy(function($s) {
                return match(trim($s->name)) {
                    'Mặc định' => 1,
                    'Size S' => 2,
                    'Size M' => 3,
                    'Size L' => 4,
                    default => 99,
                };
            }) 
            : collect([]);

        // Lấy danh sách Giá/Size đang có
        $variants = DB::table('product_variants')
            ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
            ->where('product_id', $id)
            ->select('product_variants.*', 'sizes.name as size_name')
            ->get();

        $extraImages = Schema::hasTable('product_images') ? DB::table('product_images')->where('product_id', $id)->limit(3)->get() : collect([]);

        $toppingCategory = DB::table('categories')->where('name', 'LIKE', '%topping%')->orWhere('name', 'LIKE', '%Topping%')->first();
        $allToppings = $toppingCategory ? DB::table('products')->where('category_id', $toppingCategory->category_id)->select('product_id as topping_id', 'name')->get() : collect([]);
        $selectedToppings = Schema::hasTable('product_toppings') ? DB::table('product_toppings')->where('product_id', $id)->pluck('topping_id')->toArray() : [];

        return view('admin.products.edit', compact('product', 'categories', 'variants', 'extraImages', 'allToppings', 'selectedToppings', 'sizes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|max:255', 'category_id' => 'required', 'status' => 'required']);

        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) abort(404);

        // XỬ LÝ CẬP NHẬT ẢNH CHÍNH & XÓA ẢNH CŨ NẾU THAY ĐỔI
        $mainImageUrl = $product->image_url;
        if ($request->hasFile('image_file')) {
            $this->deleteImageFile($product->image_url);
            $mainImageUrl = '/storage/' . $request->file('image_file')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            if ($request->image_url !== $product->image_url) {
                $this->deleteImageFile($product->image_url);
            }
            $mainImageUrl = $request->image_url;
        }

        DB::table('products')->where('product_id', $id)->update([
            'name' => $request->name, 
            'category_id' => $request->category_id, 
            'status' => $request->status,
            'image_url' => $mainImageUrl ?: '/images/trasuaccdd.jpg', 
            'description' => $request->description, 
            'updated_at' => now()
        ]);

        // XỬ LÝ CẬP NHẬT, THÊM VÀ XÓA GIÁ THEO KÍCH CỠ (SIZE)
        $this->syncProductVariants($id, $request->input('prices'), $request->input('variants'), $request->input('custom_sizes'));

        // XỬ LÝ CẬP NHẬT ẢNH PHỤ & XÓA ẢNH CŨ
        if (Schema::hasTable('product_images')) {
            $extraImageIds = $request->extra_image_ids ?? [];
            for ($i = 0; $i < 3; $i++) {
                $imageId = $extraImageIds[$i] ?? null;
                $extraUrl = $request->extra_images[$i] ?? null;
                $oldImage = $imageId ? DB::table('product_images')->where('id', $imageId)->first() : null;
                
                if ($request->hasFile("extra_image_files.$i")) {
                    if ($oldImage) {
                        $this->deleteImageFile($oldImage->image_url);
                    }
                    $extraUrl = '/storage/' . $request->file("extra_image_files.$i")->store('products', 'public');
                }

                if ($imageId) {
                    if (empty($extraUrl)) {
                        if ($oldImage) {
                            $this->deleteImageFile($oldImage->image_url);
                        }
                        DB::table('product_images')->where('id', $imageId)->delete();
                    } else {
                        if ($oldImage && $oldImage->image_url !== $extraUrl && !$request->hasFile("extra_image_files.$i")) {
                            $this->deleteImageFile($oldImage->image_url);
                        }
                        DB::table('product_images')->where('id', $imageId)->update(['image_url' => $extraUrl, 'updated_at' => now()]);
                    }
                } else {
                    if (!empty($extraUrl)) {
                        DB::table('product_images')->insert(['product_id' => $id, 'image_url' => $extraUrl, 'created_at' => now(), 'updated_at' => now()]);
                    }
                }
            }
        }

        // XỬ LÝ CẬP NHẬT TOPPING
        if (Schema::hasTable('product_toppings')) {
            DB::table('product_toppings')->where('product_id', $id)->delete();
            if ($request->has('toppings')) {
                foreach ($request->toppings as $toppingId) {
                    DB::table('product_toppings')->insert(['product_id' => $id, 'topping_id' => $toppingId]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy($id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Sản phẩm không tồn tại!');
        }

        // 1. Kiểm tra xem sản phẩm có nằm trong GIỎ HÀNG KHÁCH HÀNG (cart_items / carts / session) không
        $inCart = false;
        if (Schema::hasTable('cart_items')) {
            $inCart = DB::table('cart_items')->where('product_id', $id)->exists();
        }
        if (!$inCart && Schema::hasTable('carts')) {
            $variantIds = DB::table('product_variants')->where('product_id', $id)->pluck('variant_id')->toArray();
            if (!empty($variantIds)) {
                $inCart = DB::table('carts')->whereIn('variant_id', $variantIds)->exists();
            }
        }
        if (!$inCart) {
            $sessionCart = session()->get('cart', []);
            foreach ($sessionCart as $item) {
                if (isset($item['product_id']) && $item['product_id'] == $id) {
                    $inCart = true;
                    break;
                }
            }
        }

        if ($inCart) {
            return redirect()->route('products.index')->with('error', '⚠️ Không thể xóa vĩnh viễn! Sản phẩm này hiện đang nằm trong GIỎ HÀNG của khách hàng. Vui lòng chuyển trạng thái sản phẩm sang "Ngừng bán" (Xóa mềm) để đảm bảo không làm hỏng giỏ hàng của khách hàng!');
        }

        // 2. Kiểm tra xem sản phẩm có xuất hiện trong LỊCH SỬ ĐƠN HÀNG không
        $hasOrderHistory = false;

        if (Schema::hasTable('order_items')) {
            $hasOrderHistory = DB::table('order_items')->where('product_id', $id)->exists();
        }

        if (!$hasOrderHistory && Schema::hasTable('orders')) {
            $hasOrderHistory = DB::table('orders')
                ->where('items', 'LIKE', '%"product_id":' . $id . '%')
                ->orWhere('items', 'LIKE', '%"product_id":"' . $id . '"%')
                ->orWhere('items', 'LIKE', '%"id":' . $id . '%')
                ->orWhere('items', 'LIKE', '%"id":"' . $id . '"%')
                ->exists();
        }

        // 3. NẾU CÓ TRONG ĐƠN HÀNG -> THỰC HIỆN XÓA MỀM (Chuyển status sang 0: Ngừng bán)
        if ($hasOrderHistory) {
            DB::table('products')->where('product_id', $id)->update([
                'status' => 0,
                'updated_at' => now()
            ]);

            return redirect()->route('products.index')->with('success', 'Sản phẩm đã nằm trong lịch sử đơn hàng của khách! Hệ thống đã chuyển trạng thái sang "Ngừng bán" (Xóa mềm) để bảo toàn dữ liệu lịch sử đơn hàng.');
        }

        // 3. NẾU CHƯA TỪNG CÓ TRONG ĐƠN HÀNG NÀO -> XÓA VĨNH VIỄN & GIẢI PHÓNG BỘ NHỚ FILE ẢNH
        $this->deleteImageFile($product->image_url);

        if (Schema::hasTable('product_images')) {
            $extraImages = DB::table('product_images')->where('product_id', $id)->get();
            foreach ($extraImages as $img) {
                $this->deleteImageFile($img->image_url);
            }
            DB::table('product_images')->where('product_id', $id)->delete();
        }

        if (Schema::hasTable('product_variants')) {
            DB::table('product_variants')->where('product_id', $id)->delete();
        }
        if (Schema::hasTable('product_toppings')) {
            DB::table('product_toppings')->where('product_id', $id)->delete();
        }

        DB::table('products')->where('product_id', $id)->delete();

        return redirect()->route('products.index')->with('success', 'Đã xóa vĩnh viễn sản phẩm và toàn bộ file ảnh khỏi hệ thống!');
    }

    /**
     * Ghim món làm Sản phẩm Nổi Bật hiển thị ở Banner Hero Trang Chủ
     */
    public function toggleFeatured($id)
    {
        if (!Schema::hasColumn('products', 'is_featured')) {
            Schema::table('products', function ($table) {
                $table->boolean('is_featured')->default(0)->after('status');
            });
        }

        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) abort(404);

        $newStatus = empty($product->is_featured) ? 1 : 0;

        if ($newStatus == 1) {
            DB::table('products')->update(['is_featured' => 0]);
        }

        DB::table('products')->where('product_id', $id)->update(['is_featured' => $newStatus]);

        $msg = $newStatus == 1 
            ? 'Đã ghim món "' . $product->name . '" làm Sản phẩm Nổi Bật trên Banner Hero Trang Chủ!' 
            : 'Đã bỏ ghim món "' . $product->name . '" trên Banner Hero!';

        return back()->with('success', $msg);
    }

    /**
     * Xóa nhiều sản phẩm được chọn
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một sản phẩm để xóa!');
        }

        $deletedCount = 0;
        $softDeletedCount = 0;

        foreach ($ids as $id) {
            $product = DB::table('products')->where('product_id', $id)->first();
            if (!$product) continue;

            $hasOrders = Schema::hasTable('order_items') && DB::table('order_items')->where('product_id', $id)->exists();

            if ($hasOrders) {
                DB::table('products')->where('product_id', $id)->update([
                    'status' => 0,
                    'updated_at' => now()
                ]);
                $softDeletedCount++;
            } else {
                $this->deleteImageFile($product->image_url);

                if (Schema::hasTable('product_images')) {
                    $extraImages = DB::table('product_images')->where('product_id', $id)->get();
                    foreach ($extraImages as $img) {
                        $this->deleteImageFile($img->image_url);
                    }
                    DB::table('product_images')->where('product_id', $id)->delete();
                }
                if (Schema::hasTable('product_variants')) {
                    DB::table('product_variants')->where('product_id', $id)->delete();
                }
                if (Schema::hasTable('product_toppings')) {
                    DB::table('product_toppings')->where('product_id', $id)->delete();
                }
                DB::table('products')->where('product_id', $id)->delete();
                $deletedCount++;
            }
        }

        $msg = "Đã xử lý xóa các sản phẩm được chọn!";
        if ($deletedCount > 0) $msg .= " (Xóa vĩnh viễn: {$deletedCount})";
        if ($softDeletedCount > 0) $msg .= " (Ngừng bán do có đơn hàng: {$softDeletedCount})";

        return back()->with('success', $msg);
    }

    /**
     * Đồng bộ danh sách Giá theo Size (bảo toàn size_id và chống lỗi khóa ngoại FK 1452)
     */
    protected function syncProductVariants($productId, $prices = null, $variants = null, $customSizes = null)
    {
        if (!Schema::hasTable('product_variants')) return;

        $activeSizeIds = [];
        $pricesArr = is_array($prices) ? $prices : [];
        $variantsArr = is_array($variants) ? $variants : [];
        $variantData = $pricesArr + $variantsArr; // Dùng + để giữ nguyên key size_id thay vì array_merge làm đánh lại chỉ số từ 0

        $validSizeIds = DB::table('sizes')->pluck('size_id')->map(fn($id) => (int)$id)->toArray();

        // 1. Lưu các size sẵn có
        foreach ($variantData as $sizeId => $price) {
            $sizeId = (int)$sizeId;
            if (in_array($sizeId, $validSizeIds) && $price !== null && $price !== '') {
                DB::table('product_variants')->updateOrInsert(
                    ['product_id' => $productId, 'size_id' => $sizeId],
                    ['price' => $price]
                );
                $activeSizeIds[] = $sizeId;
            }
        }

        // 2. Lưu các size mới nạp động (nếu có)
        if (is_array($customSizes)) {
            foreach ($customSizes as $custom) {
                $name = trim($custom['name'] ?? '');
                $price = $custom['price'] ?? null;
                if (!empty($name) && $price !== null && $price !== '') {
                    if (!Str::startsWith(strtolower($name), 'size') && $name !== 'Mặc định') {
                        $name = 'Size ' . ucfirst($name);
                    }
                    $size = DB::table('sizes')->where('name', $name)->first();
                    if (!$size) {
                        $sizeId = DB::table('sizes')->insertGetId([
                            'name' => $name,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        $sizeId = (int)$size->size_id;
                    }

                    DB::table('product_variants')->updateOrInsert(
                        ['product_id' => $productId, 'size_id' => $sizeId],
                        ['price' => $price]
                    );
                    $activeSizeIds[] = $sizeId;
                }
            }
        }

        // 3. Xóa các size không được điền giá khỏi sản phẩm này
        DB::table('product_variants')
            ->where('product_id', $productId)
            ->whereNotIn('size_id', array_unique($activeSizeIds))
            ->delete();
    }
}