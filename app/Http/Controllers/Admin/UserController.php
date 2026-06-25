<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Xử lý chuyển đổi qua lại giữa Khóa và Mở khóa
    public function destroy($user_id)
    {
        // Tìm user theo khóa chính user_id
        $user = User::findOrFail($user_id);

        // Đảo ngược trạng thái
        if ($user->status == 1) {
            $user->status = 0;
            $msg = "Đã khóa tài khoản của " . $user->name . " thành công!";
        } else {
            $user->status = 1;
            $msg = "Đã kích hoạt lại (Mở khóa) tài khoản của " . $user->name . " thành công!";
        }

        // Lưu thay đổi vào database
        $user->save();

        // Quay lại trang danh sách và bắn thông báo thành công
        return redirect()->back()->with('success', $msg);
    }
}
