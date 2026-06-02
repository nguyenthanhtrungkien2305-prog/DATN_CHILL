<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateStaffAccount extends Command
{
    // Tên lệnh bạn sẽ gõ ở Terminal
    protected $signature = 'account:make-staff';

    // Mô tả của lệnh
    protected $description = 'Tạo nhanh tài khoản nhân viên hệ thống quầy POS';

    public function handle()
    {
        $username = 'huyho';
        $email = 'huyho@diemcongcoffee.com'; // Đắp email mẫu để khớp cấu trúc Laravel Auth mặc định

        // Kiểm tra xem tên đăng nhập hoặc email này đã bị trùng trong DB chưa
        $checkExists = DB::table('users')->where('email', $email)->orWhere('name', $username)->exists();

        if ($checkExists) {
            $this->error(' Thất bại: Tài khoản hoặc Email này đã tồn tại trong hệ thống!');
            return Command::FAILURE;
        }

        // Tiến hành ghi trực tiếp vào Database thật
        DB::table('users')->insert([
            'name' => $username,           // Tên hiển thị khi đăng nhập
            'email' => $email,             // Tài khoản email dùng để login
            'password' => Hash::make('123456'), // Mã hóa mật khẩu bảo mật băm Hash
            'role' => 'staff',             // Phân quyền chuẩn vai trò nhân viên
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info('==================================================');
        $this->info('🎉 KHỞI TẠO TÀI KHOẢN STAFF THÀNH CÔNG!');
        $this->info('==================================================');
        $this->line('👉 Tài khoản (Email): ' . $email);
        $this->line('👉 Tên hiển thị (Name): ' . $username);
        $this->line('👉 Mật khẩu: 123456');
        $this->line('👉 Quyền hạn: staff');
        $this->info('==================================================');

        return Command::SUCCESS;
    }
}