<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Đây là hàm mà route gọi đến, phải tồn tại thì mới không báo lỗi
    public function index()
    {
        $users = User::all(); // Hoặc User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return back()->with('success', 'Đã cập nhật quyền thành công!');
    }
}