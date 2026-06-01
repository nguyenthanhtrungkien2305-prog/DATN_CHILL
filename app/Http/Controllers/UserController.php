<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // 1. Hiển thị trang tài khoản
    public function profile()
    {
        // Lấy thông tin user đang đăng nhập
        $user = Auth::user(); 
        return view('user.profile', compact('user'));
    }

    // 2. Xử lý lưu thông tin và avatar
   public function updateProfile(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // File ảnh, tối đa 2MB
        ]);

        $user = auth()->user(); // Hoặc DB::table('users')->where('user_id', $id)->first() tùy logic của bạn
        
        // 2. Mảng dữ liệu cần update
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address, // Lưu địa chỉ
            'updated_at' => now(),
        ];

        // 3. XỬ LÝ LƯU ẢNH AVATAR VĨNH VIỄN
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Tạo tên file ngẫu nhiên để không bị trùng (vd: 1690000000_avatar.jpg)
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Di chuyển file ảnh vào thư mục public/uploads/avatars
            $file->move(public_path('uploads/avatars'), $filename);
            
            // Cập nhật đường dẫn vào mảng để lưu xuống Database
            $updateData['avatar'] = 'uploads/avatars/' . $filename;
        }

        // 4. Update vào Database
        \DB::table('users')->where('user_id', $user->user_id)->update($updateData);

        return back()->with('success', 'Đã cập nhật hồ sơ thành công!');
    }
    public function orders()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('cart.index')->with('login_required', 'Vui lòng đăng nhập để xem danh sách đơn hàng!');
        }
        
        // Lấy danh sách đơn hàng của người này, xếp mới nhất lên đầu
        $orders = \DB::table('orders')
            ->where('user_id', $user->user_id) // Thay user_id bằng tên cột ID khóa chính của bạn nếu khác
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.orders', compact('user', 'orders'));
    }   
}