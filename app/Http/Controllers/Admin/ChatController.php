<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display the index view for Admin Live Chat.
     */
    public function index()
    {
        return view('admin.chats.index');
    }

    /**
     * Get a list of all chat sessions for sidebar.
     */
    public function getSessions()
    {
        $sessions = ChatSession::with(['user', 'messages'])
            ->whereHas('messages') // Chỉ lấy các phiên có tin nhắn
            ->get()
            ->map(function($session) {
                $lastMsgObj = $session->messages->last();
                $unreadCount = $session->messages
                    ->where('sender_type', 'customer')
                    ->whereNull('read_at')
                    ->count();

                $name = 'Khách vãng lai';
                if ($session->user) {
                    $name = $session->user->name;
                } else {
                    $name = 'Khách #' . substr($session->session_token, 0, 6);
                }

                return [
                    'id' => $session->id,
                    'name' => $name,
                    'session_token' => $session->session_token,
                    'last_message' => $lastMsgObj ? $lastMsgObj->message : 'Chưa có tin nhắn',
                    'last_message_time' => $lastMsgObj ? $lastMsgObj->created_at->format('H:i d/m') : '',
                    'unread_count' => $unreadCount,
                    'status' => $session->status,
                    'is_bot_enabled' => (bool) $session->is_bot_enabled,
                ];
            })
            ->sortByDesc(function($s) {
                // Sắp xếp theo phiên có tin nhắn mới nhất
                $session = ChatSession::find($s['id']);
                $lastMsg = $session->messages->last();
                return $lastMsg ? $lastMsg->created_at->timestamp : 0;
            })
            ->values();

        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }

    /**
     * Get messages for a specific session and mark customer messages as read.
     */
    public function getSessionMessages($id)
    {
        $session = ChatSession::findOrFail($id);

        // Đánh dấu đã đọc cho các tin nhắn từ khách hàng gửi
        $session->messages()
            ->where('sender_type', 'customer')
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

        $name = 'Khách vãng lai';
        if ($session->user) {
            $name = $session->user->name;
        } else {
            $name = 'Khách #' . substr($session->session_token, 0, 6);
        }

        return response()->json([
            'success' => true,
            'session_name' => $name,
            'is_bot_enabled' => (bool) $session->is_bot_enabled,
            'messages' => $messages
        ]);
    }

    /**
     * Send a reply from Admin.
     */
    public function sendReply(Request $request, $id)
    {
        $session = ChatSession::findOrFail($id);
        $messageText = $request->input('message');

        if (!$messageText) {
            return response()->json(['success' => false, 'message' => 'Nội dung tin nhắn trống.'], 400);
        }

        // Tự động tắt bot khi admin trả lời thủ công để tránh tranh chấp cuộc chat
        if ($session->is_bot_enabled) {
            $session->is_bot_enabled = false;
            $session->save();
        }

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'admin',
            'message' => $messageText,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_type' => 'admin',
                'message' => $message->message,
                'created_at' => $message->created_at->format('H:i'),
            ]
        ]);
    }

    /**
     * Get the bot enabled status for a session.
     */
    public function getBotStatus($id)
    {
        $session = ChatSession::findOrFail($id);
        return response()->json([
            'success' => true,
            'is_bot_enabled' => (bool) $session->is_bot_enabled,
        ]);
    }

    /**
     * Toggle the bot enabled status.
     */
    public function toggleBot($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->is_bot_enabled = !$session->is_bot_enabled;
        $session->save();

        return response()->json([
            'success' => true,
            'is_bot_enabled' => (bool) $session->is_bot_enabled,
        ]);
    }
}
