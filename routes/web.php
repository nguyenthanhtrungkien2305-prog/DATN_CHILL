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
use App\Http\Controllers\Staff\OrderController;

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
Route::post('/lien-he', [FeedbackController::class, 'store'])->name('contact.submit');
// ROUTES NGƯỜI DÙNG (Bắt buộc phải đăng nhập)
Route::middleware(['auth'])->group(function () {
    Route::get('/tai-khoan', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/tai-khoan/cap-nhat', [UserController::class, 'updateProfile'])->name('user.update_profile');
    Route::get('/tai-khoan/don-hang', [UserController::class, 'orders'])->name('user.orders');
    // QUẢN LÝ ĐƠN HÀNG
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::post('orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update_status');
});
Route::get('/user/orders/{order_id}', [UserController::class, 'show'])->name('user.orders.show');

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
    
    // ==========================================
    // VÙNG 1: KHÔNG CẦN VÀO CA VẪN TRUY CẬP ĐƯỢC
    // ==========================================
    Route::get('/staff/shifts', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('staff.shifts');
    Route::post('/staff/shifts/register', [\App\Http\Controllers\AttendanceController::class, 'storeRegistration'])->name('staff.shifts.register');
    Route::get('/staff/salary', [\App\Http\Controllers\SalaryController::class, 'index'])->name('staff.salary');   
    Route::post('/staff/api/orders', [OrderController::class, 'storeApi']);
    // 👉 ĐÃ CỨU: API Check-in và Check-out được đưa ra vùng an toàn để ai cũng bấm được!
    Route::post('/staff/attendance/checkin', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('staff.checkin');
    Route::post('/staff/attendance/checkout', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('staff.checkout');

    // ==========================================
    // VÙNG 2: NHẠY CẢM - BẮT BUỘC PHẢI ĐANG TRONG CA LÀM (MIDDLEWARE CHẶN)
    // ==========================================
    Route::middleware([\App\Http\Middleware\CheckActiveShift::class])->group(function () {
        Route::get('/staff/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('staff.pos');
        Route::get('/staff/new-orders', [\App\Http\Controllers\PosController::class, 'newOrders'])->name('staff.new_orders');
        Route::get('/staff/commission', [\App\Http\Controllers\CommissionController::class, 'index'])->name('staff.commission');
        
        // API xử lý đơn hàng cũng cần được bảo vệ để người ngoài không chọc vào được
        Route::post('/staff/api/orders/{id}/complete', [\App\Http\Controllers\PosController::class, 'completeOrder'])->name('staff.api.complete_order');
    });
    Route::prefix('staff')->group(function () {
    // Thêm chữ Route:: vào trước get
    Route::get('/orders', [OrderController::class, 'index'])->name('staff.orders.new');
    
    // API nhận tạo đơn từ trang POS
    Route::post('/api/orders', [OrderController::class, 'storeApi']);
    
    // API bấm hoàn thành đơn hàng
    Route::post('/api/orders/{id}/complete', [OrderController::class, 'complete']);
});
});

Route::get('/tai-khoan/don-hang', [\App\Http\Controllers\UserController::class, 'orders'])->name('user.orders');
Route::post('/tai-khoan/don-hang/{id}/huy', [UserController::class, 'cancelOrder'])->name('user.orders.cancel');
Route::post('/tai-khoan/don-hang/danh-gia', [\App\Http\Controllers\UserController::class, 'submitReview'])->name('user.orders.review');
// ROUTES CHAT BOX TRỰC TUYẾN
Route::prefix('chat')->group(function () {
    Route::post('/start', [ChatController::class, 'startSession'])->name('chat.start');
    Route::get('/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/add-to-cart', [ChatController::class, 'addToCartAction'])->name('chat.add_to_cart');
    Route::post('/add-combo', [ChatController::class, 'addComboAction'])->name('chat.add_combo');
});
/*
|--------------------------------------------------------------------------
| ROUTES QUẢN TRỊ (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('products', AdminProductController::class);
    // Route::resource('toppings', AdminToppingController::class);
    Route::resource('vouchers', AdminVoucherController::class);
    Route::resource('users', AdminUserController::class);
    Route::post('users/{id}/toggle-lock', [AdminUserController::class, 'toggleLock'])->name('users.toggle_lock');
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::post('users/{id}/update-role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('admin.users.update_role');
    
    // QUẢN LÝ ĐƠN HÀNG (ORDERS):
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    Route::put('orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update_status');
    
    // QUẢN LÝ LỊCH LÀM & LƯƠNG NHÂN VIÊN
    Route::get('staff-manager', [\App\Http\Controllers\Admin\StaffManagerController::class, 'index'])->name('admin.staff.manager');
    Route::post('staff-manager/shift/{id}', [\App\Http\Controllers\Admin\StaffManagerController::class, 'updateShiftStatus'])->name('admin.staff.update_shift');
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