# HƯỚNG DẪN TÍCH HỢP SEPAY THẬT (TỰ ĐỘNG XÁC NHẬN THANH TOÁN)

Tài liệu này hướng dẫn bạn cách chuyển từ **Giả lập thanh toán** sang **Tích hợp thanh toán thật bằng SePay.vn** (liên kết tài khoản MB Bank của bạn). Khi bạn sẵn sàng chạy thật, hãy copy nội dung file này hoặc yêu cầu AI làm theo các bước dưới đây.

---

## BƯỚC 1: Đăng ký SePay & Liên kết ngân hàng
1. Truy cập [https://sepay.vn](https://sepay.vn) và đăng ký tài khoản (miễn phí).
2. Kết nối tài khoản ngân hàng của bạn (MB Bank - `0385792442`) trên bảng điều khiển SePay.
3. Tạo một **Cấu hình Webhook** trên SePay:
   - **URL tích hợp**: `https://<tên-miền-hoặc-link-ngrok-của-bạn>/api/sepay-webhook`
   - **Phương thức**: `POST`
   - **Kiểu dữ liệu**: `JSON`
   - **API Key (Token)**: Nhập một chuỗi bảo mật bất kỳ (Ví dụ: `sepay_token_sec_123456`) để xác minh yêu cầu.

---

## BƯỚC 2: Khai báo Route Webhook trong Laravel
Mở file [routes/api.php](file:///H:/DATN_CHILL/routes/api.php) và thêm dòng sau vào cuối file (bên ngoài group middleware auth:sanctum):

```php
use App\Http\Controllers\PaymentWebhookController;

Route::post('/sepay-webhook', [PaymentWebhookController::class, 'handleSePay']);
```

---

## BƯỚC 3: Tạo Controller xử lý Webhook
Tạo file controller mới tại [app/Http/Controllers/PaymentWebhookController.php](file:///H:/DATN_CHILL/app/Http/Controllers/PaymentWebhookController.php) với nội dung sau:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handleSePay(Request $request)
    {
        // 1. Xác minh API Key gửi kèm trong header Authorization
        $authHeader = $request->header('Authorization');
        $expectedToken = 'Apikey YOUR_SEPAY_API_KEY_HERE'; // Thay thế bằng API Key cấu hình ở Bước 1
        
        if ($authHeader !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // 2. Lấy dữ liệu giao dịch từ SePay gửi về
        $transaction = $request->all();
        $code = $transaction['code']; // Nội dung chuyển khoản (Ví dụ: "CHILLCHILL 12")
        $amount = (float)$transaction['transferAmount']; // Số tiền chuyển khoản thực tế
        $transferType = $transaction['transferType']; // Loại giao dịch ("in" là tiền vào, "out" là tiền ra)

        // Chỉ xử lý nếu là giao dịch tiền vào (khách chuyển tiền đến)
        if (strtolower($transferType) !== 'in') {
            return response()->json(['success' => false, 'message' => 'Not a deposit transaction'], 200);
        }

        // 3. Tách mã đơn hàng từ nội dung chuyển khoản
        // Sử dụng Regex để tìm chuỗi dạng CHILLCHILL <order_id>
        if (preg_match('/CHILLCHILL\s+(\d+)/i', $code, $matches)) {
            $orderId = $matches[1];

            // Tìm đơn hàng trong DB
            $order = DB::table('orders')->where('order_id', $orderId)->first();

            if ($order) {
                // Kiểm tra trạng thái đơn hàng (chỉ xử lý nếu đang chờ xác nhận) và số tiền khớp
                if ($order->status === 'pending') {
                    if ($amount >= (float)$order->total_amount) {
                        
                        // Cập nhật trạng thái đơn hàng sang "đang chuẩn bị" (processing)
                        DB::table('orders')
                            ->where('order_id', $orderId)
                            ->update([
                                'status' => 'processing',
                                'updated_at' => now()
                            ]);

                        Log::info("Thanh toán thành công đơn hàng #{$orderId} qua SePay. Số tiền: {$amount}");
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Order paid successfully'
                        ], 200);
                    } else {
                        Log::warning("Đơn hàng #{$orderId} thanh toán thiếu tiền. Cần: {$order->total_amount}, thực nhận: {$amount}");
                        return response()->json(['success' => false, 'message' => 'Amount mismatch'], 200);
                    }
                }
                
                return response()->json(['success' => true, 'message' => 'Order was already processed'], 200);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid transaction code'], 200);
    }
}
```

---

## BƯỚC 4: Gỡ bỏ giả lập trên giao diện
Khi đã chạy SePay thật, bạn chỉ cần gỡ bỏ block **GIẢ LẬP THANH TOÁN SAU 10 GIÂY** (hàm `setTimeout`) trong file [payment_qr.blade.php](file:///H:/DATN_CHILL/resources/views/checkout/payment_qr.blade.php).

Giữ lại block **POLLING STATUS CHECK** (`setInterval`), vì block này sẽ tự động lắng nghe trạng thái đơn hàng thay đổi để chuyển hướng trang khi Webhook SePay cập nhật thành công.
