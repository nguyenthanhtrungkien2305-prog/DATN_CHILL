<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

// Khai báo Controller Khách hàng
use App\Http\Controllers\ProductController as PublicProductController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Khai báo Controller Nhân Viên (Staff)
use App\Http\Controllers\PosController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\AttendanceController;

// Khai báo Controller Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController; 
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\ToppingController as AdminToppingController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\UserController as AdminUserController;


/*
|--------------------------------------------------------------------------
| ROUTES CHUNG (Ai cũng có thể truy cập)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham/{slug}', [PublicProductController::class, 'show'])->name('product.show');
Route::get('/thuc-don', [PublicProductController::class, 'index'])->name('product.index');
Route::get('/chuyen-nha', function () { return view('post.story'); })->name('post.story');
Route::get('/tin-tuc', function () { return view('post.index'); })->name('post.index');
Route::get('/lien-he', function () { return view('contact'); })->name('contact');

/*
|--------------------------------------------------------------------------
| ROUTES DÀNH CHO KHÁCH (CHƯA ĐĂNG NHẬP)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/dang-nhap', function () { return view('auth.login'); })->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.post');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');
});

// Route đăng xuất (Ai đăng nhập rồi cũng dùng được)
Route::get('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ROUTES NGƯỜI DÙNG THÀNH VIÊN (ĐÃ ĐĂNG NHẬP)
|--------------------------------------------------------------------------
*/
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
Route::post('/lien-he', [FeedbackController::class, 'store'])->name('contact.submit');
// ROUTES NGƯỜI DÙNG (Bắt buộc phải đăng nhập)
Route::middleware(['auth'])->group(function () {
    Route::get('/tai-khoan', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/tai-khoan/cap-nhat', [UserController::class, 'updateProfile'])->name('user.update_profile');
    Route::get('/tai-khoan/don-hang', [UserController::class, 'orders'])->name('user.orders');
});

/*
|--------------------------------------------------------------------------
| ROUTES GIỎ HÀNG & THANH TOÁN
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove', [CartController::class, 'remove'])->name('cart.remove');
    
    Route::post('/get-item', [CartController::class, 'getItem'])->name('cart.getItem');
    Route::post('/update-toppings', [CartController::class, 'updateToppings'])->name('cart.updateToppings');
    Route::post('/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.applyVoucher');
    Route::post('/remove-voucher', [CartController::class, 'removeVoucher'])->name('cart.removeVoucher');
    Route::get('/count', [CartController::class, 'getCount'])->name('cart.count');
});

Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/add-address', [CheckoutController::class, 'addAddress'])->name('checkout.addAddress');
    Route::post('/delete-address', [CheckoutController::class, 'deleteAddress'])->name('checkout.deleteAddress');
    Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/payment-qr/{id}', [CheckoutController::class, 'paymentQr'])->name('checkout.payment_qr');
    Route::get('/check-status/{id}', [CheckoutController::class, 'checkStatus'])->name('checkout.check_status');
    Route::post('/mock-pay/{id}', [CheckoutController::class, 'mockPay'])->name('checkout.mock_pay');
});

/*
|--------------------------------------------------------------------------
| ROUTES NHÂN VIÊN (STAFF / POS) - Đã được bảo vệ nghiêm ngặt
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'staff'])->group(function () {
    
    // Tự động chuyển hướng nếu gõ thiếu đường dẫn
    Route::redirect('/staff', '/staff/pos');

    // Các trang Giao diện
    Route::get('/staff/pos', [PosController::class, 'index'])->name('staff.pos');
    Route::get('/staff/new-orders', [PosController::class, 'newOrders'])->name('staff.new_orders');
    Route::get('/staff/commission', [CommissionController::class, 'index'])->name('staff.commission');
    Route::get('/staff/shifts', [AttendanceController::class, 'index'])->name('staff.shifts');
Route::post('/staff/shifts/register', [AttendanceController::class, 'storeRegistration'])->name('staff.shifts.register');
Route::post('/staff/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('staff.checkout');
Route::post('/staff/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('staff.checkin');
    
    // Các đường dẫn API xử lý ngầm
    Route::get('/staff/api/check-new-orders', [PosController::class, 'checkNewOrders'])->name('staff.api.check_orders');
    Route::post('/staff/api/orders', [PosController::class, 'storeOrder'])->name('staff.api.store_order');
    Route::post('/staff/api/orders/{id}/complete', [PosController::class, 'completeOrder'])->name('staff.api.complete_order');
});

Route::get('/tai-khoan/don-hang', [\App\Http\Controllers\UserController::class, 'orders'])->name('user.orders');

// ROUTES CHAT BOX TRỰC TUYẾN
Route::prefix('chat')->group(function () {
    Route::post('/start', [ChatController::class, 'startSession'])->name('chat.start');
    Route::get('/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});
/*
|--------------------------------------------------------------------------
| ROUTES QUẢN TRỊ (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('toppings', AdminToppingController::class);
    Route::resource('vouchers', AdminVoucherController::class);
    Route::resource('users', AdminUserController::class);
    Route::post('users/{id}/toggle-lock', [AdminUserController::class, 'toggleLock'])->name('users.toggle_lock');

    
    // QUẢN LÝ ĐƠN HÀNG (ORDERS):
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    Route::put('orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update_status');
    

    // QUẢN LÝ PHẢN HỒI (FEEDBACK):
    Route::resource('feedbacks', AdminFeedbackController::class)->only(['index', 'show', 'destroy']);
    Route::post('feedbacks/{id}/reply', [AdminFeedbackController::class, 'reply'])->name('feedbacks.reply');

    // QUẢN LÝ CHAT BOX (LIVE CHAT):
    Route::get('chats', [AdminChatController::class, 'index'])->name('admin.chats.index');
    Route::get('chats/sessions', [AdminChatController::class, 'getSessions'])->name('admin.chats.sessions');
    Route::get('chats/sessions/{id}/messages', [AdminChatController::class, 'getSessionMessages'])->name('admin.chats.messages');
    Route::post('chats/sessions/{id}/reply', [AdminChatController::class, 'sendReply'])->name('admin.chats.reply');
    Route::get('chats/sessions/{id}/bot-status', [AdminChatController::class, 'getBotStatus'])->name('admin.chats.bot_status');
    Route::post('chats/sessions/{id}/toggle-bot', [AdminChatController::class, 'toggleBot'])->name('admin.chats.toggle_bot');

    // TRỢ LÝ AI QUẢN LÝ (ADMIN AI ASSISTANT):
    Route::get('ai-assistant', [AiAssistantController::class, 'index'])->name('admin.ai.index');
    Route::post('ai-assistant/chat', [AiAssistantController::class, 'chat'])->name('admin.ai.chat');
    Route::post('ai-assistant/clear', [AiAssistantController::class, 'clearHistory'])->name('admin.ai.clear');
});