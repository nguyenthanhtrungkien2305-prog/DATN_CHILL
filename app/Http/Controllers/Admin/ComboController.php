<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\ComboItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $query = Combo::with(['products']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $combos = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        return view('admin.combos.index', compact('combos'));
    }

    public function create()
    {
        // Lấy toàn bộ sản phẩm active kèm giá nhỏ nhất từ biến thể
        $products = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.product_id', 'products.name', 'products.image_url', DB::raw('COALESCE(MIN(product_variants.price), 0) as price'))
            ->where('products.status', 1)
            ->groupBy('products.product_id', 'products.name', 'products.image_url')
            ->get();

        return view('admin.combos.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'image_url' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'name.required' => 'Vui lòng nhập tên gói Combo.',
            'price.required' => 'Vui lòng nhập giá bán của Combo.',
            'items.required' => 'Combo phải chọn ít nhất 1 sản phẩm.',
            'items.min' => 'Combo phải chọn ít nhất 1 sản phẩm.',
        ]);

        // Upload ảnh
        $imageUrl = $request->input('image_url');
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/combos'), $fileName);
            $imageUrl = '/uploads/combos/' . $fileName;
        }

        // Tự động tính giá gốc nếu chưa nhập
        $originalPrice = $request->original_price;
        if (!$originalPrice) {
            $originalPrice = 0;
            foreach ($request->items as $item) {
                $p = DB::table('products')
                    ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                    ->where('products.product_id', $item['product_id'])
                    ->select(DB::raw('COALESCE(MIN(product_variants.price), 0) as price'))
                    ->groupBy('products.product_id')
                    ->first();
                if ($p) {
                    $originalPrice += $p->price * $item['quantity'];
                }
            }
        }

        $slug = Str::slug($request->name);
        $countSlug = Combo::where('slug', 'LIKE', "{$slug}%")->count();
        if ($countSlug > 0) {
            $slug .= '-' . ($countSlug + 1);
        }

        $combo = Combo::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'original_price' => $originalPrice,
            'price' => $request->price,
            'image_url' => $imageUrl,
            'status' => $request->status,
        ]);

        // Lưu sản phẩm thuộc combo
        foreach ($request->items as $item) {
            ComboItem::create([
                'combo_id' => $combo->combo_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('combos.index')->with('success', 'Tạo gói Combo sản phẩm thành công!');
    }

    public function edit($id)
    {
        $combo = Combo::with('items')->findOrFail($id);

        $products = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->select('products.product_id', 'products.name', 'products.image_url', DB::raw('COALESCE(MIN(product_variants.price), 0) as price'))
            ->where('products.status', 1)
            ->groupBy('products.product_id', 'products.name', 'products.image_url')
            ->get();

        $selectedProducts = $combo->items->pluck('quantity', 'product_id')->toArray();

        return view('admin.combos.edit', compact('combo', 'products', 'selectedProducts'));
    }

    public function update(Request $request, $id)
    {
        $combo = Combo::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'image_url' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'name.required' => 'Vui lòng nhập tên gói Combo.',
            'price.required' => 'Vui lòng nhập giá bán của Combo.',
            'items.required' => 'Combo phải chọn ít nhất 1 sản phẩm.',
            'items.min' => 'Combo phải chọn ít nhất 1 sản phẩm.',
        ]);

        $imageUrl = $combo->image_url;
        if ($request->filled('image_url')) {
            $imageUrl = $request->image_url;
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/combos'), $fileName);
            $imageUrl = '/uploads/combos/' . $fileName;
        }

        $originalPrice = $request->original_price;
        if (!$originalPrice) {
            $originalPrice = 0;
            foreach ($request->items as $item) {
                $p = DB::table('products')
                    ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                    ->where('products.product_id', $item['product_id'])
                    ->select(DB::raw('COALESCE(MIN(product_variants.price), 0) as price'))
                    ->groupBy('products.product_id')
                    ->first();
                if ($p) {
                    $originalPrice += $p->price * $item['quantity'];
                }
            }
        }

        $combo->update([
            'name' => $request->name,
            'description' => $request->description,
            'original_price' => $originalPrice,
            'price' => $request->price,
            'image_url' => $imageUrl,
            'status' => $request->status,
        ]);

        // Cập nhật các item trong combo
        ComboItem::where('combo_id', $combo->combo_id)->delete();
        foreach ($request->items as $item) {
            ComboItem::create([
                'combo_id' => $combo->combo_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('combos.index')->with('success', 'Cập nhật gói Combo thành công!');
    }

    public function destroy($id)
    {
        $combo = Combo::findOrFail($id);
        $combo->delete();

        return redirect()->route('combos.index')->with('success', 'Xóa gói Combo thành công!');
    }
}
