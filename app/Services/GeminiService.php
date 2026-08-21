<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeminiService
{
    protected $apiKey;
    protected $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Get response from Gemini API for chat.
     */
    public function getAiResponse($chatMessages)
    {
        // Lấy tin nhắn cuối cùng của khách hàng
        $lastUserMsg = '';
        if ($chatMessages instanceof \Illuminate\Support\Collection) {
            $lastMsgObj = $chatMessages->where('sender_type', 'customer')->last();
            $lastUserMsg = $lastMsgObj ? $lastMsgObj->message : '';
        }

        if (!$this->apiKey) {
            Log::info('Gemini API key is not set. Using Smart Local Bot Fallback.');
            return $this->getSmartFallbackResponse($lastUserMsg);
        }

        // 1. Xây dựng System Instruction (Menu động từ DB)
        $systemInstruction = $this->buildSystemInstruction();

        // 2. Định dạng hội thoại theo chuẩn API Gemini
        $contents = $this->formatHistory($chatMessages);

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint . '?key=' . $this->apiKey, [
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
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($reply)) {
                    return $reply;
                }
            }

            Log::error('Gemini API Error: ' . $response->body());
            return $this->getSmartFallbackResponse($lastUserMsg);

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return $this->getSmartFallbackResponse($lastUserMsg);
        }
    }

    /**
     * Smart local fallback bot that queries DB products dynamically.
     */
    protected function getSmartFallbackResponse($userMsg = '')
    {
        $msgLower = mb_strtolower($userMsg);

        try {
            // Lấy ngẫu nhiên 3 sản phẩm nổi bật / mới nhất từ DB
            $featuredProducts = DB::table('products')
                ->where('status', 1)
                ->inRandomOrder()
                ->take(3)
                ->get();



            // 1. Nếu hỏi về Bánh ngọt / Ăn kèm / Tráng miệng
            if (str_contains($msgLower, 'bánh') || str_contains($msgLower, 'cake') || str_contains($msgLower, 'tráng miệng') || str_contains($msgLower, 'ăn kèm') || str_contains($msgLower, 'bánh ngọt')) {
                $cakeProducts = DB::table('products')
                    ->where('category_id', 4) // ID 4: Bánh Ngọt
                    ->where('status', 1)
                    ->take(3)
                    ->get();

                if ($cakeProducts->isNotEmpty()) {
                    $reply = "Dạ, nhâm nhi tách trà chiều cùng bánh ngọt là nhất luôn ạ! 🍰🥐\n\nGợi ý các món Bánh tươi nướng mới mỗi ngày tại Chill Chill:\n\n";
                    foreach ($cakeProducts as $p) {
                        $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                        $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thêm vào giỏ bánh]({$url})\n\n";
                    }
                    return $reply;
                }
            }

            // 2. Nếu hỏi về Trà sữa (Phải kiểm tra TRƯỚC Trà Trái Cây vì 'trà sữa' chứa từ 'trà')
            if (str_contains($msgLower, 'trà sữa') || str_contains($msgLower, 'milk tea') || str_contains($msgLower, 'hồng trà sữa') || str_contains($msgLower, 'lục trà sữa')) {
                $bobaProducts = DB::table('products')
                    ->where('category_id', 7) // ID 7: Trà sữa
                    ->where('status', 1)
                    ->take(3)
                    ->get();

                if ($bobaProducts->isNotEmpty()) {
                    $reply = "Dạ, Trà sữa thơm béo ngậy chuẩn gu đây ạ! 🧋✨\n\nChill Chill gợi ý các món Trà Sữa được yêu thích nhất:\n\n";
                    foreach ($bobaProducts as $p) {
                        $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                        $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thêm vào giỏ hàng]({$url})\n\n";
                    }
                    $reply .= "Nhớ thêm Trân châu đen hoặc Thạch dừa cho tròn vị bạn nhé! ❤️";
                    return $reply;
                }
            }

            // 3. Nếu hỏi về Đá Xay
            if (str_contains($msgLower, 'đá xay') || str_contains($msgLower, 'smoothie') || str_contains($msgLower, 'matcha') || str_contains($msgLower, 'caramel') || str_contains($msgLower, 'chocolate')) {
                $iceProducts = DB::table('products')
                    ->where('category_id', 3) // ID 3: Đá Xay
                    ->where('status', 1)
                    ->take(3)
                    ->get();

                if ($iceProducts->isNotEmpty()) {
                    $reply = "Dạ, những ly Đá Xay mát lạnh, đậm vị béo thơm đã sẵn sàng phục vụ bạn đây ạ! ❄️🥤\n\nChill Chill gợi ý các món Đá Xay cực 'hot':\n\n";
                    foreach ($iceProducts as $p) {
                        $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                        $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thêm vào giỏ hàng]({$url})\n\n";
                    }
                    return $reply;
                }
            }

            // 4. Nếu hỏi Nước ép / Soda
            if (str_contains($msgLower, 'nước ép') || str_contains($msgLower, 'soda') || str_contains($msgLower, 'cam vắt') || str_contains($msgLower, 'dưa hấu')) {
                $otherProducts = DB::table('products')
                    ->where('category_id', 8) // ID 8: Khác (Soda & Nước ép)
                    ->where('status', 1)
                    ->take(3)
                    ->get();

                if ($otherProducts->isNotEmpty()) {
                    $reply = "Dạ, Nước ép tươi giàu Vitamin và Soda sảng khoái đây ạ! 🍊🍹\n\nGợi ý dành riêng cho bạn:\n\n";
                    foreach ($otherProducts as $p) {
                        $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                        $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thêm vào giỏ hàng]({$url})\n\n";
                    }
                    return $reply;
                }
            }

            // 5. Nếu hỏi về Cà phê
            if (str_contains($msgLower, 'cafe') || str_contains($msgLower, 'cà phê') || str_contains($msgLower, 'đắng') || str_contains($msgLower, 'tỉnh táo') || str_contains($msgLower, 'bạc sỉu')) {
                $cafeProducts = DB::table('products')
                    ->where('category_id', 1) // ID 1: Cà phê Phin
                    ->where('status', 1)
                    ->take(3)
                    ->get();

                if ($cafeProducts->isNotEmpty()) {
                    $reply = "Dạ, Chill Chill Coffee chào bạn ạ! ☕\n\nĐể giúp bạn tỉnh táo và tràn đầy năng lượng, Chill Chill gợi ý các món Cà phê nguyên chất tuyệt hảo dành cho bạn:\n\n";
                    foreach ($cafeProducts as $p) {
                        $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                        $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thêm vào giỏ hàng]({$url})\n\n";
                    }
                    $reply .= "Bạn có muốn dùng kèm thêm Topping Trân châu hoặc Thạch cà phê thơm béo không ạ? ✨";
                    return $reply;
                }
            }

            // 6. Nếu hỏi về Trà trái cây / Giải nhiệt (Kiểm tra SAU Trà Sữa)
            if (str_contains($msgLower, 'trà') || str_contains($msgLower, 'giải nhiệt') || str_contains($msgLower, 'mát')) {
                $teaProducts = DB::table('products')
                    ->where('category_id', 2) // ID 2: Trà Trái Cây
                    ->where('status', 1)
                    ->take(3)
                    ->get();

                if ($teaProducts->isNotEmpty()) {
                    $reply = "Dạ, thanh mát và sảng khoái là gu của bạn phải không ạ? 🍵🍹\n\nChill Chill gợi ý các món Trà Trái Cây thanh nhiệt cực thơm ngon dành cho bạn:\n\n";
                    foreach ($teaProducts as $p) {
                        $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                        $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thưởng thức ngay]({$url})\n\n";
                    }
                    $reply .= "Thêm chút Trân châu hoàng kim hoặc Thạch dừa nữa là chuẩn gu luôn ạ! ✨";
                    return $reply;
                }
            }

            // 7. Nếu hỏi Combo
            if (str_contains($msgLower, 'combo') || str_contains($msgLower, 'tiết kiệm') || str_contains($msgLower, 'giảm')) {
                $combos = DB::table('combos')->where('status', 1)->take(2)->get();
                if ($combos->isNotEmpty()) {
                    $reply = "Dạ, quán có các gói Combo Tiết Kiệm cực kỳ ưu đãi dành cho bạn đây ạ! 🎁\n\n";
                    foreach ($combos as $c) {
                        $url = route('combo.show', ['id' => $c->combo_id]);
                        $reply .= "• **{$c->name}**: Giá chỉ " . number_format($c->price) . "đ (Tiết kiệm 30%)\n👉 [Đặt ngay Gói Combo]({$url})\n\n";
                    }
                    return $reply;
                }
            }

            // Phản hồi mặc định / Chào hỏi
            $reply = "Dạ, Chill Chill Coffee & Tea xin chào bạn ạ! ☕✨\n\nBạn đang muốn chọn đồ uống thanh mát, cà phê đậm vị hay bánh ngọt thơm ngon ạ? Chill Chill gợi ý một số món được yêu thích nhất hôm nay:\n\n";

            foreach ($featuredProducts as $p) {
                $url = route('product.show', ['slug' => $p->slug]) . '#add-to-cart-' . $p->product_id;
                $reply .= "• **{$p->name}**: {$p->description}\n👉 [🛒 Thêm vào giỏ hàng]({$url})\n\n";
            }

            $reply .= "Bạn cần Chill Chill tư vấn thêm chi tiết món nào không ạ? ❤️";
            return $reply;

        } catch (\Exception $e) {
            return "Dạ, Chill Chill Coffee & Tea chào bạn ạ! ☕ Bạn cần mình tư vấn chọn đồ uống hay bánh ngọt nào hôm nay ạ?";
        }
    }

    /**
     * Build the dynamic system prompt with updated menu from Database.
     */
    protected function buildSystemInstruction()
    {
        try {
            // Lấy sản phẩm, biến thể, kích cỡ và danh mục tương ứng
            $products = DB::table('products')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->select(
                    'products.product_id',
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
                        $pId  = $variants->first()->product_id;
                        
                        $pricesText = [];
                        foreach ($variants as $v) {
                            $pricesText[] = $v->size_name . ": " . number_format($v->price) . "đ";
                        }
                        
                        // Sinh link thêm nhanh vào giỏ hàng
                        $url = route('product.show', ['slug' => $slug]) . '#add-to-cart-' . $pId;
                        
                        $menuText .= "- **{$name}**: {$desc} (Giá: " . implode(', ', $pricesText) . "). Thêm vào giỏ hàng: [🛒 Thêm vào giỏ hàng]({$url})\n";
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
Nhiệm vụ của bạn:
1. Chào hỏi và nói chuyện với khách hàng bằng tiếng Việt tự nhiên, thân thiện, sử dụng các đại từ xưng hô lịch sự nhưng gần gũi.
2. NGUYÊN TẮC BẮT BUỘC VỀ DANH MỤC: Khi khách hàng hỏi về một Danh mục cụ thể (Ví dụ: 'Bánh ngọt', 'Trà trái cây', 'Cà phê', 'Trà sữa', 'Đá xay', 'Nước ép'), bạn CHỈ ĐƯỢC GỢI Ý CÁC MÓN THUỘC ĐÚNG DANH MỤC ĐÓ trong danh sách bên dưới. Tuyệt đối KHÔNG ĐƯỢC nhầm lẫn đưa đồ uống vào danh mục bánh hoặc ngược lại!
3. CHỈ gợi ý các món có trong menu được liệt kê ở dưới. Cung cấp đường link chi tiết của đồ uống dưới dạng link markdown đẹp (ví dụ: [Đặt mua ngay](URL)).
4. Trả lời ngắn gọn, có bố cục rõ ràng, sử dụng các icon/emoji liên quan tới cà phê (☕, 🍵, 🍹, 🍰, ✨) để đoạn chat sinh động.

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
