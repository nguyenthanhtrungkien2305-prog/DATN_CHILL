<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Mail\FeedbackReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the feedbacks.
     */
    public function index(Request $request)
    {
        $query = Feedback::query();

        // Lọc theo trạng thái
        if ($request->has('status') && in_array($request->status, ['unread', 'read', 'replied'])) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Đếm các trạng thái để hiển thị thống kê nhỏ
        $countUnread = Feedback::where('status', 'unread')->count();
        $countTotal = Feedback::count();

        return view('admin.feedbacks.index', compact('feedbacks', 'countUnread', 'countTotal'));
    }

    /**
     * Display the specified feedback and mark it as read.
     */
    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);

        // Đổi trạng thái sang 'read' nếu đang là 'unread'
        if ($feedback->status === 'unread') {
            $feedback->status = 'read';
            $feedback->save();
        }

        return view('admin.feedbacks.show', compact('feedback'));
    }

    /**
     * Send email reply to the customer and update status to 'replied'.
     */
    public function reply(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);

        $validated = $request->validate([
            'reply_content' => 'required|string|min:5',
        ], [
            'reply_content.required' => 'Vui lòng nhập nội dung câu trả lời.',
            'reply_content.min' => 'Nội dung câu trả lời phải từ 5 ký tự trở lên.',
        ]);

        $feedback->reply_content = $validated['reply_content'];
        $feedback->status = 'replied';
        $feedback->replied_by = Auth::user()->user_id; // Khớp với khóa chính user_id trong bảng users
        $feedback->replied_at = now();
        $feedback->save();

        // Gửi email trả lời khách hàng
        try {
            Mail::to($feedback->email)->send(new FeedbackReplyMail($feedback));
            $mailSuccess = true;
        } catch (\Exception $e) {
            Log::error('Lỗi gửi mail phản hồi từ Admin: ' . $e->getMessage());
            $mailSuccess = false;
        }

        if ($mailSuccess) {
            return redirect()->route('feedbacks.show', $id)
                ->with('success', 'Gửi phản hồi thành công và đã gửi email cho khách hàng!');
        } else {
            return redirect()->route('feedbacks.show', $id)
                ->with('success', 'Đã lưu phản hồi vào hệ thống nhưng không gửi được email (Vui lòng kiểm tra lại cấu hình SMTP).');
        }
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('feedbacks.index')
            ->with('success', 'Xóa phản hồi thành công!');
    }
}
