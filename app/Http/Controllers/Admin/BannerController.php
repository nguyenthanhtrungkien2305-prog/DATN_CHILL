<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $position = $request->input('position');
        $query = Banner::with('product')->orderBy('banner_id', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('badge', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($position) {
            $query->where('position', $position);
        }

        $banners = $query->paginate(10);
        if ($search || $position) {
            $banners->appends(['search' => $search, 'position' => $position]);
        }

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $products = Product::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.banners.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'                 => 'required|string|max:255',
            'badge'                 => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'button_text'           => 'nullable|string|max:255',
            'button_link'           => 'nullable|string|max:255',
            'button_secondary_text' => 'nullable|string|max:255',
            'button_secondary_link' => 'nullable|string|max:255',
            'bg_gradient'           => 'nullable|string|max:255',
            'position'              => 'nullable|string|max:255',
            'product_id'            => 'nullable|integer|exists:products,product_id',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề chính cho Banner!',
            'image.image'    => 'File tải lên phải là định dạng hình ảnh!',
            'image.max'      => 'Kích thước ảnh tối đa là 2MB!',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $fileName);
            $imageUrl = 'uploads/banners/' . $fileName;
        }

        Banner::create([
            'title'                 => $request->title,
            'badge'                 => $request->badge,
            'description'           => $request->description,
            'button_text'           => $request->button_text,
            'button_link'           => $request->button_link,
            'button_secondary_text' => $request->button_secondary_text,
            'button_secondary_link' => $request->button_secondary_link,
            'image_url'             => $imageUrl,
            'bg_gradient'           => $request->bg_gradient ?: 'from-espresso via-coral to-amber-600',
            'position'              => $request->position ?: 'home_hero',
            'product_id'            => $request->product_id ?: null,
            'status'                => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('banners.index')->with('success', 'Đã thêm mới Banner thành công!');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        $products = Product::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.banners.edit', compact('banner', 'products'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title'                 => 'required|string|max:255',
            'badge'                 => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'button_text'           => 'nullable|string|max:255',
            'button_link'           => 'nullable|string|max:255',
            'button_secondary_text' => 'nullable|string|max:255',
            'button_secondary_link' => 'nullable|string|max:255',
            'bg_gradient'           => 'nullable|string|max:255',
            'position'              => 'nullable|string|max:255',
            'product_id'            => 'nullable|integer|exists:products,product_id',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề chính cho Banner!',
            'image.image'    => 'File tải lên phải là định dạng hình ảnh!',
            'image.max'      => 'Kích thước ảnh tối đa là 2MB!',
        ]);

        $imageUrl = $banner->image_url;
        if ($request->hasFile('image')) {
            if ($imageUrl && File::exists(public_path($imageUrl))) {
                File::delete(public_path($imageUrl));
            }

            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $fileName);
            $imageUrl = 'uploads/banners/' . $fileName;
        }

        $banner->update([
            'title'                 => $request->title,
            'badge'                 => $request->badge,
            'description'           => $request->description,
            'button_text'           => $request->button_text,
            'button_link'           => $request->button_link,
            'button_secondary_text' => $request->button_secondary_text,
            'button_secondary_link' => $request->button_secondary_link,
            'image_url'             => $imageUrl,
            'bg_gradient'           => $request->bg_gradient ?: 'from-espresso via-coral to-amber-600',
            'position'              => $request->position ?: 'home_hero',
            'product_id'            => $request->product_id ?: null,
            'status'                => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('banners.index')->with('success', 'Cập nhật Banner thành công!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image_url && File::exists(public_path($banner->image_url))) {
            File::delete(public_path($banner->image_url));
        }
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Đã xoá Banner thành công!');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = !$banner->status;
        $banner->save();

        $statusText = $banner->status ? 'Hiển thị' : 'Ẩn';
        return redirect()->back()->with('success', "Đã đổi trạng thái Banner sang: {$statusText}");
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một Banner!');
        }

        $banners = Banner::whereIn('banner_id', $ids)->get();
        foreach ($banners as $banner) {
            if ($banner->image_url && File::exists(public_path($banner->image_url))) {
                File::delete(public_path($banner->image_url));
            }
            $banner->delete();
        }

        return back()->with('success', 'Đã xóa ' . count($ids) . ' Banner đã chọn thành công!');
    }
}
