<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

// 1. Khai báo Controller Khách hàng (Đặt bí danh là PublicProductController)
use App\Http\Controllers\ProductController as PublicProductController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;


// 2. Khai báo Controller Admin (Đặt bí danh là AdminProductController)
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController; 
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\ToppingController as AdminToppingController;

/*
|--------------------------------------------------------------------------
| ROUTES KHÁCH HÀNG
|--------------------------------------------------------------------------
*/
// Trang chủ
Route::get('/', [HomeController::class, 'index']);

// Chi tiết sản phẩm (Sử dụng bí danh Public)
Route::get('/san-pham/{slug}', [PublicProductController::class, 'show'])->name('product.show');

// Đăng nhập / Đăng ký
Route::get('/dang-nhap', function () {
    return view('auth.login');
})->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.post');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');
Route::get('/dang-xuat', [AuthController::class, 'logout'])->name('logout');
// Trang danh mục sản phẩm (Thực đơn)
Route::get('/thuc-don', [PublicProductController::class, 'index'])->name('product.index');
// Trang câu chuyện thương hiệu (Bài viết nổi bật)
Route::get('/chuyen-nha', function () {
    return view('post.story'); // Trỏ tới file view story.blade.php
})->name('post.story');
// Trang Tin tức / Blog
Route::get('/tin-tuc', function () {
    return view('post.index'); 
})->name('post.index');
// Trang Liên hệ
Route::get('/lien-he', function () {
    return view('contact'); 
})->name('contact');
// ROUTES NGƯỜI DÙNG (Bắt buộc phải đăng nhập)
Route::middleware(['auth'])->group(function () {
    Route::get('/tai-khoan', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/tai-khoan/cap-nhat', [UserController::class, 'updateProfile'])->name('user.update_profile');
});
// CÁC ROUTE GIỎ HÀNG
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove', [CartController::class, 'remove'])->name('cart.remove');
    
    // THÊM 2 DÒNG MỚI NÀY:
    Route::post('/get-item', [CartController::class, 'getItem'])->name('cart.getItem');
    Route::post('/update-toppings', [CartController::class, 'updateToppings'])->name('cart.updateToppings');
});
// ROUTE THANH TOÁN (Yêu cầu đăng nhập - Tuỳ bạn cấu hình middleware auth)
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/add-address', [CheckoutController::class, 'addAddress'])->name('checkout.addAddress');
    Route::post('/delete-address', [CheckoutController::class, 'deleteAddress'])->name('checkout.deleteAddress'); // THÊM DÒNG NÀY
    Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process');
});
Route::get('/tai-khoan/don-hang', [\App\Http\Controllers\UserController::class, 'orders'])->name('user.orders');
/*
|--------------------------------------------------------------------------
| ROUTES QUẢN TRỊ (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    
    // Trang Dashboard Admin
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Quản lý Sản phẩm (Sử dụng bí danh Admin)
    Route::resource('products', AdminProductController::class);
    
});
// Danh mục
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('products', AdminProductController::class);
    
    // Thêm dòng này để tạo 7 route Thêm/Sửa/Xóa cho Danh mục
    Route::resource('categories', AdminCategoryController::class);
});
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    // Các route cũ của bạn...
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('products', AdminProductController::class);
    
    // THÊM DÒNG NÀY CHO TOPPING:
    Route::resource('toppings', AdminToppingController::class);
});