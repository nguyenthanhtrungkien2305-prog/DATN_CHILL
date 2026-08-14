<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Start a new chat session for guest or logged-in user.
     */
    public function startSession(Request $request)
    {
        $sessionToken = $request->input('session_token');

        if (!$sessionToken) {
            $sessionToken = Str::random(40);
        }

        $session = ChatSession::where('session_token', $sessionToken)->first();

        if (!$session) {
            $session = ChatSession::create([
                'session_token' => $sessionToken,
                'user_id' => Auth::check() ? Auth::user()->user_id : null,
                'status' => 'active',
                'is_bot_enabled' => true,
            ]);
        } else {
            // Cập nhật user_id nếu trước đó là khách vãng lai nay đã đăng nhập
            if (Auth::check() && !$session->user_id) {
                $session->user_id = Auth::user()->user_id;
                $session->save();
            }
        }

        return response()->json([
            'success' => true,
            'session_token' => $sessionToken,
            'messages' => $session->messages()->get()
        ]);
    }

    /**
     * Get all messages for a session.
     */
    public function getMessages(Request $request)
    {
        $sessionToken = $request->input('session_token');
        if (!$sessionToken) {
            return response()->json(['success' => false, 'message' => 'Missing session token.'], 400);
        }

        $session = ChatSession::where('session_token', $sessionToken)->first();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Session not found.'], 404);
        }

        // Đánh dấu tin nhắn của admin gửi là ĐÃ ĐỌC
        $session->messages()
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $session->messages()->get()->map(function($msg) {
            return [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'message' => $msg->message,
                'created_at' => $msg->created_at->format('H:i'),
            ];
        });

        // Đọc các flag thay đổi từ bot trong session
        $cartUpdated = session()->pull('cart_updated_by_bot', false);
        $orderCreated = session()->pull('order_created_by_bot', null);
        $paymentMethod = session()->pull('order_payment_method_by_bot', null);

        // Đếm lại số lượng sản phẩm trong giỏ hàng để cập nhật badge
        $cartCount = 0;
        if ($cartUpdated) {
            $cart = session()->get('cart', []);
            $cartCount = array_sum(array_column($cart, 'quantity'));
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'cart_updated' => $cartUpdated,
            'cart_count' => $cartCount,
            'order_created' => $orderCreated,
            'payment_method' => $paymentMethod
        ]);
    }

    /**
     * Send a message from client.
     */
    public function sendMessage(Request $request)
    {
        $sessionToken = $request->input('session_token');
        $messageText = $request->input('message');

        if (!$sessionToken || !$messageText) {
            return response()->json(['success' => false, 'message' => 'Data invalid.'], 400);
        }

        $session = ChatSession::where('session_token', $sessionToken)->first();
        if (!$session) {
            $session = ChatSession::create([
                'session_token' => $sessionToken,
                'user_id' => Auth::check() ? Auth::user()->user_id : null,
                'status' => 'active',
                'is_bot_enabled' => true,
            ]);
        }

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'customer',
            'message' => $messageText,
        ]);

        // Nếu bật chế độ Bot AI thì tự động sinh phản hồi
        if ($session->is_bot_enabled) {
            try {
                $geminiService = new \App\Services\GeminiService();
                $allMessages = $session->messages()->orderBy('created_at', 'asc')->get();
                $aiReply = $geminiService->getAiResponse($allMessages);

                ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_type' => 'admin',
                    'message' => $aiReply,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error generating AI reply: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_type' => 'customer',
                'message' => $message->message,
                'created_at' => $message->created_at->format('H:i'),
            ]
        ]);
    }

    /**
     * Nút thêm nhanh vào giỏ hàng từ cửa sổ Chatbot UI.
     */
    public function addToCartAction(Request $request)
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('variant_id');
        $quantity = (int)($request->input('quantity', 1));
        if ($quantity < 1) $quantity = 1;

        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ.'], 400);
        }

        $product = \Illuminate\Support\Facades\DB::table('products')->where('product_id', $productId)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm.'], 404);
        }

        $variant = null;
        if ($variantId) {
            $variant = \Illuminate\Support\Facades\DB::table('product_variants')->where('variant_id', $variantId)->first();
        }
        if (!$variant) {
            $variant = \Illuminate\Support\Facades\DB::table('product_variants')
                ->where('product_id', $product->product_id)
                ->orderBy('price', 'asc')
                ->first();
        }

        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kích cỡ sản phẩm.'], 400);
        }

        $sizeName = \Illuminate\Support\Facades\DB::table('sizes')->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định';

        $cart = \App\Http\Controllers\CartController::getCart();
        $cartKey = md5($product->product_id . '_' . $variant->variant_id . '_' . serialize([]));

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'image' => $product->image_url ?? $product->image ?? '',
                'variant_id' => $variant->variant_id,
                'size_name' => $sizeName,
                'price' => $variant->price,
                'quantity' => $quantity,
                'toppings' => [],
                'topping_total' => 0,
            ];
        }

        \App\Http\Controllers\CartController::saveCart($cart);
        session()->put('cart', $cart);
        \App\Http\Controllers\CartController::checkAndRecalculateVoucher();
        session()->put('cart_updated_by_bot', true);

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => "Đã thêm {$quantity}x {$product->name} (size {$sizeName}) vào giỏ hàng!",
            'product_name' => $product->name,
            'size_name' => $sizeName,
            'quantity' => $quantity,
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Thêm cả Combo (nhiều sản phẩm cùng lúc) vào giỏ hàng từ chatbot.
     * items = "product_id:variant_id,product_id:variant_id,..."
     */
    public function addComboAction(Request $request)
    {
        $itemsRaw = $request->input('items', '');
        if (!$itemsRaw) {
            return response()->json(['success' => false, 'message' => 'Không có sản phẩm trong combo.'], 400);
        }

        $pairs = array_filter(explode(',', $itemsRaw));
        if (empty($pairs)) {
            return response()->json(['success' => false, 'message' => 'Combo không hợp lệ.'], 400);
        }

        $cart = \App\Http\Controllers\CartController::getCart();
        $addedNames = [];

        foreach ($pairs as $pair) {
            [$productId, $variantId] = array_pad(explode(':', trim($pair), 2), 2, null);
            $productId = (int)$productId;
            $variantId = (int)$variantId;

            $product = \Illuminate\Support\Facades\DB::table('products')->where('product_id', $productId)->first();
            if (!$product) continue;

            $variant = $variantId
                ? \Illuminate\Support\Facades\DB::table('product_variants')->where('variant_id', $variantId)->first()
                : null;
            if (!$variant) {
                $variant = \Illuminate\Support\Facades\DB::table('product_variants')
                    ->where('product_id', $productId)
                    ->orderBy('price', 'asc')
                    ->first();
            }
            if (!$variant) continue;

            $sizeName = \Illuminate\Support\Facades\DB::table('sizes')
                ->where('size_id', $variant->size_id)->value('name') ?? 'Mặc định';

            $cartKey = md5($product->product_id . '_' . $variant->variant_id . '_' . serialize([]));
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += 1;
            } else {
                $cart[$cartKey] = [
                    'product_id'    => $product->product_id,
                    'name'          => $product->name,
                    'image'         => $product->image_url ?? $product->image ?? '',
                    'variant_id'    => $variant->variant_id,
                    'size_name'     => $sizeName,
                    'price'         => $variant->price,
                    'quantity'      => 1,
                    'toppings'      => [],
                    'topping_total' => 0,
                ];
            }
            $addedNames[] = $product->name;
        }

        \App\Http\Controllers\CartController::saveCart($cart);
        session()->put('cart', $cart);
        \App\Http\Controllers\CartController::checkAndRecalculateVoucher();
        session()->put('cart_updated_by_bot', true);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $summary   = implode(', ', $addedNames);

        return response()->json([
            'success'    => true,
            'message'    => "Đã thêm toàn bộ Combo ({$summary}) vào giỏ hàng!",
            'cart_count' => $cartCount,
        ]);
    }
}

