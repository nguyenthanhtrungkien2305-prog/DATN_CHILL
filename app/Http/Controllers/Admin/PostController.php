<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class PostController extends Controller
{
    /**
     * TỰ ĐỘNG KHỞI TẠO BẢNG & DỮ LIỆU MẶC ĐỊNH NẾU CHƯA CÓ
     */
    private function ensureTablesAndDataExist()
    {
        try {
            if (Schema::hasTable('categories_post')) {
                $countCat = DB::table('categories_post')->count();
                if ($countCat === 0) {
                    DB::table('categories_post')->insert([
                        ['name' => 'Coffeeholic', 'slug' => 'coffeeholic', 'created_at' => now(), 'updated_at' => now()],
                        ['name' => 'Teaholic', 'slug' => 'teaholic', 'created_at' => now(), 'updated_at' => now()],
                        ['name' => 'Blog', 'slug' => 'blog', 'created_at' => now(), 'updated_at' => now()],
                    ]);
                }
            }

            if (Schema::hasTable('posts')) {
                $countPosts = DB::table('posts')->count();
                if ($countPosts === 0) {
                    $catId = DB::table('categories_post')->value('categories_post_id') ?? 1;
                    $adminId = auth()->id() ?? auth()->user()->user_id ?? 1;

                    DB::table('posts')->insert([
                        [
                            'title' => 'BẮT GẶP SÀI GÒN XƯA TRONG MÓN UỐNG HIỆN ĐẠI CỦA GIỚI TRẺ',
                            'slug' => 'bat-gap-sai-gon-xua-trong-mon-uong-hien-dai-cua-gioi-tre',
                            'content' => 'Dẫu qua bao nhiêu lớp sóng thời gian, người ta vẫn có thể tìm lại những dấu ấn thăng trầm của một Sài Gòn xưa cũ. Trên những góc phố, trong các bức ảnh, trong vô số tác phẩm văn chương... và dĩ nhiên trong cả những hương vị cà phê thân thuộc tại Chill Chill. Với sự sáng tạo không ngừng, chúng mình mang tới những ly cà phê nguyên bản Robusta kết hợp vị béo ngậy tinh tế.',
                            'thumbnail' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=800&auto=format&fit=crop',
                            'status' => true,
                            'categories_post_id' => $catId,
                            'auth_id' => $adminId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ],
                        [
                            'title' => 'UỐNG GÌ KHI TỚI SIGNATURE BY CHILL CHILL?',
                            'slug' => 'uong-gi-khi-toi-signature-by-chill-chill',
                            'content' => 'Vừa qua, Chill Chill chính thức khai trương cửa hàng SIGNATURE chuyên phục vụ cà phê đặc sản. Cùng khám phá ngay menu độc đáo đang gây bão giới trẻ Sài Thành nhé. Chúng mình chuẩn bị những hạt cà phê rang xay thơm nức cùng không gian ấm cúng sang trọng.',
                            'thumbnail' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=600&auto=format&fit=crop',
                            'status' => true,
                            'categories_post_id' => $catId,
                            'auth_id' => $adminId,
                            'created_at' => now()->subDays(2),
                            'updated_at' => now()->subDays(2)
                        ],
                        [
                            'title' => 'CÀ PHÊ SỮA ESPRESSO CHILL CHILL - RẤT LỚN RẤT VỊ NGON',
                            'slug' => 'ca-phe-sua-espresso-chill-chill-rat-lon-rat-vi-ngon',
                            'content' => 'Cà phê sữa Espresso là một lon cà phê sữa giải khát với hương vị cà phê đậm đà từ 100% cà phê Robusta cùng vị sữa béo ngậy tuyệt hảo. Sự kết hợp hoàn hảo giữa hương vị đậm đà và thiết kế tiện lợi.',
                            'thumbnail' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?q=80&w=600&auto=format&fit=crop',
                            'status' => true,
                            'categories_post_id' => $catId,
                            'auth_id' => $adminId,
                            'created_at' => now()->subDays(5),
                            'updated_at' => now()->subDays(5)
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore error if schema loading
        }
    }

    // 1. Danh sách bài viết trong Admin
    public function index(Request $request)
    {
        $this->ensureTablesAndDataExist();

        $search = $request->search;
        $categoryId = $request->category_id;

        $query = DB::table('posts')
            ->leftJoin('categories_post', 'posts.categories_post_id', '=', 'categories_post.categories_post_id')
            ->leftJoin('users', 'posts.auth_id', '=', 'users.user_id')
            ->select('posts.*', 'categories_post.name as category_name', 'users.name as author_name')
            ->orderBy('posts.post_id', 'desc');

        if ($search) {
            $query->where('posts.title', 'like', '%' . $search . '%');
        }

        if ($categoryId) {
            $query->where('posts.categories_post_id', $categoryId);
        }

        $posts = $query->paginate(10);
        if ($search || $categoryId) {
            $posts->appends(['search' => $search, 'category_id' => $categoryId]);
        }

        $categories = DB::table('categories_post')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    // 2. Form tạo bài viết mới
    public function create()
    {
        $this->ensureTablesAndDataExist();
        $categories = DB::table('categories_post')->get();
        return view('admin.posts.create', compact('categories'));
    }

    // 3. Lưu bài viết mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories_post_id' => 'required|integer',
            'thumbnail' => 'nullable|string',
            'status' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết',
            'content.required' => 'Vui lòng nhập nội dung bài viết',
            'categories_post_id.required' => 'Vui lòng chọn danh mục bài viết',
        ]);

        $slug = Str::slug($request->title);
        // Đảm bảo slug là duy nhất
        $originalSlug = $slug;
        $count = 1;
        while (DB::table('posts')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $adminId = auth()->id() ?? auth()->user()->user_id ?? 1;

        DB::table('posts')->insert([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=800&auto=format&fit=crop',
            'status' => $request->has('status') ? (bool)$request->status : true,
            'categories_post_id' => $request->categories_post_id,
            'auth_id' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('posts.index')->with('success', 'Thêm bài viết mới thành công!');
    }

    // 4. Form chỉnh sửa bài viết
    public function edit($id)
    {
        $post = DB::table('posts')->where('post_id', $id)->first();
        if (!$post) {
            return redirect()->route('posts.index')->with('error', 'Không tìm thấy bài viết!');
        }

        $categories = DB::table('categories_post')->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    // 5. Cập nhật bài viết
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories_post_id' => 'required|integer',
            'thumbnail' => 'nullable|string',
            'status' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết',
            'content.required' => 'Vui lòng nhập nội dung bài viết',
            'categories_post_id.required' => 'Vui lòng chọn danh mục bài viết',
        ]);

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (DB::table('posts')->where('slug', $slug)->where('post_id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        DB::table('posts')->where('post_id', $id)->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=800&auto=format&fit=crop',
            'status' => $request->has('status') ? (bool)$request->status : false,
            'categories_post_id' => $request->categories_post_id,
            'updated_at' => now(),
        ]);

        return redirect()->route('posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    // 6. Xóa bài viết
    public function destroy($id)
    {
        DB::table('posts')->where('post_id', $id)->delete();
        return redirect()->route('posts.index')->with('success', 'Xóa bài viết thành công!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một bài viết!');
        }

        DB::table('posts')->whereIn('post_id', $ids)->delete();

        return back()->with('success', 'Đã xóa ' . count($ids) . ' bài viết đã chọn thành công!');
    }
}
