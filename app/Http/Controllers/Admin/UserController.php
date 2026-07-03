<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // 1. Danh sách người dùng
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $query = DB::table('users');

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

        $users = $query->orderBy('user_id', 'desc')->paginate(10);

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
            'point' => 'required|integer|min:0',
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
            'point.required' => 'Vui lòng nhập điểm số.',
            'point.integer' => 'Điểm số phải là một số nguyên.',
            'point.min' => 'Điểm số không được âm.',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'point' => $request->point,
            'address' => $request->address,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Thêm người dùng mới thành công!');
    }

    // 4. Giao diện chỉnh sửa người dùng (Đã vô hiệu hóa)
    public function edit($id)
    {
        return redirect()->route('users.index')->with('error', 'Chức năng chỉnh sửa tài khoản đã bị vô hiệu hóa.');
    }

    // 5. Xử lý cập nhật thông tin người dùng (Đã vô hiệu hóa)
    public function update(Request $request, $id)
    {
        return redirect()->route('users.index')->with('error', 'Chức năng chỉnh sửa tài khoản đã bị vô hiệu hóa.');
    }

    // 6. Xử lý xóa người dùng kèm các điều kiện ràng buộc an toàn dữ liệu
    public function destroy($id)
    {
        $currentUser = auth()->user();
        $currentUserId = $currentUser->user_id ?? $currentUser->id;

        if ($currentUserId == $id) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        // Ràng buộc đơn hàng
        $orderCount = DB::table('orders')->where('user_id', $id)->count();
        if ($orderCount > 0) {
            return back()->with('error', 'Không thể xóa! Người dùng này đã thực hiện ' . $orderCount . ' đơn hàng. Vui lòng giữ lại để lưu lịch sử giao dịch.');
        }

        // Ràng buộc đăng ký ca làm
        $shiftRegCount = DB::table('shift_registrations')->where('user_id', $id)->count();
        if ($shiftRegCount > 0) {
            return back()->with('error', 'Không thể xóa! Nhân viên này đã có ' . $shiftRegCount . ' đơn đăng ký ca làm việc.');
        }

        // Ràng buộc chấm công
        $attendanceCount = DB::table('attendances')->where('user_id', $id)->count();
        if ($attendanceCount > 0) {
            return back()->with('error', 'Không thể xóa! Nhân viên này đã có dữ liệu chấm công (' . $attendanceCount . ' lượt).');
        }

        DB::table('users')->where('user_id', $id)->delete();

        return redirect()->route('users.index')->with('success', 'Đã xóa người dùng thành công!');
    }

    // 7. Xử lý khóa / mở khóa tài khoản người dùng
    public function toggleLock($id)
    {
        $currentUser = auth()->user();
        $currentUserId = $currentUser->user_id ?? $currentUser->id;

        if ($currentUserId == $id) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình!');
        }

        $user = DB::table('users')->where('user_id', $id)->first();
        if (!$user) {
            return back()->with('error', 'Không tìm thấy người dùng này.');
        }

        $newLockStatus = $user->is_locked ? 0 : 1;
        DB::table('users')->where('user_id', $id)->update([
            'is_locked' => $newLockStatus,
            'updated_at' => now(),
        ]);

        $message = $newLockStatus ? 'Đã khóa tài khoản thành công!' : 'Đã mở khóa tài khoản thành công!';
        return back()->with('success', $message);
    }
}
