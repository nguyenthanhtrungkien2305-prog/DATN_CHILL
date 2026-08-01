<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // Ignore exception
        }
    }

    // 1. Hiển thị danh sách Tin tức / Blog công khai
    public function index(Request $request)
    {
        $this->ensureTablesAndDataExist();

        $categories = DB::table('categories_post')->get();
        $selectedCatSlug = $request->get('category');

        $query = DB::table('posts')
            ->leftJoin('categories_post', 'posts.categories_post_id', '=', 'categories_post.categories_post_id')
            ->leftJoin('users', 'posts.auth_id', '=', 'users.user_id')
            ->select('posts.*', 'categories_post.name as category_name', 'categories_post.slug as category_slug', 'users.name as author_name')
            ->where('posts.status', 1)
            ->orderBy('posts.post_id', 'desc');

        if ($selectedCatSlug) {
            $query->where('categories_post.slug', $selectedCatSlug);
        }

        $posts = $query->paginate(9);
        if ($selectedCatSlug) {
            $posts->appends(['category' => $selectedCatSlug]);
        }

        return view('post.index', compact('posts', 'categories', 'selectedCatSlug'));
    }

    // 2. Trang Chi Tiết Bài Viết
    public function show($slug)
    {
        $this->ensureTablesAndDataExist();

        $post = DB::table('posts')
            ->leftJoin('categories_post', 'posts.categories_post_id', '=', 'categories_post.categories_post_id')
            ->leftJoin('users', 'posts.auth_id', '=', 'users.user_id')
            ->select('posts.*', 'categories_post.name as category_name', 'categories_post.slug as category_slug', 'users.name as author_name')
            ->where('posts.slug', $slug)
            ->where('posts.status', 1)
            ->first();

        if (!$post) {
            return redirect()->route('post.index')->with('error', 'Không tìm thấy bài viết!');
        }

        // Bài viết liên quan
        $relatedPosts = DB::table('posts')
            ->leftJoin('categories_post', 'posts.categories_post_id', '=', 'categories_post.categories_post_id')
            ->select('posts.*', 'categories_post.name as category_name')
            ->where('posts.status', 1)
            ->where('posts.post_id', '!=', $post->post_id)
            ->orderBy('posts.post_id', 'desc')
            ->limit(3)
            ->get();

        return view('post.show', compact('post', 'relatedPosts'));
    }
}
