<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handleSePay(Request $request)
    {
        $expectedToken = env('SEPAY_API_KEY') ?: config('services.sepay.secret_key');
        
        // 1. Xác thực bảo mật API Key / Authorization Header từ SePay
        if (!empty($expectedToken)) {
            $authHeader   = $request->header('Authorization', '');
            $apiKeyHeader = $request->header('X-Sepay-Api-Key', '');

            $isValid = str_contains($authHeader, $expectedToken) ||
                       str_contains($apiKeyHeader, $expectedToken) ||
                       $authHeader === $expectedToken ||
                       $apiKeyHeader === $expectedToken;

            if (!$isValid) {
                Log::warning('SePay Webhook: Xác thực API Key không khớp.', [
                    'received_authorization' => $authHeader,
                    'received_x_sepay_key'  => $apiKeyHeader
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid API Key'
                ], 401);
            }
        }

        // 2. Lấy dữ liệu payload từ SePay Webhook
        $transaction = $request->all();
        Log::info('SePay Webhook Payload Received:', $transaction);

        $rawPayloadText = json_encode($transaction, JSON_UNESCAPED_UNICODE);
        $amount = (float)($transaction['transferAmount'] ?? $transaction['amount'] ?? 0);
        $transferType = strtolower($transaction['transferType'] ?? 'in');

        // Bỏ qua nếu không phải giao dịch nộp/chuyển tiền vào
        if ($transferType !== 'in' && isset($transaction['transferType'])) {
            return response()->json([
                'success' => true,
                'message' => 'Ignored: Non-deposit transaction'
            ], 200);
        }

        // 3. Giải mã lấy Mã đơn hàng (Order ID) từ nhiều nguồn dữ liệu SePay
        $orderId = null;

        // Ưu tiên 1: Lấy từ orderInvoiceNumber (SePay PG) hoặc code/content/description
        $searchFields = [
            $transaction['orderInvoiceNumber'] ?? null,
            $transaction['code'] ?? null,
            $transaction['content'] ?? null,
            $transaction['description'] ?? null,
            $transaction['referenceCode'] ?? null,
        ];

        foreach ($searchFields as $field) {
            if (!empty($field) && preg_match('/CHILLCHILL[\s_\-]*(\d+)/i', $field, $matches)) {
                $orderId = (int)$matches[1];
                break;
            }
        }

        // Ưu tiên 2: Quét toàn bộ JSON payload tìm định dạng CHILLCHILL <ID>
        if (!$orderId && preg_match('/CHILLCHILL[\s_\-]*(\d+)/i', $rawPayloadText, $matches)) {
            $orderId = (int)$matches[1];
        }

        // Ưu tiên 3: Nếu SePay chỉ gửi số ID đơn hàng dạng số thuần túy
        if (!$orderId && !empty($transaction['orderInvoiceNumber']) && is_numeric($transaction['orderInvoiceNumber'])) {
            $orderId = (int)$transaction['orderInvoiceNumber'];
        }

        if (!$orderId) {
            Log::warning('SePay Webhook: Không tìm thấy mã đơn CHILLCHILL trong payload:', ['payload' => $rawPayloadText]);
            return response()->json([
                'success' => false,
                'message' => 'Order ID not found in payload'
            ], 400);
        }

        // 4. Kiểm tra đơn hàng trong cơ sở dữ liệu
        $order = DB::table('orders')->where('order_id', $orderId)->first();

        if (!$order) {
            Log::warning("SePay Webhook: Không tìm thấy đơn hàng #{$orderId} trong cơ sở dữ liệu.");
            return response()->json([
                'success' => false,
                'message' => "Order #{$orderId} not found"
            ], 404);
        }

        // 5. Kiểm tra Idempotency (Tránh xử lý lặp lại nếu đơn đã duyệt)
        if (in_array($order->status, ['processing', 'completed', 'paid'])) {
            Log::info("SePay Webhook: Đơn hàng #{$orderId} đã được duyệt trước đó rồi.");
            return response()->json([
                'success' => true,
                'message' => "Order #{$orderId} already processed"
            ], 200);
        }

        // 6. Kiểm tra số tiền chuyển khoản có đủ không
        $requiredAmount = (float)$order->total_amount;

        if ($amount >= ($requiredAmount - 1)) { // Cho phép chênh lệch nhỏ do làm tròn
            // Cập nhật trạng thái đơn hàng sang "đang xử lý / chuẩn bị món"
            DB::table('orders')
                ->where('order_id', $orderId)
                ->update([
                    'status' => 'processing',
                    'updated_at' => now()
                ]);

            Log::info("🎉 XÁC NHẬN THANH TOÁN TỰ ĐỘNG THÀNH CÔNG đơn hàng #{$orderId}. Nhận: {$amount}đ / Cần: {$requiredAmount}đ");

            return response()->json([
                'success' => true,
                'message' => "Order #{$orderId} paid successfully"
            ], 200);
        } else {
            Log::warning("⚠️ Đơn hàng #{$orderId} thanh toán thiếu tiền: Nhận {$amount}đ, Cần {$requiredAmount}đ");
            return response()->json([
                'success' => false,
                'message' => "Amount mismatch: Received {$amount}đ, expected {$requiredAmount}đ"
            ], 400);
        }
    }
}
