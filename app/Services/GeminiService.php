<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
        // Lấy tin nhắn mới nhất của khách hàng để làm dữ liệu tra cứu fallback
        $lastMsgObj = is_countable($chatMessages) ? $chatMessages->last() : null;
        $lastUserMsg = is_object($lastMsgObj) ? $lastMsgObj->message : (is_array($lastMsgObj) ? ($lastMsgObj['message'] ?? '') : '');

        if (!$this->apiKey) {
            Log::warning('Gemini API key is not set. Using Smart Local Search Fallback.');
            return $this->getSmartFallbackResponse($lastUserMsg);
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
                                    'enum' => ['cod', 'qr'],
                                    'description' => "Thanh toán: 'cod' (tiền mặt khi nhận), 'qr' (chuyển khoản ngân hàng QR Code)"
                                ],
                                'items' => [
                                    'type' => 'ARRAY',
                                    'description' => 'Danh sách các sản phẩm cần đặt mua',
                                    'items' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'product_name' => [
                                                'type' => 'STRING',
                                                'description' => 'Tên sản phẩm'
                                            ],
                                            'size' => [
                                                'type' => 'STRING',
                                                'description' => "Size: 'S', 'M', 'L' hoặc 'Mặc định'"
                                            ],
                                            'quantity' => [
                                                'type' => 'INTEGER',
                                                'description' => 'Số lượng'
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

            $response = Http::withoutVerifying()->timeout(30)
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
                        $modelContent = $candidate['content'];
                        if (isset($modelContent['parts'])) {
                            foreach ($modelContent['parts'] as &$part) {
                                if (isset($part['functionCall'])) {
                                    $part['functionCall']['args'] = empty($part['functionCall']['args']) ? (object)[] : (object)$part['functionCall']['args'];
                                }
                            }
                            unset($part);
                        }
                        $payload['contents'][] = $modelContent;
                        
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
                        $response2 = Http::withoutVerifying()->timeout(30)
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

                return $this->getSmartFallbackResponse($lastUserMsg);
            }

            Log::error('Gemini API Error: ' . $response->body());
            return $this->getSmartFallbackResponse($lastUserMsg);

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
1. Bạn TUYỆT ĐỐI KHÔNG chào hỏi (như 'Chào bạn', 'Xin chào', 'Chill Chill Coffee xin chào',...) hoặc chúc trong bất kỳ tin nhắn phản hồi nào. Đi thẳng vào việc phản hồi câu hỏi hoặc thực hiện tư vấn món cho khách.
2. NHẬN BIẾT VÀ XỬ LÝ CÁC TRƯỜNG HỢP CÂU HỎI CỦA KHÁCH HÀNG:
   - **Trường hợp 1: Hỏi các món theo danh mục (Cà phê Phin, Trà Trái Cây, Đá Xay, Bánh Ngọt, Topping)**: Phân loại các món theo đúng danh mục khách hỏi, báo giá và mô tả ngắn.
   - **Trường hợp 2: Hỏi chia khẩu phần theo số tiền & số người (Tư vấn Combo ngân sách)**: Ví dụ khách bảo 'tôi có 200k cho 4 người' hoặc 'mình có 100k nên gọi gì', hãy tính toán thông minh các món nước + bánh sao cho tổng giá <= ngân sách khách đưa ra. Liệt kê rõ từng món, giá từng món và tổng tiền combo.
   - **Trường hợp 3: Hỏi món ngon, món bán chạy nhất (Best Sellers / Hot Items)**: Giới thiệu các sản phẩm hot tiêu biểu nhất từ các danh mục của quán.
   - **Trường hợp 4: Hỏi theo sở thích / khẩu vị đặc biệt**: Đồ uống thanh mát giải nhiệt (Trà trái cây/Đá xay), đồ uống đậm vị tỉnh táo (Cà phê Phin), món ngọt ngào tráng miệng (Bánh ngọt).
3. ĐẶC BIỆT: Khi giới thiệu hoặc gợi ý bất kỳ món nào, hãy luôn đính kèm nút hành động theo định dạng Markdown: `[🛒 Thêm vào giỏ](action:add_to_cart?product_id=ID_SAN_PHAM&variant_id=ID_BIEN_THE)` để khách hàng có thể bấm mua trực tiếp ngay trên khung chat.
4. LUỒNG XÁC NHẬN VÀ ĐẶT HÀNG:
   - Khi khách đồng ý lấy món/combo, hãy gọi ngay hàm `addToCart` để thêm vào giỏ.
   - Hỏi hình thức phục vụ: **Dùng tại quán (bàn số mấy?)** hay **Giao hàng (Họ tên, SĐT, Địa chỉ?)**.
   - Gọi hàm `createOrder` tạo đơn tương ứng.

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
        $cart = \App\Http\Controllers\CartController::getCart();
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
            \App\Http\Controllers\CartController::saveCart($cart);
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

    /**
     * Get response from Gemini API for Admin Management.
     */
    public function getAdminAiResponse($chatMessages)
    {
        if (!$this->apiKey) {
            Log::warning('Gemini API key is not set.');
            return 'Dạ, hiện tại kết nối trợ lý AI đang gặp sự cố. Quản lý vui lòng thử lại sau! ☕';
        }

        // 1. Đọc System Instruction từ file rules
        $rulesPath = storage_path('app/ai/admin_rules.md');
        $systemInstruction = "";
        if (file_exists($rulesPath)) {
            $systemInstruction = file_get_contents($rulesPath);
        } else {
            $systemInstruction = "Bạn là Trợ lý AI Quản lý của Chill Chill.";
        }

        // 2. Định dạng hội thoại theo chuẩn API Gemini
        $contents = $this->formatHistory($chatMessages);

        // 3. Khai báo các tools cho Admin
        $tools = [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'getProductsList',
                        'description' => 'Lấy danh sách tất cả các sản phẩm hiện có của quán bao gồm ID, tên, danh mục và giá bán.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object)[]
                        ]
                    ],
                    [
                        'name' => 'createVoucher',
                        'description' => 'Tạo một mã giảm giá (voucher) mới cho cửa hàng.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'code' => [
                                    'type' => 'STRING',
                                    'description' => 'Mã voucher (Ví dụ: APRIl30, GIAM20k).'
                                ],
                                'discount_type' => [
                                    'type' => 'STRING',
                                    'enum' => ['percent', 'fixed'],
                                    'description' => 'Kiểu giảm giá: percent (theo phần trăm) hoặc fixed (số tiền cố định).'
                                ],
                                'discount_value' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Giá trị giảm (Ví dụ: 10 cho 10%, hoặc 30000 cho 30,000đ).'
                                ],
                                'min_order' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Giá trị đơn hàng tối thiểu để áp dụng voucher (Mặc định: 0).'
                                ],
                                'usage_limit' => [
                                    'type' => 'INTEGER',
                                    'description' => 'Tổng số lượt sử dụng tối đa.'
                                ],
                                'start_date' => [
                                    'type' => 'STRING',
                                    'description' => 'Ngày bắt đầu có hiệu lực (định dạng YYYY-MM-DD).'
                                ],
                                'end_date' => [
                                    'type' => 'STRING',
                                    'description' => 'Ngày kết thúc hiệu lực (định dạng YYYY-MM-DD).'
                                ]
                            ],
                            'required' => ['code', 'discount_type', 'discount_value']
                        ]
                    ],
                    [
                        'name' => 'adjustProductPrice',
                        'description' => 'Điều chỉnh giá bán cơ bản của một sản phẩm bằng ID.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'product_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID của sản phẩm cần đổi giá.'
                                ],
                                'new_price' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Mức giá mới cần đặt cho sản phẩm.'
                                ]
                            ],
                            'required' => ['product_id', 'new_price']
                        ]
                    ],
                    [
                        'name' => 'adjustVariantPrice',
                        'description' => 'Điều chỉnh giá bán của một biến thể kích cỡ sản phẩm (Size S, M, L...) bằng Variant ID.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'variant_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID của biến thể sản phẩm cần đổi giá.'
                                ],
                                'new_price' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Mức giá mới cần đặt cho biến thể sản phẩm.'
                                ]
                            ],
                            'required' => ['variant_id', 'new_price']
                        ]
                    ],
                    [
                        'name' => 'discountCategoryProducts',
                        'description' => 'Áp dụng chương trình giảm giá cho toàn bộ sản phẩm thuộc một Danh mục cụ thể (Ví dụ: giảm 10% danh mục Đá Xay).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'category_name' => [
                                    'type' => 'STRING',
                                    'description' => 'Tên danh mục sản phẩm (ví dụ: Đá Xay, Cà phê Phin, Bánh Ngọt).'
                                ],
                                'category_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID của danh mục sản phẩm nếu biết.'
                                ],
                                'discount_type' => [
                                    'type' => 'STRING',
                                    'enum' => ['percent', 'fixed'],
                                    'description' => 'Kiểu giảm giá: percent (theo %) hoặc fixed (theo số tiền).'
                                ],
                                'discount_value' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Mức giảm giá áp dụng (Ví dụ: 10 cho 10%, hoặc 5000 cho 5,000đ).'
                                ]
                            ],
                            'required' => ['discount_type', 'discount_value']
                        ]
                    ],
                    [
                        'name' => 'setCategoryPrice',
                        'description' => 'Đặt một mức giá bán cố định mới cho toàn bộ sản phẩm (và tất cả biến thể) thuộc một Danh mục cụ thể (Ví dụ: đồng bộ giá mục Cà phê Phin là 300000, đặt giá danh mục Bánh Ngọt thành 50000).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'category_name' => [
                                    'type' => 'STRING',
                                    'description' => 'Tên danh mục sản phẩm (ví dụ: Cà phê Phin, Đá Xay, Bánh Ngọt).'
                                ],
                                'category_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID của danh mục sản phẩm nếu biết.'
                                ],
                                'new_price' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Mức giá cố định mới áp dụng cho toàn bộ sản phẩm thuộc danh mục đó (Ví dụ: 300000).'
                                ]
                            ],
                            'required' => ['new_price']
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
                    'temperature' => 0.2,
                    'maxOutputTokens' => 4000,
                ]
            ];

            Log::info('Gemini Admin Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));

            $response = Http::withoutVerifying()->timeout(30)
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
                Log::info('Gemini Admin Response: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
                
                $candidate = $data['candidates'][0] ?? null;
                if ($candidate && isset($candidate['content']['parts'])) {
                    $parts = $candidate['content']['parts'];
                    
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
                        $result = $this->executeAdminTool($functionName, $args);
                        Log::info('Gemini Executed Admin Tool ' . $functionName . ' with result: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
                        
                        $modelContent = $candidate['content'];
                        if (isset($modelContent['parts'])) {
                            foreach ($modelContent['parts'] as &$part) {
                                if (isset($part['functionCall'])) {
                                    $part['functionCall']['args'] = empty($part['functionCall']['args']) ? (object)[] : (object)$part['functionCall']['args'];
                                }
                            }
                            unset($part);
                        }
                        $payload['contents'][] = $modelContent;
                        
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

                        Log::info('Gemini Admin Callback Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
                        
                        $response2 = Http::withoutVerifying()->timeout(30)
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
                            Log::info('Gemini Admin Callback Response: ' . json_encode($data2, JSON_UNESCAPED_UNICODE));
                            
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
                            return 'Dạ, em đã thực hiện thao tác quản trị thành công! ☕';
                        }
                        
                        Log::error('Gemini Admin Tool Call Callback Error: ' . $response2->body());
                        return 'Dạ, em đã xử lý thao tác nhưng gặp sự cố khi phản hồi lại kết quả. Quản lý vui lòng kiểm tra danh sách trong hệ thống ạ!';
                    }

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

                return 'Dạ, em chưa hiểu rõ yêu cầu của quản lý. Quản lý có thể cung cấp thêm chi tiết không ạ?';
            }

            Log::error('Gemini Admin API Error: ' . $response->body());
            return 'Dạ, hệ thống kết nối AI đang bận. Quản lý vui lòng thử lại sau ạ! ☕';

        } catch (\Exception $e) {
            Log::error('Gemini Admin Service Exception: ' . $e->getMessage());
            return 'Dạ, có lỗi kết nối trợ lý AI xảy ra. Quản lý thử lại sau nhé! 🙏';
        }
    }

    /**
     * Execute Admin Tools
     */
    protected function executeAdminTool($name, $args)
    {
        if ($name === 'getProductsList') {
            return $this->toolGetProductsList();
        } elseif ($name === 'createVoucher') {
            return $this->toolCreateVoucher($args);
        } elseif ($name === 'adjustProductPrice') {
            return $this->toolAdjustProductPrice($args);
        } elseif ($name === 'adjustVariantPrice') {
            return $this->toolAdjustVariantPrice($args);
        } elseif ($name === 'discountCategoryProducts') {
            return $this->toolDiscountCategoryProducts($args);
        } elseif ($name === 'setCategoryPrice') {
            return $this->toolSetCategoryPrice($args);
        }
        return ['success' => false, 'error' => 'Unknown admin function'];
    }

    /**
     * Tool: Get Products List for Admin
     */
    protected function toolGetProductsList()
    {
        try {
            $products = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->leftJoin('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
                ->select(
                    'products.product_id',
                    'products.name',
                    'categories.name as category_name',
                    'product_variants.variant_id',
                    'sizes.name as size_name',
                    'product_variants.price as variant_price'
                )
                ->orderBy('products.product_id')
                ->get();

            return [
                'success' => true,
                'products' => $products->map(function ($p) {
                    return [
                        'product_id' => $p->product_id,
                        'name' => $p->name,
                        'category' => $p->category_name,
                        'variant_id' => $p->variant_id,
                        'size' => $p->size_name,
                        'price' => $p->variant_price ?? $p->base_price
                    ];
                })
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tool: Create Voucher
     */
    protected function toolCreateVoucher($args)
    {
        $code = strtoupper($args['code'] ?? '');
        $discountType = $args['discount_type'] ?? 'fixed';
        $discountValue = (float)($args['discount_value'] ?? 0);
        $minOrder = (float)($args['min_order'] ?? 0);
        $usageLimit = isset($args['usage_limit']) ? (int)$args['usage_limit'] : null;
        $startDate = $args['start_date'] ?? now()->toDateString();
        $endDate = $args['end_date'] ?? null;

        if (empty($code)) {
            return ['success' => false, 'error' => 'Mã voucher không được để trống.'];
        }

        if ($discountValue < 0) {
            return ['success' => false, 'error' => 'Giá trị giảm giá không được nhỏ hơn 0.'];
        }

        if ($minOrder < 0) {
            return ['success' => false, 'error' => 'Yêu cầu đơn hàng tối thiểu không được nhỏ hơn 0.'];
        }

        // Kiểm tra xem đã tồn tại chưa
        $exists = DB::table('vouchers')->where('code', $code)->first();
        if ($exists) {
            return ['success' => false, 'error' => "Mã voucher '{$code}' đã tồn tại trong hệ thống."];
        }

        try {
            $id = DB::table('vouchers')->insertGetId([
                'code' => $code,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'min_order' => $minOrder,
                'usage_limit' => $usageLimit,
                'used_count' => 0,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return [
                'success' => true,
                'voucher_id' => $id,
                'code' => $code,
                'message' => "Tạo thành công mã giảm giá {$code}."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tool: Adjust base product price
     */
    protected function toolAdjustProductPrice($args)
    {
        $productId = $args['product_id'] ?? null;
        $newPrice = (float)($args['new_price'] ?? 0);

        if (!$productId) {
            return ['success' => false, 'error' => 'Thiếu ID hoặc tên sản phẩm.'];
        }

        if ($newPrice < 0) {
            return ['success' => false, 'error' => 'Mức giá mới của sản phẩm không được nhỏ hơn 0đ.'];
        }

        try {
            $product = null;
            if (is_numeric($productId)) {
                $product = DB::table('products')->where('product_id', (int)$productId)->first();
            }
            
            if (!$product) {
                // Thử tìm kiếm gần đúng theo tên sản phẩm nếu ID không phải số hoặc không tìm thấy
                $product = DB::table('products')
                    ->where('name', 'like', '%' . trim($productId) . '%')
                    ->first();
            }

            if (!$product) {
                return ['success' => false, 'error' => "Không tìm thấy sản phẩm với thông tin '{$productId}'. Thay đổi giá thất bại."];
            }

            DB::table('products')->where('product_id', $product->product_id)->update([
                'price' => $newPrice,
                'updated_at' => now()
            ]);

            return [
                'success' => true,
                'product_name' => $product->name,
                'new_price' => $newPrice,
                'message' => "Cập nhật giá sản phẩm {$product->name} thành " . number_format($newPrice) . "đ."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tool: Adjust variant price
     */
    protected function toolAdjustVariantPrice($args)
    {
        $variantId = $args['variant_id'] ?? null;
        $newPrice = (float)($args['new_price'] ?? 0);

        if (!$variantId) {
            return ['success' => false, 'error' => 'Thiếu ID biến thể.'];
        }

        if ($newPrice < 0) {
            return ['success' => false, 'error' => 'Mức giá biến thể mới không được nhỏ hơn 0đ.'];
        }

        try {
            $variant = DB::table('product_variants')->where('variant_id', $variantId)->first();
            if (!$variant) {
                return ['success' => false, 'error' => 'Không tìm thấy biến thể sản phẩm.'];
            }

            DB::table('product_variants')->where('variant_id', $variantId)->update([
                'price' => $newPrice,
                'updated_at' => now()
            ]);

            $productName = DB::table('products')->where('product_id', $variant->product_id)->value('name');
            $sizeName = DB::table('sizes')->where('size_id', $variant->size_id)->value('name');

            return [
                'success' => true,
                'product_name' => $productName,
                'size' => $sizeName,
                'new_price' => $newPrice,
                'message' => "Cập nhật giá biến thể {$productName} (size {$sizeName}) thành " . number_format($newPrice) . "đ."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tool: Discount all products in category
     */
    protected function toolDiscountCategoryProducts($args)
    {
        $categoryId = $args['category_id'] ?? null;
        $categoryName = $args['category_name'] ?? null;
        $discountType = $args['discount_type'] ?? 'percent';
        $discountValue = (float)($args['discount_value'] ?? 0);

        if ($discountValue < 0) {
            return ['success' => false, 'error' => 'Giá trị chiết khấu không được nhỏ hơn 0.'];
        }

        try {
            $category = null;
            if ($categoryId) {
                $category = DB::table('categories')->where('category_id', $categoryId)->first();
            } elseif ($categoryName) {
                $category = DB::table('categories')->where('name', 'like', '%' . trim($categoryName) . '%')->first();
            }

            if (!$category) {
                return ['success' => false, 'error' => 'Không tìm thấy danh mục yêu cầu.'];
            }

            $catId = $category->category_id;
            $products = DB::table('products')->where('category_id', $catId)->get();

            if ($products->isEmpty()) {
                return [
                    'success' => true,
                    'category_name' => $category->name,
                    'updated_products' => 0,
                    'updated_variants' => 0,
                    'message' => "Danh mục '{$category->name}' hiện không có sản phẩm nào để áp dụng giảm giá."
                ];
            }

            $updatedProductsCount = 0;
            $updatedVariantsCount = 0;

            foreach ($products as $product) {
                // 1. Cập nhật giá cơ bản
                $newBasePrice = $product->price;
                if ($discountType === 'percent') {
                    $newBasePrice = max(0, $product->price * (1 - $discountValue / 100));
                } else {
                    $newBasePrice = max(0, $product->price - $discountValue);
                }

                DB::table('products')->where('product_id', $product->product_id)->update([
                    'price' => $newBasePrice,
                    'updated_at' => now()
                ]);
                $updatedProductsCount++;

                // 2. Cập nhật giá các biến thể
                $variants = DB::table('product_variants')->where('product_id', $product->product_id)->get();
                foreach ($variants as $variant) {
                    $newVariantPrice = $variant->price;
                    if ($discountType === 'percent') {
                        $newVariantPrice = max(0, $variant->price * (1 - $discountValue / 100));
                    } else {
                        $newVariantPrice = max(0, $variant->price - $discountValue);
                    }

                    DB::table('product_variants')->where('variant_id', $variant->variant_id)->update([
                        'price' => $newVariantPrice,
                        'updated_at' => now()
                    ]);
                    $updatedVariantsCount++;
                }
            }

            return [
                'success' => true,
                'category_name' => $category->name,
                'updated_products' => $updatedProductsCount,
                'updated_variants' => $updatedVariantsCount,
                'message' => "Đã giảm giá thành công toàn bộ sản phẩm danh mục {$category->name}."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tool: Set all products in category to a fixed price
     */
    protected function toolSetCategoryPrice($args)
    {
        $categoryId = $args['category_id'] ?? null;
        $categoryName = $args['category_name'] ?? null;
        $newPrice = (float)($args['new_price'] ?? 0);

        if ($newPrice < 0) {
            return ['success' => false, 'error' => 'Mức giá mới không được nhỏ hơn 0đ.'];
        }

        try {
            $category = null;
            if ($categoryId) {
                $category = DB::table('categories')->where('category_id', $categoryId)->first();
            } elseif ($categoryName) {
                $category = DB::table('categories')->where('name', 'like', '%' . trim($categoryName) . '%')->first();
            }

            if (!$category) {
                return ['success' => false, 'error' => 'Không tìm thấy danh mục yêu cầu.'];
            }

            $catId = $category->category_id;
            $products = DB::table('products')->where('category_id', $catId)->get();

            if ($products->isEmpty()) {
                return [
                    'success' => true,
                    'category_name' => $category->name,
                    'updated_products' => 0,
                    'updated_variants' => 0,
                    'message' => "Danh mục '{$category->name}' hiện không có sản phẩm nào để thay đổi giá."
                ];
            }

            $updatedProductsCount = 0;
            $updatedVariantsCount = 0;

            foreach ($products as $product) {
                // 1. Cập nhật giá cơ bản của sản phẩm
                DB::table('products')->where('product_id', $product->product_id)->update([
                    'price' => $newPrice,
                    'updated_at' => now()
                ]);
                $updatedProductsCount++;

                // 2. Cập nhật giá các biến thể
                DB::table('product_variants')->where('product_id', $product->product_id)->update([
                    'price' => $newPrice,
                    'updated_at' => now()
                ]);
                $updatedVariantsCount += DB::table('product_variants')->where('product_id', $product->product_id)->count();
            }

            return [
                'success' => true,
                'category_name' => $category->name,
                'new_price' => $newPrice,
                'updated_products' => $updatedProductsCount,
                'updated_variants' => $updatedVariantsCount,
                'message' => "Đã điều chỉnh giá thành công toàn bộ sản phẩm thuộc danh mục {$category->name} thành " . number_format($newPrice) . "đ."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tra cứu thông minh trực tiếp từ Database khi không có Gemini API key hoặc khi API gián đoạn
     */
    protected function getSmartFallbackResponse($userMessage)
    {
        $msgRaw = $userMessage ?? '';
        $msgLower = mb_strtolower(trim($msgRaw));

        if (empty($msgLower)) {
            return "Dạ, Chill Chill chào bạn! Bạn cần tư vấn món nước hay bánh ngọt gì cứ nhắn Chill Chill nhé! ☕🍰";
        }

        // Lấy danh sách sản phẩm active từ Cache / DB (sắp xếp theo độ dài tên giảm dần)
        $allProducts = Cache::remember('active_products_for_bot', 600, function () {
            return DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->where('products.status', 1)
                ->select('products.*', 'categories.name as category_name')
                ->get()
                ->sortByDesc(function($p) {
                    return mb_strlen($p->name);
                });
        });

        // =========================================================================
        // CASE 1: HỎI CHIA KHẨU PHẦN THEO SỐ TIỀN VÀ ĐỒ UỐNG, BÁNH NGỌT (COMBO NGÂN SÁCH)
        // =========================================================================
        $isBudgetQuery = false;
        $budget = 0;
        $peopleCount = 2; // Mặc định 2 người

        // Bắt số tiền (ví dụ: 100k, 200k, 150.000, 50k, 80k)
        if (preg_match('/(\d+)\s*(k|000|nghìn|đ|d)/iu', $msgLower, $bMatch)) {
            $val = (int)$bMatch[1];
            $unit = mb_strtolower($bMatch[2]);
            if ($unit === 'k' || $unit === 'nghìn') {
                $budget = $val * 1000;
            } elseif ($unit === '000' || $unit === 'đ' || $unit === 'd') {
                $budget = ($val < 1000) ? $val * 1000 : $val;
            }
            $isBudgetQuery = true;
        } elseif (preg_match('/(\d{2,3})\s*(triệu)/iu', $msgLower, $bMatch)) {
            $budget = (int)$bMatch[1] * 1000000;
            $isBudgetQuery = true;
        }

        // Bắt số người (ví dụ: 2 người, 4 bạn, 3 khách)
        if (preg_match('/(\d+)\s*(người|bạn|khách|suất|khẩu\s*phần|nguoi)/iu', $msgLower, $pMatch)) {
            $peopleCount = max(1, (int)$pMatch[1]);
            $isBudgetQuery = true;
        }

        if (str_contains($msgLower, 'ngân sách') || str_contains($msgLower, 'combo') || str_contains($msgLower, 'chia khẩu phần') || str_contains($msgLower, 'bao nhiêu tiền')) {
            $isBudgetQuery = true;
            if ($budget === 0) $budget = 100000;
        }

        if ($isBudgetQuery && $budget > 0) {
            $drinkProducts = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->whereIn('categories.name', ['Cà phê Phin', 'Trà Trái Cây', 'Đá Xay'])
                ->select('products.product_id', 'products.name', 'categories.name as cat_name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as price'))
                ->groupBy('products.product_id', 'products.name', 'categories.name')
                ->orderBy('price', 'asc')
                ->get();

            $cakeProducts = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->where('categories.name', 'LIKE', '%bánh%')
                ->select('products.product_id', 'products.name', 'categories.name as cat_name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as price'))
                ->groupBy('products.product_id', 'products.name', 'categories.name')
                ->orderBy('price', 'asc')
                ->get();

            $comboItems = [];
            $currentTotal = 0;

            for ($i = 0; $i < $peopleCount; $i++) {
                $chosenDrink = $drinkProducts->get($i % $drinkProducts->count());
                if ($chosenDrink && ($currentTotal + $chosenDrink->price) <= $budget) {
                    $comboItems[] = $chosenDrink;
                    $currentTotal += $chosenDrink->price;
                }
            }

            if ($cakeProducts->isNotEmpty() && ($budget - $currentTotal) >= 30000) {
                foreach ($cakeProducts as $cake) {
                    if (($currentTotal + $cake->price) <= $budget) {
                        $comboItems[] = $cake;
                        $currentTotal += $cake->price;
                        break;
                    }
                }
            }

            $budgetText = number_format($budget) . 'đ';
            $totalText = number_format($currentTotal) . 'đ';

            if (!empty($comboItems)) {
                $reply = "Dạ, với ngân sách khoảng **{$budgetText}** cho **{$peopleCount} người**, Chill Chill xin gợi ý Combo vừa vặn siêu hời cho bạn nè: ☕🍰🍹\n\n";
                $reply .= "📌 **Chi tiết Combo đề xuất:**\n";
                
                foreach ($comboItems as $item) {
                    $itemPrice = number_format($item->price) . 'đ';
                    $reply .= "- 1x **{$item->name}** ({$item->cat_name}): {$itemPrice} [🛒 Thêm](action:add_to_cart?product_id={$item->product_id}&variant_id={$item->variant_id})\n";
                }

                $reply .= "\n💰 **Tổng tiền Combo**: **{$totalText}** (Nằm trong ngân sách {$budgetText} của bạn!)\n\n";
                $reply .= "👉 Bạn nhấn nút **🛒 Thêm** từng món ở trên hoặc nhắn Chill Chill để chốt đơn ngay nhé!";
                return $reply;
            } else {
                return "Dạ, với ngân sách **{$budgetText}**, bạn có thể chọn ngay 1 ly nước thơm ngon từ menu của quán nè! ☕ Bạn xem qua danh mục Cà phê hoặc Trà trái cây của Chill Chill nhé!";
            }
        }

        // =========================================================================
        // CASE 2: NHẬN BIẾT SẢN PHẨM CỤ THỂ KHÁCH HÀNG ĐANG NÓI TỚI (Ví dụ: món 20, chill chill 12)
        // =========================================================================
        $matchedProduct = null;
        preg_match_all('/\b(\d{1,2})\b(?!\s*(?:k|000|đ|d|nghìn|triệu|người|bạn|khách|suất))/iu', $msgLower, $matches);
        $foundNumbers = $matches[1] ?? [];

        foreach ($allProducts as $p) {
            $pNameLower = mb_strtolower($p->name);
            
            // Check match tên đầy đủ
            if (str_contains($msgLower, $pNameLower)) {
                $matchedProduct = $p;
                break;
            }

            // Check match số (Ví dụ: "20" khớp với "Món Ngon Chill Chill 20")
            foreach ($foundNumbers as $num) {
                if (preg_match('/\b' . preg_quote($num, '/') . '\b/u', $pNameLower)) {
                    $matchedProduct = $p;
                    break 2;
                }
            }

            // Check match tên rút gọn
            $shortName = str_replace('món ngon ', '', $pNameLower);
            if (mb_strlen($shortName) > 3 && preg_match('/\b' . preg_quote($shortName, '/') . '\b/u', $msgLower)) {
                $matchedProduct = $p;
                break;
            }
        }

        if ($matchedProduct) {
            $variants = DB::table('product_variants')
                ->join('sizes', 'product_variants.size_id', '=', 'sizes.size_id')
                ->where('product_variants.product_id', $matchedProduct->product_id)
                ->select('product_variants.*', 'sizes.name as size_name')
                ->orderBy('product_variants.price', 'asc')
                ->get();

            $variant = $variants->first();
            $sizeName = $variant ? $variant->size_name : 'Mặc định';
            $price = $variant ? $variant->price : $matchedProduct->price;
            $variantId = $variant ? $variant->variant_id : null;

            $qty = 1;
            if (preg_match('/(\d+)\s*(ly|cái|món|phần|cốc|x)/iu', $msgLower, $qMatch)) {
                $qty = (int)$qMatch[1];
            } elseif (preg_match('/(x|số lượng)\s*(\d+)/iu', $msgLower, $qMatch)) {
                $qty = (int)$qMatch[2];
            }

            $isOrderIntent = false;
            $isInquiry = false;
            $inquiryKeywords = ['xem', 'thông tin', 'hỏi', 'là gì', 'giá bao nhiêu', 'mô tả', 'chi tiết'];
            foreach ($inquiryKeywords as $ikw) {
                if (str_contains($msgLower, $ikw)) {
                    $isInquiry = true;
                    break;
                }
            }

            if (!$isInquiry) {
                $orderKeywords = ['lên', 'lấy', 'cho', 'đặt', 'thêm', 'mua', 'chốt', 'giao', 'order', 'tải', 'bán'];
                foreach ($orderKeywords as $kw) {
                    if (str_contains($msgLower, $kw)) {
                        $isOrderIntent = true;
                        break;
                    }
                }
            }

            if ($isOrderIntent) {
                $this->toolAddToCart([
                    [
                        'product_name' => $matchedProduct->name,
                        'size' => $sizeName,
                        'quantity' => $qty
                    ]
                ]);

                $priceFormatted = number_format($price) . 'đ';
                return "Dạ, Chill Chill đã thêm **{$qty}x {$matchedProduct->name}** (Size {$sizeName}, Giá: {$priceFormatted}) vào giỏ hàng của bạn rồi ạ! ☕✨\n\n" .
                       "[🛒 Thêm vào giỏ hàng nữa](action:add_to_cart?product_id={$matchedProduct->product_id}&variant_id={$variantId}&qty=1) | [🛍️ Xem giỏ hàng](/cart) | [💳 Thanh toán ngay](/checkout)\n\n" .
                       "👉 Bạn muốn chọn hình thức **Dùng tại quán (cho Chill Chill xin số bàn)** hay **Giao hàng tận nơi** ạ?";
            } else {
                $priceFormatted = number_format($price) . 'đ';
                $pricesList = [];
                foreach ($variants as $v) {
                    $pricesList[] = "Size {$v->size_name}: " . number_format($v->price) . "đ";
                }
                $pricesText = implode(', ', $pricesList);

                return "Dạ, Chill Chill tìm thấy thông tin món **{$matchedProduct->name}** bạn quan tâm nè! ☕\n\n" .
                       "📌 **{$matchedProduct->name}** ({$matchedProduct->category_name})\n" .
                       "- 📝 **Mô tả**: " . ($matchedProduct->description ?: 'Món ngon đậm vị của quán.') . "\n" .
                       "- 💰 **Giá bán**: {$pricesText}\n\n" .
                       "👉 Bạn muốn thưởng thức món này? Nhấn nút dưới đây để thêm ngay vào giỏ hàng nhé!\n\n" .
                       "[🛒 Thêm vào giỏ hàng](action:add_to_cart?product_id={$matchedProduct->product_id}&variant_id={$variantId}&qty={$qty})";
            }
        }

        // =========================================================================
        // CASE 3: HỎI MÓN NGON BÁN CHẠY NHẤT (BEST SELLERS / HOT ITEMS)
        // =========================================================================
        if (str_contains($msgLower, 'bán chạy') || str_contains($msgLower, 'best seller') || str_contains($msgLower, 'bestseller') || str_contains($msgLower, 'ngon nhất') || str_contains($msgLower, 'hot nhất') || str_contains($msgLower, 'đặc sản') || str_contains($msgLower, 'món tủ') || str_contains($msgLower, 'nhiều người mua') || str_contains($msgLower, 'ưa chuộng') || str_contains($msgLower, 'gợi ý món')) {
            $bestSellers = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->select('products.product_id', 'products.name', 'products.description', 'categories.name as cat_name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.product_id', 'products.name', 'products.description', 'categories.name')
                ->limit(5)
                ->get();

            if ($bestSellers->isNotEmpty()) {
                $reply = "Dạ, đây là Top **Món Bán Chạy Nhất (Best Sellers)** cực hot được đông đảo khách hàng mê mẩn tại Chill Chill nè! 🏆✨\n\n📌 **Danh sách Best Sellers:**\n";
                foreach ($bestSellers as $idx => $p) {
                    $priceText = number_format($p->min_price) . 'đ';
                    $icon = ($p->cat_name === 'Bánh Ngọt') ? '🍰' : (($p->cat_name === 'Cà phê Phin') ? '☕' : '🍹');
                    $reply .= ($idx + 1) . ". {$icon} **{$p->name}** ({$p->cat_name}) - Giá từ {$priceText}\n   [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
                }
                $reply .= "\n👉 Bạn thích món nào cứ nhấn **🛒 Thêm vào giỏ** để thưởng thức ngay nhé!";
                return $reply;
            }
        }

        // =========================================================================
        // CASE 4: HỎI THEO KHẨU VỊ / SỞ THÍCH ĐẶC BIỆT (THANH MÁT / ĐẬM VỊ)
        // =========================================================================
        if (str_contains($msgLower, 'thanh mát') || str_contains($msgLower, 'giải nhiệt') || str_contains($msgLower, 'mát lạnh') || str_contains($msgLower, 'ít ngọt')) {
            $coolProducts = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->whereIn('categories.name', ['Trà Trái Cây', 'Đá Xay'])
                ->select('products.product_id', 'products.name', 'categories.name as cat_name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.product_id', 'products.name', 'categories.name')
                ->limit(4)
                ->get();

            if ($coolProducts->isNotEmpty()) {
                $reply = "Dạ, nếu bạn thích đồ uống **Thanh mát & Giải nhiệt**, Chill Chill xin gợi ý các món cực mát sảng khoái này nhé: 🍹🧊\n\n📌 **Đồ uống thanh mát giải nhiệt:**\n";
                foreach ($coolProducts as $p) {
                    $priceText = number_format($p->min_price) . 'đ';
                    $reply .= "- **{$p->name}** ({$p->cat_name}): {$priceText} [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
                }
                $reply .= "\n👉 Nhấn **🛒 Thêm vào giỏ** để giải nhiệt ngay thôi bạn ơi!";
                return $reply;
            }
        }

        if (str_contains($msgLower, 'đậm vị') || str_contains($msgLower, 'tỉnh táo') || str_contains($msgLower, 'tinh thần')) {
            $coffeeProducts = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.category_id')
                ->join('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->where('categories.name', 'LIKE', '%cà phê%')
                ->select('products.product_id', 'products.name', 'categories.name as cat_name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.product_id', 'products.name', 'categories.name')
                ->limit(4)
                ->get();

            if ($coffeeProducts->isNotEmpty()) {
                $reply = "Dạ, nếu bạn cần đồ uống **Đậm vị & Tỉnh táo** cho ngày làm việc năng lượng, các món Cà phê Phin nguyên chất của quán chắc chắn là lựa chọn số 1 nè: ☕⚡\n\n📌 **Cà phê đậm vị tỉnh táo:**\n";
                foreach ($coffeeProducts as $p) {
                    $priceText = number_format($p->min_price) . 'đ';
                    $reply .= "- **{$p->name}**: {$priceText} [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
                }
                $reply .= "\n👉 Thêm ngay 1 ly Cà phê đậm đà để nạp năng lượng ngay bạn nhé!";
                return $reply;
            }
        }

        // =========================================================================
        // CASE 5: HỎI MÓN THEO DANH MỤC (CÀ PHÊ / TRÀ TRÁI CÂY / ĐÁ XAY / BÁNH NGỌT / TOPPING)
        // =========================================================================
        // 5.1 Cà phê
        if (str_contains($msgLower, 'cà phê') || str_contains($msgLower, 'cafe') || str_contains($msgLower, 'coffee') || str_contains($msgLower, 'phin') || str_contains($msgLower, 'bạc xỉu') || str_contains($msgLower, 'espresso')) {
            $catProducts = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->where(function($q) {
                    $q->where('products.name', 'LIKE', '%cà phê%')
                      ->orWhere('categories.name', 'LIKE', '%cà phê%')
                      ->orWhere('categories.name', 'LIKE', '%phin%');
                })
                ->select('products.product_id', 'products.name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.product_id', 'products.name')
                ->limit(6)
                ->get();

            if ($catProducts->count() > 0) {
                $reply = "Dạ, Chill Chill có các món **Cà phê** đậm vị thơm ngon lắm nè bạn! ☕\n\n📌 **Danh sách món Cà phê:**\n";
                foreach ($catProducts as $p) {
                    $priceText = $p->min_price ? number_format($p->min_price, 0, ',', '.') . 'đ' : '35.000đ';
                    $reply .= "- **{$p->name}**: {$priceText} [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
                }
                $reply .= "\n👉 Bạn chọn món rồi nhấn nút **🛒 Thêm vào giỏ** hoặc nhắn tên món cho Chill Chill nhé!";
                return $reply;
            }
        }

        // 5.2 Trà trái cây / Đồ uống / Đá xay
        if (str_contains($msgLower, 'trà') || str_contains($msgLower, 'tea') || str_contains($msgLower, 'nước') || str_contains($msgLower, 'đá xay') || str_contains($msgLower, 'uống')) {
            $catProducts = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->where(function($q) {
                    $q->where('products.name', 'LIKE', '%trà%')
                      ->orWhere('categories.name', 'LIKE', '%trà%')
                      ->orWhere('categories.name', 'LIKE', '%đá xay%');
                })
                ->select('products.product_id', 'products.name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.product_id', 'products.name')
                ->limit(6)
                ->get();

            if ($catProducts->count() > 0) {
                $reply = "Dạ, Chill Chill có menu **Trà Trái Cây & Đá Xay** cực mát lạnh giải nhiệt nè! 🍹\n\n📌 **Các món hot hôm nay:**\n";
                foreach ($catProducts as $p) {
                    $priceText = $p->min_price ? number_format($p->min_price, 0, ',', '.') . 'đ' : '35.000đ';
                    $reply .= "- **{$p->name}**: {$priceText} [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
                }
                $reply .= "\n👉 Bạn nhắn tên món hoặc bấm **🛒 Thêm vào giỏ** để chọn món ngay nha!";
                return $reply;
            }
        }

        // 5.3 Bánh ngọt
        if (str_contains($msgLower, 'bánh') || str_contains($msgLower, 'cake') || str_contains($msgLower, 'ăn')) {
            $catProducts = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
                ->where('products.status', 1)
                ->where(function($q) {
                    $q->where('products.name', 'LIKE', '%bánh%')
                      ->orWhere('categories.name', 'LIKE', '%bánh%');
                })
                ->select('products.product_id', 'products.name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.product_id', 'products.name')
                ->limit(6)
                ->get();

            if ($catProducts->count() > 0) {
                $reply = "Dạ, quán có sẵn các loại **Bánh Ngọt** thơm ngon giao kèm nước nè! 🍰\n\n📌 **Menu Bánh Ngọt:**\n";
                foreach ($catProducts as $p) {
                    $priceText = $p->min_price ? number_format($p->min_price, 0, ',', '.') . 'đ' : '35.000đ';
                    $reply .= "- **{$p->name}**: {$priceText} [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
                }
                $reply .= "\n👉 Thêm bánh ngọt vào giỏ hàng thôi bạn ơi!";
                return $reply;
            }
        }

        // 6. TRA CỨU MENU TỔNG QUÁT
        if (str_contains($msgLower, 'menu') || str_contains($msgLower, 'thực đơn') || str_contains($msgLower, 'danh sách')) {
            $cats = DB::table('categories')->pluck('name')->toArray();
            $catsList = !empty($cats) ? implode(', ', $cats) : 'Cà phê Phin, Trà Trái Cây, Đá Xay, Bánh Ngọt';
            return "Dạ, Chill Chill xin gửi bạn thông tin Menu nhé! ☕🍹🍰\n\nQuán có các nhóm danh mục: **{$catsList}**.\n\n👉 Bạn nhắn tên nhóm món (Cà phê, Trà trái cây, Bánh ngọt) hoặc tên/số của món (ví dụ: 'Chill Chill 20') để Chill Chill hỗ trợ lên đơn ngay nhé!";
        }

        // 7. GIAO HÀNG / MỞ CỬA
        if (str_contains($msgLower, 'giao hàng') || str_contains($msgLower, 'ship') || str_contains($msgLower, 'địa chỉ') || str_contains($msgLower, 'mở cửa') || str_contains($msgLower, 'ở đâu')) {
            return "Dạ, Chill Chill Coffee & Tea mở cửa từ **07:00 đến 22:30** hàng ngày! 🛵\n\n📍 **Giao hàng tận nơi:** Quán nhận giao hàng tận nơi nhanh chóng. Bạn chọn món rồi nhắn Chill Chill hoặc bấm **🛒 Thêm vào giỏ** để chốt đơn ngay nhé!";
        }

        // MẶC ĐỊNH
        $sampleProducts = DB::table('products')
            ->leftJoin('product_variants', 'products.product_id', '=', 'product_variants.product_id')
            ->where('products.status', 1)
            ->select('products.product_id', 'products.name', DB::raw('MIN(product_variants.variant_id) as variant_id'), DB::raw('MIN(product_variants.price) as min_price'))
            ->groupBy('products.product_id', 'products.name')
            ->limit(4)
            ->get();

        $reply = "Dạ, Chill Chill chào bạn! ☕ Chill Chill có các món ngon siêu hấp dẫn nè:\n\n";
        foreach ($sampleProducts as $p) {
            $priceText = $p->min_price ? number_format($p->min_price, 0, ',', '.') . 'đ' : '35.000đ';
            $reply .= "- **{$p->name}**: {$priceText} [🛒 Thêm vào giỏ](action:add_to_cart?product_id={$p->product_id}&variant_id={$p->variant_id})\n";
        }
        $reply .= "\n👉 Bạn muốn chọn món nào nhắn tên/số món (Ví dụ: 'Món 20') cho Chill Chill nhé!";
        return $reply;
    }
}


