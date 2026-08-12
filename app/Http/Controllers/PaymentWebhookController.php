<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handleSePay(Request $request)
    {
        $expectedToken = env('SEPAY_API_KEY');
        
        // Nếu có cấu hình SEPAY_API_KEY trong .env thì tiến hành xác thực Authorization Header
        if (!empty($expectedToken)) {
            $authHeader = $request->header('Authorization');
            
            // Hỗ trợ cả 2 dạng: "Apikey <token>" hoặc "Bearer <token>" hoặc token trực tiếp
            $isValid = ($authHeader === 'Apikey ' . $expectedToken) ||
                       ($authHeader === 'Bearer ' . $expectedToken) ||
                       ($authHeader === $expectedToken) ||
                       ($request->header('X-Sepay-Api-Key') === $expectedToken);

            if (!$isValid) {
                Log::warning('SePay Webhook: Xác thực API Key thất bại.', [
                    'received_header' => $authHeader
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid API Key'
                ], 401);
            }
        }

        // Lấy dữ liệu giao dịch từ SePay gửi về
        $transaction = $request->all();
        Log::info('SePay Webhook Payload Received:', $transaction);

        // Các trường dữ liệu chuẩn từ SePay Webhook:
        // - code / content: Nội dung chuyển khoản
        // - transferAmount: Số tiền chuyển khoản
        // - transferType: "in" (tiền vào) hoặc "out" (tiền ra)
        $code = $transaction['code'] ?? $transaction['content'] ?? '';
        $amount = (float)($transaction['transferAmount'] ?? $transaction['amount'] ?? 0);
        $transferType = strtolower($transaction['transferType'] ?? 'in');

        // Chỉ xử lý nếu là giao dịch tiền vào (khách nộp/chuyển tiền đến)
        if ($transferType !== 'in') {
            return response()->json([
                'success' => false,
                'message' => 'Ignored: Not a deposit transaction'
            ], 200);
        }

        // Tách mã đơn hàng từ nội dung chuyển khoản sử dụng Regex (Ví dụ: "CHILLCHILL 12" hoặc "CHILLCHILL12")
        if (preg_match('/CHILLCHILL\s*(\d+)/i', $code, $matches)) {
            $orderId = $matches[1];

            // Tìm đơn hàng trong DB
            $order = DB::table('orders')->where('order_id', $orderId)->first();

            if (!$order) {
                Log::warning("SePay Webhook: Không tìm thấy đơn hàng #{$orderId}");
                return response()->json([
                    'success' => false,
                    'message' => "Order #{$orderId} not found"
                ], 404);
            }

            // Kiểm tra trạng thái đơn hàng (chỉ xử lý nếu đang chờ xác nhận)
            if ($order->status === 'pending') {
                if ($amount >= (float)$order->total_amount) {
                    
                    // Cập nhật trạng thái đơn hàng sang "đang chuẩn bị" (processing)
                    DB::table('orders')
                        ->where('order_id', $orderId)
                        ->update([
                            'status' => 'processing',
                            'updated_at' => now()
                        ]);

                    Log::info("✅ Thanh toán thành công đơn hàng #{$orderId} qua SePay. Số tiền nhận: {$amount}đ");
                    
                    return response()->json([
                        'success' => true,
                        'message' => "Order #{$orderId} paid successfully"
                    ], 200);
                } else {
                    Log::warning("⚠️ Đơn hàng #{$orderId} thanh toán thiếu tiền. Cần: {$order->total_amount}đ, thực nhận: {$amount}đ");
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount mismatch: Paid less than total amount'
                    ], 400);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order was already processed'
            ], 200);
        }

        Log::warning('SePay Webhook: Không tìm thấy mã đơn hàng CHILLCHILL trong nội dung chuyển khoản:', ['code' => $code]);
        return response()->json([
            'success' => false,
            'message' => 'Invalid transaction code format'
        ], 400);
    }
}
