<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    const MAIN_ADMIN_ID = 1;

    // 1. Danh sách người dùng (Hỗ trợ tìm kiếm theo tên, sđt, email)
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('email', 'like', "%{$kw}%")
                  ->orWhere('phone', 'like', "%{$kw}%");
            });
        }

        $users = $query->orderBy('user_id', 'asc')->get();
        $mainAdminId = self::MAIN_ADMIN_ID;
        return view('admin.users.index', compact('users', 'mainAdminId'));
    }

    // 2. Cập nhật phân quyền (Role)
    public function updateRole(Request $request, $id)
    {
        if ((int)$id === self::MAIN_ADMIN_ID) {
            return back()->with('error', 'Không thể thay đổi vai trò của tài khoản này!');
        }

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Đã cập nhật phân quyền cho tài khoản ' . $user->name . ' thành công!');
    }

    // 3. Khóa hoặc Mở khóa tài khoản
    public function toggleLock($id)
    {
        if ((int)$id === self::MAIN_ADMIN_ID) {
            return back()->with('error', 'Không thể khóa tài khoản này!');
        }

        $user = User::findOrFail($id);
        $user->is_locked = empty($user->is_locked) ? 1 : 0;
        $user->save();

        $actionText = $user->is_locked ? 'khóa' : 'mở khóa';
        return back()->with('success', 'Đã ' . $actionText . ' tài khoản ' . $user->name . ' thành công!');
    }
}