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

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Send a message from client.
     */
    public function sendMessage(Request $request)
    {
        $sessionToken = $request->input('session_token');
        $messageText = $request->input('message');

        if (!$messageText) {
            return response()->json(['success' => false, 'message' => 'Tin nhắn không được để trống.'], 400);
        }

        // Nếu chưa có sessionToken thì tự động tạo mới
        if (!$sessionToken) {
            $sessionToken = 'session_' . \Illuminate\Support\Str::random(32);
        }

        $session = ChatSession::where('session_token', $sessionToken)->first();
        if (!$session) {
            $session = ChatSession::create([
                'session_token' => $sessionToken,
                'user_id' => Auth::check() ? Auth::user()->user_id : null,
                'status' => 'active',
                'is_bot_enabled' => 1,
            ]);
        }

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'customer',
            'message' => $messageText,
        ]);

        // Nếu bật chế độ Bot AI thì tự động sinh phản hồi
        $aiReplyMsg = null;
        if ($session->is_bot_enabled != 0) {
            try {
                $geminiService = new \App\Services\GeminiService();
                $allMessages = ChatMessage::where('chat_session_id', $session->id)->orderBy('created_at', 'asc')->get();
                $aiReplyText = $geminiService->getAiResponse($allMessages);

                $replyObj = ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_type' => 'admin',
                    'message' => $aiReplyText,
                ]);

                $aiReplyMsg = [
                    'id' => $replyObj->id,
                    'sender_type' => 'admin',
                    'message' => $replyObj->message,
                    'created_at' => $replyObj->created_at->format('H:i'),
                ];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error generating AI reply: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'session_token' => $session->session_token,
            'message' => [
                'id' => $message->id,
                'sender_type' => 'customer',
                'message' => $message->message,
                'created_at' => $message->created_at->format('H:i'),
            ],
            'ai_reply' => $aiReplyMsg
        ]);
    }
}
