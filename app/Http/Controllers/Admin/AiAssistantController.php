<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiService;

class AiAssistantController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Display the Admin AI Assistant Page
     */
    public function index()
    {
        // Lấy lịch sử chat từ Session (nếu có)
        $history = session()->get('admin_ai_chat_history', []);
        
        return view('admin.ai.index', compact('history'));
    }

    /**
     * Handle incoming messages from Admin and generate AI reply
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->input('message');

        // 1. Lấy lịch sử chat từ Session
        $history = session()->get('admin_ai_chat_history', []);

        // 2. Thêm tin nhắn mới của Admin vào lịch sử (sender_type = 'customer' để Gemini map sang 'user')
        $history[] = [
            'sender_type' => 'customer',
            'message' => $userMessage,
            'created_at' => now()->format('H:i')
        ];

        // 3. Biến đổi lịch sử thành Collection chứa các object để truyền vào GeminiService
        $chatMessagesCollection = collect($history)->map(function ($item) {
            return (object) [
                'sender_type' => $item['sender_type'],
                'message' => $item['message']
            ];
        });

        // 4. Gọi Gemini Service
        $aiResponse = $this->geminiService->getAdminAiResponse($chatMessagesCollection);

        // 5. Thêm tin nhắn của AI vào lịch sử (sender_type = 'admin' để Gemini map sang 'model')
        $history[] = [
            'sender_type' => 'admin',
            'message' => $aiResponse,
            'created_at' => now()->format('H:i')
        ];

        // 6. Lưu lại lịch sử chat mới vào Session
        session()->put('admin_ai_chat_history', $history);

        return response()->json([
            'success' => true,
            'reply' => $aiResponse,
            'time' => now()->format('H:i')
        ]);
    }

    /**
     * Clear the chat history
     */
    public function clearHistory()
    {
        session()->forget('admin_ai_chat_history');
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa lịch sử trò chuyện.'
        ]);
    }
}
