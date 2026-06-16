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

        // 1. Xây dựng System Instruction (Menu động từ DB & thông tin khách hàng)
        $systemInstruction = $this->buildSystemInstruction();

        // 2. Định dạng hội thoại theo chuẩn API Gemini
        $contents = $this->formatHistory($chatMessages);

        // 3. Khai báo các tool (Functions) cho Gemini
        $tools = [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'addToCart',
                        'description' => 'Thêm một hoặc nhiều sản phẩm (đồ uống hoặc bánh ngọt) vào giỏ hàng của khách hàng.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'items' => [
                                    'type' => 'ARRAY',
                                    'description' => 'Danh sách các sản phẩm và toppings kèm theo',
                                    'items' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'product_name' => [
                                                'type' => 'STRING',
                                                'description' => 'Tên chính xác của sản phẩm trong menu.'
                                            ],
                                            'size' => [
                                                'type' => 'STRING',
                                                'description' => "Kích cỡ: 'S', 'M', 'L' hoặc 'Mặc định'."
                                            ],
                                            'quantity' => [
                                                'type' => 'INTEGER',
                                                'description' => 'Số lượng món này.'
                                            ],
                                            'toppings' => [
                                                'type' => 'ARRAY',
                                                'items' => [
                                                    'type' => 'STRING'
                                                ],
                                                'description' => "Danh sách tên các topping đi kèm (ví dụ: 'Trân Châu Đen')."
                                            ]
                                        ],
                                        'required' => ['product_name', 'size', 'quantity']
                                    ]
                                ]
                            ],
                            'required' => ['items']
                        ]
                    ],
                    [
                        'name' => 'createOrder',
                        'description' => 'Tạo đơn hàng trực tiếp cho khách hàng sau khi họ đồng ý đặt mua các món và đã cung cấp thông tin giao nhận.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'customer_name' => [
                                    'type' => 'STRING',
                                    'description' => 'Tên người nhận hàng'
                                ],
                                'customer_phone' => [
                                    'type' => 'STRING',
                                    'description' => 'Số điện thoại nhận hàng'
                                ],
                                'shipping_address' => [
                                    'type' => 'STRING',
                                    'description' => "Địa chỉ giao hàng đầy đủ (hoặc ghi chú khác/số bàn nếu không phải giao tận nhà)"
                                ],
                                'order_type' => [
                                    'type' => 'STRING',
                                    'enum' => ['delivery', 'at_table', 'take_away'],
                                    'description' => "Hình thức: 'delivery' (giao hàng), 'at_table' (ăn tại quán), 'take_away' (mang đi)"
                                ],
                                'table_number' => [
                                    'type' => 'INTEGER',
                                    'description' => "Số bàn nếu order_type là 'at_table'"
                                ],
                                'payment_method' => [
                                    'type' => 'STRING',
                                    'enum' => ['cash', 'qr'],
                                    'description' => "Phương thức thanh toán: 'cash' (tiền mặt COD) hoặc 'qr' (chuyển khoản ngân hàng qua mã QR)"
                                ],
                                'items' => [
                                    'type' => 'ARRAY',
                                    'description' => 'Danh sách các sản phẩm và toppings kèm theo trong đơn hàng này',
                                    'items' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'product_name' => [
                                                'type' => 'STRING',
                                                'description' => 'Tên chính xác của sản phẩm trong menu.'
                                            ],
                                            'size' => [
                                                'type' => 'STRING',
                                                'description' => "Kích cỡ: 'S', 'M', 'L' hoặc 'Mặc định'."
                                            ],
                                            'quantity' => [
                                                'type' => 'INTEGER',
                                                'description' => 'Số lượng món này.'
                                            ],
                                            'toppings' => [
                                                'type' => 'ARRAY',
                                                'items' => [
                                                    'type' => 'STRING'
                                                ],
                                                'description' => 'Danh sách tên các topping đi kèm.'
                                            ]
                                        ],
                                        'required' => ['product_name', 'size', 'quantity']
                                    ]
                                ]
                            ],
                            'required' => ['order_type', 'payment_method', 'items']
                        ]
                    ]
                ]
            ]
        ];

        try {
            $payload = [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'tools' => $tools,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 4000,
                ]
            ];

            Log::info('Gemini Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));

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
                ->post($this->endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Gemini Response: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
                
                $candidate = $data['candidates'][0] ?? null;
                if ($candidate && isset($candidate['content']['parts'])) {
                    $parts = $candidate['content']['parts'];
                    
                    // Kiểm tra xem có yêu cầu gọi Hàm (Function Calling) trong bất kỳ part nào không
                    $hasFunctionCall = false;
                    $functionCall = null;
                    foreach ($parts as $part) {
                        if (isset($part['functionCall'])) {
                            $hasFunctionCall = true;
                            $functionCall = $part['functionCall'];
                            break;
                        }
                    }
                    
                    if ($hasFunctionCall) {
                        $functionName = $functionCall['name'];
                        $args = $functionCall['args'] ?? [];
                        
                        // Thực thi hàm PHP tương ứng
                        $result = $this->executeTool($functionName, $args);
                        Log::info('Gemini Executed Tool ' . $functionName . ' with result: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
                        
                        // Thêm lượt hội thoại chứa yêu cầu gọi hàm của Model vào contents
                        $payload['contents'][] = $candidate['content'];
                        
                        // Thêm kết quả phản hồi của hàm vào contents
                        $payload['contents'][] = [
                            'role' => 'tool',
                            'parts' => [
                                [
                                    'functionResponse' => [
                                        'name' => $functionName,
                                        'response' => $result
                                    ]
                                ]
                            ]
                        ];

                        Log::info('Gemini Callback Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
                        
                        // Gọi lại API lần 2 để Gemini tổng hợp câu trả lời tự nhiên
                        $response2 = Http::timeout(15)
                            ->withOptions([
                                'curl' => [
                                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                                ]
                            ])
                            ->withHeaders([
                                'Content-Type' => 'application/json',
                                'x-goog-api-key' => $this->apiKey,
                            ])
                            ->post($this->endpoint, $payload);
                            
                        if ($response2->successful()) {
                            $data2 = $response2->json();
                            Log::info('Gemini Callback Response: ' . json_encode($data2, JSON_UNESCAPED_UNICODE));
                            
                            $candidate2 = $data2['candidates'][0] ?? null;
                            if ($candidate2 && isset($candidate2['content']['parts'])) {
                                $textParts = [];
                                foreach ($candidate2['content']['parts'] as $part) {
                                    if (isset($part['text'])) {
                                        $textParts[] = $part['text'];
                                    }
                                }
                                return implode('', $textParts);
                            }
                            return 'Dạ, Chill Chill đã thực hiện hành động thành công rồi ạ! ☕';
                        }
                        
                        Log::error('Gemini Tool Call Callback Error: ' . $response2->body());
                        return 'Dạ, Chill Chill đã xử lý hành động nhưng gặp sự cố khi phản hồi. Bạn vui lòng kiểm tra giỏ hàng nhé! 🙏';
                    }

                    // Nếu không có function call, ghép tất cả text parts lại
                    $textParts = [];
                    foreach ($parts as $part) {
                        if (isset($part['text'])) {
                            $textParts[] = $part['text'];
                        }
                    }
                    if (!empty($textParts)) {
                        return implode('', $textParts);
                    }
                }

                return 'Dạ, Chill Chill chưa nghe rõ ý bạn. Bạn có thể nói chi tiết hơn được không?';
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

        // Lấy thông tin khách hàng đang đăng nhập nếu có
        $userInfo = "";
        if (auth()->check()) {
            $user = auth()->user();
            $addresses = [];
            if ($user->address) {
                $decoded = json_decode($user->address, true);
                $addresses = is_array($decoded) ? $decoded : [$user->address];
            }
            $addressStr = implode('; ', $addresses);
            $userInfo = "THÔNG TIN KHÁCH HÀNG ĐÃ ĐĂNG NHẬP:\n" .
                "- Họ tên khách hàng: {$user->name}\n" .
                "- Email: {$user->email}\n" .
                "- Số điện thoại: " . ($user->phone ?? 'Chưa cập nhật') . "\n" .
                "- Các địa chỉ nhận hàng đã lưu: " . ($addressStr ?: 'Chưa cập nhật') . "\n\n" .
                "Hướng dẫn: Bạn hãy chủ động chào khách bằng tên của họ, hỏi xem họ có muốn sử dụng thông tin và địa chỉ đã lưu ở trên để giao hàng hay không. Nếu khách xác nhận đồng ý, hãy sử dụng ngay các thông tin này để gọi tool `createOrder` mà không bắt khách phải nhập lại.\n";
        } else {
            $userInfo = "KHÁCH HÀNG CHƯA ĐĂNG NHẬP (KHÁCH VÃNG LAI):\n" .
                "Hướng dẫn: Bạn nên nhắc nhở khách hàng đăng nhập tài khoản để được tích điểm thành viên và áp dụng các mã giảm giá. Tuy nhiên, nếu họ vẫn muốn đặt hàng nhanh qua chat, hãy hỏi họ Tên, Số điện thoại và Địa chỉ giao hàng để tạo đơn.\n";
        }

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

" . $userInfo . "

Nhiệm vụ của bạn:
1. Bạn TUYỆT ĐỐI KHÔNG chào hỏi (như 'Chào bạn', 'Xin chào', 'Chill Chill Coffee xin chào',...) hoặc chúc (như 'Chúc bạn một ngày tốt lành', 'Chúc bạn buổi tối vui vẻ',...) trong bất kỳ tin nhắn phản hồi nào của mình. Lý do là vì tin nhắn mở đầu của cửa sổ chat (do hệ thống hiển thị sẵn) đã chào hỏi và chúc khách hàng theo thời gian thực rồi. Bạn chỉ cần đi thẳng vào việc phản hồi câu hỏi hoặc thực hiện tư vấn món cho khách.
2. Dựa vào thời gian hiện tại của hệ thống để đề xuất món ăn/thức uống phù hợp nhất cho khách hàng:
   - Hãy giới thiệu các món có trong menu ở dưới dựa theo danh mục (Cà phê Phin, Trà Trái Cây, Đá Xay, Bánh Ngọt).
   - Hãy lưu ý rằng các món trong Menu thực tế của quán đang được đặt tên theo định dạng 'Món Ngon Chill Chill X' (X là số, ví dụ: 'Món Ngon Chill Chill 12', 'Món Ngon Chill Chill 3'). Bạn PHẢI sử dụng chính xác các tên này khi gợi ý và gọi hàm. Bạn có thể giải thích thêm loại danh mục của món đó để khách dễ hiểu, ví dụ: 'Món Ngon Chill Chill 12 (Cà phê Phin)' hoặc 'Món Ngon Chill Chill 3 (Bánh Ngọt)'.
   - Không tự bịa ra các tên món như 'cà phê sữa đá', 'bạc xỉu', 'trà đào', 'bánh sừng bò' để tư vấn vì khách hàng sẽ không tìm thấy.
3. Hỗ trợ tư vấn tài chính thông minh khi khách hàng đưa ra ngân sách giới hạn hoặc số lượng người (ví dụ: 'tôi có 200k, làm sao cho 4 người có đủ nước và bánh', hoặc 'mình có 100k nên gọi gì'):
   - Hãy tính toán thông minh dựa vào bảng giá thực tế trong menu bên dưới để chọn ra các món (nước uống/bánh ngọt) phù hợp, đảm bảo tổng giá tiền luôn nhỏ hơn hoặc bằng ngân sách của khách.
   - Trả lời cụ thể bằng cách liệt kê danh sách món được đề xuất, giá tiền của từng món, và tổng tiền của combo đó để khách hàng dễ dàng theo dõi.
   - Nếu ngân sách quá ít không đủ chia đều cho tất cả mọi người có cả nước lẫn bánh, hãy tính toán và khuyên khách hàng chọn combo chỉ gồm nước (ví dụ: 4 ly nước hết 120k-140k), hoặc 4 ly nước kèm 2 cái bánh để chia sẻ cùng nhau, đảm bảo tổng tiền không vượt quá ngân sách. Hãy giải thích rõ ràng và khéo léo cho khách.
4. Trực tiếp gợi ý đồ uống/bánh ngọt từ thực đơn của quán dựa theo nhu cầu, sở thích của khách (như muốn uống thanh mát, ít ngọt, ngọt ngào, đậm vị cà phê, v.v.).
5. CHỈ gợi ý các món có trong menu được liệt kê ở dưới. Nếu khách hỏi những món không có trong menu, hãy khéo léo nói rằng quán chưa có món đó nhưng gợi ý một món thay thế tương tự có trong menu của quán.
6. Cung cấp đường link chi tiết của đồ uống dưới dạng link markdown đẹp (ví dụ: [Đặt mua ngay](URL)) để khách có thể click vào xem ngay thông tin biến thể và thêm vào giỏ hàng. Hãy sử dụng chính xác URL tương đối (ví dụ: /san-pham/ten-san-pham) được cung cấp trong danh sách thực đơn bên dưới.
7. Luôn khuyên khách thêm topping phù hợp (như trân châu, thạch, trân châu trắng, v.v.) để đồ uống ngon hơn.
8. Trả lời ngắn gọn, có bố cục rõ ràng, sử dụng các icon/emoji liên quan tới cà phê (☕, 🍵, 🍹, 🍰, ✨) để đoạn chat sinh động.
9. ĐẶC BIỆT - TUÂN THỦ NGHIÊM NGẶT LUỒNG TƯ VẤN VÀ ĐẶT HÀNG THEO ĐÚNG THỨ TỰ SAU:
   - **Bước 1 (Hỏi món)**: Hỏi nhu cầu/sở thích hoặc ngân sách, số người của khách.
   - **Bước 2 (Tư vấn món)**: Đề xuất cụ thể các sản phẩm 'Món Ngon Chill Chill X' từ menu bên dưới phù hợp với nhu cầu.
   - **Bước 3 (Chốt món)**: Khi khách đồng ý với các món gợi ý, hãy xác nhận danh sách và số lượng món cuối cùng, sau đó gọi ngay hàm `addToCart` để thêm các món này vào giỏ hàng cho khách.
   - **Bước 4 (Hỏi hình thức phục vụ)**: Sau khi chốt món xong, hãy hỏi khách xem họ muốn dùng tại quán hay giao đi: 'Bạn muốn dùng tại quán hay giao đi ạ?'
   - **Bước 5 (Tạo đơn tương ứng)**:
     - Nếu khách chọn **dùng tại quán** (order_type là 'at_table'): Hãy hỏi số bàn (bàn số mấy?) và phương thức thanh toán (tiền mặt 'cash' hoặc chuyển khoản 'qr'). Sau đó gọi hàm `createOrder` với `order_type = 'at_table'` và `table_number = [số bàn]`. Bạn tuyệt đối không hỏi thêm họ tên, số điện thoại hay địa chỉ nhận hàng của họ.
     - Nếu khách chọn **giao hàng** (order_type là 'delivery'): Hãy hỏi thông tin giao nhận: Họ tên, Số điện thoại, Địa chỉ nhận hàng (nếu đã đăng nhập, hỏi xem họ có muốn giao tới địa chỉ đã lưu hay không) và phương thức thanh toán ('cash' hoặc 'qr'). Sau đó gọi hàm `createOrder` với `order_type = 'delivery'`.
   - Sau khi đơn hàng được tạo thành công, hãy thông báo đơn hàng của họ đang được xử lý và nhắc khách hàng xem màn hình chuyển hướng.

Dưới đây là Menu thực tế của quán để bạn tham khảo:\n" . $menuText;
    }

    /**
     * Execute tool from Gemini model.
     */
    protected function executeTool($name, $args)
    {
        if ($name === 'addToCart') {
            return $this->toolAddToCart($args['items'] ?? []);
        } elseif ($name === 'createOrder') {
            return $this->toolCreateOrder($args);
        }
        return ['success' => false, 'error' => 'Unknown function'];
    }

    /**
     * Add to Cart Tool.
     */
    protected function toolAddToCart($items)
    {
        $cart = session()->get('cart', []);
        $addedCount = 0;
        $details = [];

        foreach ($items as $item) {
            $productName = $item['product_name'] ?? '';
            $sizeName = $item['size'] ?? 'Mặc định';
            $qty = $item['quantity'] ?? 1;
            $toppingNames = $item['toppings'] ?? [];

            // 1. Tìm sản phẩm
            $product = DB::table('products')
                ->where('name', 'like', trim($productName))
                ->first();

            if (!$product) {
                // Thử tìm gần đúng
                $product = DB::table('products')
                    ->where('name', 'like', '%' . trim($productName) . '%')
                    ->first();
            }

            if (!$product) {
                $details[] = "Không tìm thấy sản phẩm '{$productName}'";
                continue;
            }

            // 2. Tìm size & variant
            $size = DB::table('sizes')
                ->where('name', 'like', trim($sizeName))
                ->first();

            if (!$size && ($sizeName === 'S' || $sizeName === 'M' || $sizeName === 'L')) {
                $size = DB::table('sizes')
                    ->where('name', 'like', '%' . trim($sizeName) . '%')
                    ->first();
            }

            $variant = null;
            if ($size) {
                $variant = DB::table('product_variants')
                    ->where('product_id', $product->product_id)
                    ->where('size_id', $size->size_id)
                    ->first();
            }

            if (!$variant) {
                $variant = DB::table('product_variants')
                    ->where('product_id', $product->product_id)
                    ->orderBy('price', 'asc')
                    ->first();
            }

            if (!$variant) {
                $details[] = "Không có sẵn kích cỡ '{$sizeName}' cho '{$product->name}'";
                continue;
            }

            $actualSizeName = DB::table('sizes')->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định';

            // 3. Toppings
            $toppingDetails = [];
            $toppingTotal = 0;
            $toppingsKeyArr = [];

            foreach ($toppingNames as $tName) {
                $top = DB::table('toppings')
                    ->where('name', 'like', trim($tName))
                    ->where('status', 1)
                    ->first();

                if (!$top) {
                    $top = DB::table('toppings')
                        ->where('name', 'like', '%' . trim($tName) . '%')
                        ->where('status', 1)
                        ->first();
                }

                if ($top) {
                    if (isset($toppingDetails[$top->topping_id])) {
                        $toppingDetails[$top->topping_id]['qty'] += 1;
                    } else {
                        $toppingDetails[$top->topping_id] = [
                            'name' => $top->name,
                            'price' => $top->price,
                            'qty' => 1
                        ];
                    }
                    $toppingTotal += $top->price;
                    $toppingsKeyArr[$top->topping_id] = ($toppingsKeyArr[$top->topping_id] ?? 0) + 1;
                }
            }

            ksort($toppingsKeyArr);
            $cartKey = md5($product->product_id . '_' . $variant->variant_id . '_' . serialize($toppingsKeyArr));

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $qty;
            } else {
                $cart[$cartKey] = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'image' => $product->image_url,
                    'variant_id' => $variant->variant_id,
                    'size_name' => $actualSizeName,
                    'price' => $variant->price,
                    'quantity' => $qty,
                    'toppings' => $toppingDetails,
                    'topping_total' => $toppingTotal,
                ];
            }

            $addedCount += $qty;
            $details[] = "Đã thêm {$qty}x {$product->name} (size {$actualSizeName}) vào giỏ hàng";
        }

        if ($addedCount > 0) {
            session()->put('cart', $cart);
            \App\Http\Controllers\CartController::checkAndRecalculateVoucher();
            session()->put('cart_updated_by_bot', true);

            return [
                'success' => true,
                'message' => 'Đã thêm sản phẩm thành công.',
                'details' => $details,
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể thêm món nào vào giỏ hàng.',
            'details' => $details
        ];
    }

    /**
     * Create Order Tool.
     */
    protected function toolCreateOrder($args)
    {
        $orderType = $args['order_type'] ?? 'delivery';
        
        // Xác định tên, sđt, địa chỉ mặc định từ tài khoản đăng nhập nếu có
        $authName = auth()->check() ? auth()->user()->name : null;
        $authPhone = auth()->check() ? auth()->user()->phone : null;
        $authAddress = null;
        if (auth()->check() && auth()->user()->address) {
            $decoded = json_decode(auth()->user()->address, true);
            $addresses = is_array($decoded) ? $decoded : [auth()->user()->address];
            $authAddress = $addresses[0] ?? null;
        }

        $customerName = $args['customer_name'] ?? $authName;
        if (!$customerName) {
            $customerName = ($orderType === 'at_table') ? 'Khách dùng tại quán' : 'Khách từ Bot AI';
        }
        
        $customerPhone = $args['customer_phone'] ?? $authPhone ?? '';
        $shippingAddress = $args['shipping_address'] ?? $authAddress ?? '';
        
        if ($orderType === 'at_table') {
            $shippingAddress = 'Dùng tại quán';
        }

        $tableNumber = $args['table_number'] ?? null;
        $paymentMethod = $args['payment_method'] ?? 'cash';
        $itemsArg = $args['items'] ?? [];

        if (empty($itemsArg)) {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return [
                    'success' => false,
                    'message' => 'Giỏ hàng đang trống và bạn chưa chỉ định món hàng nào để đặt.'
                ];
            }
        } else {
            $cart = [];
            foreach ($itemsArg as $item) {
                $productName = $item['product_name'] ?? '';
                $sizeName = $item['size'] ?? 'Mặc định';
                $qty = $item['quantity'] ?? 1;
                $toppingNames = $item['toppings'] ?? [];

                // Tìm sản phẩm
                $product = DB::table('products')
                    ->where('name', 'like', trim($productName))
                    ->first();

                if (!$product) {
                    $product = DB::table('products')
                        ->where('name', 'like', '%' . trim($productName) . '%')
                        ->first();
                }

                if (!$product) {
                    return [
                        'success' => false,
                        'message' => "Không tìm thấy sản phẩm '{$productName}' để tạo đơn."
                    ];
                }

                // Tìm size
                $size = DB::table('sizes')->where('name', 'like', trim($sizeName))->first();
                if (!$size && ($sizeName === 'S' || $sizeName === 'M' || $sizeName === 'L')) {
                    $size = DB::table('sizes')->where('name', 'like', '%' . trim($sizeName) . '%')->first();
                }

                $variant = null;
                if ($size) {
                    $variant = DB::table('product_variants')
                        ->where('product_id', $product->product_id)
                        ->where('size_id', $size->size_id)
                        ->first();
                }

                if (!$variant) {
                    $variant = DB::table('product_variants')
                        ->where('product_id', $product->product_id)
                        ->orderBy('price', 'asc')
                        ->first();
                }

                if (!$variant) {
                    return [
                        'success' => false,
                        'message' => "Kích cỡ '{$sizeName}' của '{$product->name}' không có sẵn để đặt."
                    ];
                }

                $actualSizeName = DB::table('sizes')->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định';

                // Toppings
                $toppingDetails = [];
                $toppingTotal = 0;
                $toppingsKeyArr = [];

                foreach ($toppingNames as $tName) {
                    $top = DB::table('toppings')
                        ->where('name', 'like', trim($tName))
                        ->where('status', 1)
                        ->first();
                    if (!$top) {
                        $top = DB::table('toppings')
                            ->where('name', 'like', '%' . trim($tName) . '%')
                            ->where('status', 1)
                            ->first();
                    }
                    if ($top) {
                        if (isset($toppingDetails[$top->topping_id])) {
                            $toppingDetails[$top->topping_id]['qty'] += 1;
                        } else {
                            $toppingDetails[$top->topping_id] = [
                                'name' => $top->name,
                                'price' => $top->price,
                                'qty' => 1
                            ];
                        }
                        $toppingTotal += $top->price;
                        $toppingsKeyArr[$top->topping_id] = ($toppingsKeyArr[$top->topping_id] ?? 0) + 1;
                    }
                }

                ksort($toppingsKeyArr);
                $cartKey = md5($product->product_id . '_' . $variant->variant_id . '_' . serialize($toppingsKeyArr));

                $cart[$cartKey] = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'image' => $product->image_url,
                    'variant_id' => $variant->variant_id,
                    'size_name' => $actualSizeName,
                    'price' => $variant->price,
                    'quantity' => $qty,
                    'toppings' => $toppingDetails,
                    'topping_total' => $toppingTotal,
                ];
            }
        }

        // Tính toán tổng tiền
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += ($item['price'] + $item['topping_total']) * $item['quantity'];
        }

        $voucher = session()->get('voucher');
        $discountAmount = 0;
        $voucherId = null;
        if ($voucher) {
            $voucherId = $voucher['voucher_id'];
            $discountAmount = $voucher['discount_amount'];
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);

        try {
            $today = now()->format('Y-m-d');
            $shift = \App\Models\Shift::where('date', $today)->first();
            $shiftId = $shift ? $shift->id : null;

            // Tạo order
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => auth()->id(),
                'voucher_id' => $voucherId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'shipping_address' => $shippingAddress,
                'order_type' => $orderType,
                'table_number' => $tableNumber,
                'payment_method' => $paymentMethod,
                'total_amount' => $finalAmount,
                'discount_amount' => $discountAmount,
                'status' => 'pending',
                'items' => json_encode($cart, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($voucherId) {
                DB::table('vouchers')->where('voucher_id', $voucherId)->increment('used_count');
            }

            session()->forget('cart');
            session()->forget('voucher');

            session()->put('order_created_by_bot', $orderId);
            session()->put('order_payment_method_by_bot', $paymentMethod);
            session()->put('cart_updated_by_bot', true);

            return [
                'success' => true,
                'order_id' => $orderId,
                'total_amount' => $finalAmount,
                'payment_method' => $paymentMethod,
                'message' => 'Đã tạo đơn hàng thành công.'
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi bot khi tạo đơn hàng: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi hệ thống khi tạo đơn hàng: ' . $e->getMessage()
            ];
        }
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

