<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Danh sách người dùng
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('user_id', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    // 2. Giao diện thêm mới người dùng
    public function create()
    {
        return view('admin.users.create');
    }

    // 3. Xử lý lưu người dùng mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff,user',
            'point' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên đăng nhập.',
            'name.unique' => 'Tên đăng nhập đã tồn tại trên hệ thống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng bởi tài khoản khác.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'role.required' => 'Vui lòng chọn vai trò.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'point' => $request->point ?? 0,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng mới thành công!');
    }

    // Cập nhật vai trò
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,staff,user'
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return back()->with('success', 'Đã cập nhật quyền cho ' . $user->name . ' thành công!');
    }

    // Xử lý khóa / mở khóa tài khoản
    public function toggleLock($id)
    {
        $currentUser = auth()->user();
        $currentUserId = $currentUser->user_id ?? $currentUser->id;

        if ($currentUserId == $id) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình!');
        }

        $user = User::findOrFail($id);
        $user->is_locked = !$user->is_locked;
        $user->save();

        $message = $user->is_locked ? 'Đã khóa tài khoản thành công!' : 'Đã mở khóa tài khoản thành công!';
        return back()->with('success', $message);
    }
}
