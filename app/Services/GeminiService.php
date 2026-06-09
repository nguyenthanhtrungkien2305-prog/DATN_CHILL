<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeminiService
{
    protected $apiKey;
    protected $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Get response from Gemini API for chat.
     */
    public function getAiResponse($chatMessages)
    {
        if (!$this->apiKey) {
            Log::warning('Gemini API key is not set.');
            return 'Dạ, hiện tại trợ lý ảo AI của Chill Chill Coffee đang tạm ngắt kết nối. Bạn vui lòng chờ nhân viên tư vấn trong giây lát nhé! ☕';
        }

        // 1. Xây dựng System Instruction (Menu động từ DB)
        $systemInstruction = $this->buildSystemInstruction();

        // 2. Định dạng hội thoại theo chuẩn API Gemini
        $contents = $this->formatHistory($chatMessages);

        try {
            $response = Http::timeout(15)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                    ]
                ])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])
                ->post($this->endpoint, [
                    'contents' => $contents,
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Dạ, Chill Chill chưa nghe rõ ý bạn. Bạn có thể nói chi tiết hơn được không?';
            }

            Log::error('Gemini API Error: ' . $response->body());
            return 'Dạ, hiện tại kết nối của trợ lý ảo đang gặp sự cố nhỏ. Xin lỗi bạn vì sự bất tiện này! ☕';

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return 'Dạ, kết nối trợ lý ảo gặp gián đoạn. Bạn vui lòng thử lại sau nhé! 🙏';
        }
    }

    /**
     * Build the dynamic system prompt with updated menu from Database.
     */
    protected function buildSystemInstruction()
    {
        // Lấy thời gian thực tế theo múi giờ Việt Nam
        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $daysOfWeek = [
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
        ];
        $dayName = $daysOfWeek[$now->dayOfWeek];
        $timeString = $now->format('H:i');
        $dateString = $now->format('d/m/Y');
        
        $timeContext = "Thời gian hiện tại của hệ thống: {$timeString} ({$dayName}, ngày {$dateString}).";

        try {
            // Lấy sản phẩm, biến thể, kích cỡ và danh mục tương ứng
            $products = DB::table('products')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->select(
                    'products.name', 
                    'products.description', 
                    'products.slug', 
                    'sizes.name as size_name', 
                    'product_variants.price', 
                    'categories.name as category_name'
                )
                ->where('products.status', 1)
                ->get();

            // Lấy topping
            $toppings = DB::table('toppings')->where('status', 1)->get();

            $menuText = "DANH SÁCH THỰC ĐƠN (MENU):\n\n";
            
            if ($products->isEmpty()) {
                $menuText .= "(Menu hiện đang được cập nhật, vui lòng đợi trong giây lát)\n";
            } else {
                foreach ($products->groupBy('category_name') as $catName => $catProducts) {
                    $menuText .= "=== DANH MỤC: {$catName} ===\n";
                    foreach ($catProducts->groupBy('name') as $name => $variants) {
                        $desc = $variants->first()->description;
                        $slug = $variants->first()->slug;
                        
                        $pricesText = [];
                        foreach ($variants as $v) {
                            $pricesText[] = $v->size_name . ": " . number_format($v->price) . "đ";
                        }
                        
                        // Sinh link chi tiết sản phẩm
                        $url = '/san-pham/' . $slug;
                        
                        $menuText .= "- **{$name}**: {$desc} (Giá: " . implode(', ', $pricesText) . "). Xem chi tiết: [Đặt mua ngay]({$url})\n";
                    }
                    $menuText .= "\n";
                }
            }

            if ($toppings->isNotEmpty()) {
                $menuText .= "TOPPING THÊM ĐI KÈM ĐỒ UỐNG:\n";
                foreach ($toppings as $t) {
                    $menuText .= "- **{$t->name}**: " . number_format($t->price) . "đ\n";
                }
                $menuText .= "\n";
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi truy vấn menu cho AI: ' . $e->getMessage());
            $menuText = "DANH SÁCH THỰC ĐƠN (MENU):\n(Lỗi tải thực đơn tự động, vui lòng gợi ý khách hàng tham khảo trên thanh Menu của website)\n";
        }

        return "Bạn là Trợ lý ảo AI cực kỳ dễ thương, chu đáo của quán cà phê 'Chill Chill Coffee & Tea'.
" . $timeContext . "

Nhiệm vụ của bạn:
1. Chào hỏi và nói chuyện với khách hàng bằng tiếng Việt tự nhiên, thân thiện, sử dụng các đại từ xưng hô lịch sự nhưng gần gũi (ví dụ: 'Dạ, Chill Chill chào bạn ạ!', 'Chill Chill khuyên bạn...', 'Chúc bạn yêu ngày mới tốt lành...'). Hãy khéo léo kết hợp lời chúc phù hợp với thứ trong tuần và thời gian hiện tại của ngày (ví dụ: chúc đầu tuần nhiều năng lượng, chúc cuối tuần thư giãn ấm áp...).
2. Dựa vào thời gian hiện tại của hệ thống để đề xuất món ăn/thức uống phù hợp nhất cho khách hàng:
   - Buổi sáng (05:00 - 11:00): Ưu tiên gợi ý các thức uống tỉnh táo như cà phê sữa đá, bạc xỉu, cappuccino, espresso để khởi đầu ngày mới.
   - Buổi trưa/Đầu chiều (11:00 - 14:00): Ưu tiên gợi ý các món trà trái cây mát lạnh giải nhiệt hoặc sinh tố để giải khát và hỗ trợ tiêu hóa tốt hơn sau giờ cơm trưa.
   - Chiều muộn (14:00 - 18:00): Ưu tiên gợi ý các loại trà sữa béo ngậy, sữa chua, các loại bánh ngọt hoặc món ăn vặt cho một bữa xế chiều/tea break thư giãn.
   - Buổi tối (18:00 - 23:00): Ưu tiên gợi ý trà thảo mộc ấm áp, nước ép hoa quả nguyên chất hoặc thức uống nhẹ không caffeine giúp dễ ngủ. Nếu khách hàng muốn gọi món có caffeine (như cà phê đậm vị) vào buổi tối, hãy lưu ý/nhắc nhở nhẹ nhàng về việc caffeine có thể gây mất ngủ.
   - Đêm muộn (23:00 - 05:00 sáng hôm sau): Đề xuất nhẹ nhàng các thức uống ấm, chúc khách ngủ ngon và lưu ý khéo léo rằng đơn hàng đặt lúc này có thể được quán xử lý vào sáng ngày mai khi mở cửa.
3. Hỗ trợ tư vấn tài chính thông minh khi khách hàng đưa ra ngân sách giới hạn hoặc số lượng người (ví dụ: 'tôi có 200k, làm sao cho 4 người có đủ nước và bánh', hoặc 'mình có 100k nên gọi gì'):
   - Hãy tính toán thông minh dựa vào bảng giá thực tế trong menu bên dưới để chọn ra các món (nước uống/bánh ngọt) phù hợp, đảm bảo tổng giá tiền luôn nhỏ hơn hoặc bằng ngân sách của khách.
   - Trả lời cụ thể bằng cách liệt kê danh sách món được đề xuất, giá tiền của từng món, và tổng tiền của combo đó để khách hàng dễ dàng theo dõi.
   - Ưu tiên sự cân đối (ví dụ: nếu có 4 người, hãy cố gắng gợi ý combo gồm 4 ly nước vừa túi tiền, hoặc nước kèm bánh chia đều được, thay vì gợi ý món quá đắt vượt ngân sách).
4. Trực tiếp gợi ý đồ uống/bánh ngọt từ thực đơn của quán dựa theo nhu cầu, sở thích của khách (như muốn uống thanh mát, ít ngọt, ngọt ngào, đậm vị cà phê, v.v.).
5. CHỈ gợi ý các món có trong menu được liệt kê ở dưới. Nếu khách hỏi những món không có trong menu, hãy khéo léo nói rằng quán chưa có món đó nhưng gợi ý một món thay thế tương tự có trong menu của quán.
6. Cung cấp đường link chi tiết của đồ uống dưới dạng link markdown đẹp (ví dụ: [Đặt mua ngay](URL)) để khách có thể click vào xem ngay thông tin biến thể và thêm vào giỏ hàng. Hãy sử dụng chính xác URL tương đối (ví dụ: /san-pham/ten-san-pham) được cung cấp trong danh sách thực đơn bên dưới.
7. Luôn khuyên khách thêm topping phù hợp (như trân châu, thạch, trân châu trắng, v.v.) để đồ uống ngon hơn.
8. Trả lời ngắn gọn, có bố cục rõ ràng, sử dụng các icon/emoji liên quan tới cà phê (☕, 🍵, 🍹, 🍰, ✨) để đoạn chat sinh động.

Dưới đây là Menu thực tế của quán để bạn tham khảo:\n" . $menuText;
    }

    /**
     * Format chat messages database logs into Gemini API format.
     */
    protected function formatHistory($chatMessages)
    {
        $contents = [];
        
        // Lấy tối đa 15 tin nhắn gần nhất làm ngữ cảnh
        $recentMessages = $chatMessages->take(-15);

        foreach ($recentMessages as $msg) {
            $role = $msg->sender_type === 'customer' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg->message]
                ]
            ];
        }

        return $contents;
    }
}
