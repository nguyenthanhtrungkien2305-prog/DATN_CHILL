<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Mail\AdminFeedbackNotification;
use App\Mail\CustomerFeedbackConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|min:5',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'message.min' => 'Nội dung tin nhắn phải từ 5 ký tự trở lên.',
        ]);

        // Tạo bản ghi lưu vào CSDL
        $feedback = Feedback::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        // Gửi email thông báo cho Admin và khách hàng
        try {
            // Gửi tới email của admin cấu hình trong config mail.from.address hoặc admin@chillchill.vn
            $adminEmail = config('mail.from.address', 'admin@chillchill.vn');
            Mail::to($adminEmail)->send(new AdminFeedbackNotification($feedback));

            // Gửi xác nhận cho khách hàng
            Mail::to($feedback->email)->send(new CustomerFeedbackConfirmation($feedback));
        } catch (\Exception $e) {
            // Log lại lỗi nếu cấu hình mail chưa hoàn tất, tránh làm gián đoạn trải nghiệm người dùng
            Log::error('Lỗi gửi mail phản hồi: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Gửi tin nhắn thành công! Chill Chill sẽ liên hệ lại với bạn sớm.');
    }
}
