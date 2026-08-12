<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

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

        if (Str::startsWith($imageUrl, ['http://', 'https://']) && !Str::contains($imageUrl, request()->getHost())) {
            return;
        }

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
            ->select(
                'products.product_id', 
                'products.name', 
                'products.slug', 
                'products.description', 
                'products.status', 
                'products.image_url', 
                'products.category_id', 
                'products.created_at', 
                'products.updated_at',
                'categories.name as category_name',
                DB::raw('MIN(product_variants.price) as price')
            )
            ->groupBy(
                'products.product_id', 
                'products.name', 
                'products.slug', 
                'products.description', 
                'products.status', 
                'products.image_url', 
                'products.category_id', 
                'products.created_at', 
                'products.updated_at',
                'categories.name'
            );

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
        $sizes = Schema::hasTable('sizes') ? DB::table('sizes')->get() : collect([]);

        $allToppings = DB::table('toppings')->where('status', 1)->get();

        return view('admin.products.create', compact('categories', 'sizes', 'allToppings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'status' => 'required',
        ]);

        $mainImageUrl = $request->image_url;
        if ($request->hasFile('image_file')) {
            $mainImageUrl = '/storage/' . $request->file('image_file')->store('products', 'public');
        }

        $price = $request->price ?? 0;
        if ($request->has('prices') && is_array($request->prices)) {
            $validPrices = array_filter($request->prices);
            if (!empty($validPrices)) {
                $price = min($validPrices);
            }
        }

        $productId = DB::table('products')->insertGetId([
            'name' => $request->name,
            'price' => $price,
            'slug' => Str::slug($request->name) . '-' . time(),
            'category_id' => $request->category_id,
            'status' => $request->status,
            'image_url' => $mainImageUrl ?? 'https://via.placeholder.com/600',
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->has('prices') && is_array($request->prices)) {
            foreach ($request->prices as $sizeId => $p) {
                if (!empty($p)) {
                    DB::table('product_variants')->insert([
                        'product_id' => $productId, 
                        'size_id' => $sizeId, 
                        'price' => $p
                    ]);
                }
            }
        }

        if ($request->has('toppings')) {
            $insertData = [];
            foreach ($request->toppings as $toppingId) {
                $insertData[] = [
                    'product_id' => $productId,
                    'topping_id' => $toppingId
                ];
            }
            if (Schema::hasTable('product_topping')) {
                DB::table('product_topping')->insert($insertData);
            }
        }

        Cache::forget('home_products');

        return redirect()->route('products.index')->with('success', 'Đã thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) abort(404);

        $categories = DB::table('categories')->get();
        $sizes = Schema::hasTable('sizes') ? DB::table('sizes')->get() : collect([]);

        $variants = DB::table('product_variants')
            ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
            ->where('product_id', $id)
            ->select('product_variants.*', 'sizes.name as size_name')
            ->get();

        $allToppings = DB::table('toppings')->where('status', 1)->get();
        $selectedToppings = Schema::hasTable('product_topping') ? DB::table('product_topping')->where('product_id', $id)->pluck('topping_id')->toArray() : [];

        return view('admin.products.edit', compact('product', 'categories', 'variants', 'allToppings', 'selectedToppings', 'sizes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|max:255', 'category_id' => 'required', 'status' => 'required']);

        $product = DB::table('products')->where('product_id', $id)->first();
        if (!$product) abort(404);

        $mainImageUrl = $request->image_url;
        if ($request->hasFile('image_file')) {
            $this->deleteImageFile($product->image_url);
            $mainImageUrl = '/storage/' . $request->file('image_file')->store('products', 'public');
        } elseif ($mainImageUrl && $mainImageUrl !== $product->image_url) {
            $this->deleteImageFile($product->image_url);
        }

        $price = $request->price ?? $product->price ?? 0;
        if ($request->has('variants') && is_array($request->variants)) {
            $validPrices = array_filter($request->variants);
            if (!empty($validPrices)) {
                $price = min($validPrices);
            }
        }

        DB::table('products')->where('product_id', $id)->update([
            'name' => $request->name, 
            'price' => $price,
            'category_id' => $request->category_id, 
            'status' => $request->status,
            'image_url' => $mainImageUrl ?: $product->image_url, 
            'description' => $request->description, 
            'updated_at' => now()
        ]);

        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $sizeId => $newPrice) {
                if ($newPrice !== null && $newPrice !== '') {
                    DB::table('product_variants')->updateOrInsert(
                        ['product_id' => $id, 'size_id' => $sizeId],
                        ['price' => $newPrice]
                    );
                }
            }
        }

        if (Schema::hasTable('product_topping')) {
            DB::table('product_topping')->where('product_id', $id)->delete();
            if ($request->has('toppings')) {
                $insertData = [];
                foreach ($request->toppings as $toppingId) {
                    $insertData[] = [
                        'product_id' => $id,
                        'topping_id' => $toppingId
                    ];
                }
                DB::table('product_topping')->insert($insertData);
            }
        }

        Cache::forget('home_products');

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
        if (Schema::hasTable('orders')) {
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

            return redirect()->route('products.index')->with('success', 'Sản phẩm đã nằm trong lịch sử đơn hàng! Hệ thống đã chuyển trạng thái sang "Ngừng bán".');
        }

        $this->deleteImageFile($product->image_url);

        if (Schema::hasTable('product_variants')) {
            DB::table('product_variants')->where('product_id', $id)->delete();
        }
        if (Schema::hasTable('product_topping')) {
            DB::table('product_topping')->where('product_id', $id)->delete();
        }

        DB::table('products')->where('product_id', $id)->delete();
        Cache::forget('home_products');

        return redirect()->route('products.index')->with('success', 'Đã xóa vĩnh viễn sản phẩm khỏi hệ thống!');
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
}