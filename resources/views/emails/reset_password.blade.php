<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu - Chill Chill Coffee</title>
</head>
<body style="background-color: #FAF7F2; font-family: Arial, sans-serif; margin: 0; padding: 20px;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid rgba(58,36,28,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #3A241C; font-size: 26px; margin: 0; font-family: Georgia, serif;">Chill Chill Coffee</h2>
            <p style="color: #ff7043; font-size: 13px; font-weight: bold; margin-top: 6px; text-transform: uppercase; tracking-wider: 1px;">Khôi Phục Mật Khẩu Tài Khoản</p>
        </div>

        <div style="color: #3A241C; font-size: 14px; line-height: 1.6; margin-bottom: 30px;">
            <p>Xin chào <strong>{{ $user->name ?? 'Khách hàng' }}</strong>,</p>
            <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với địa chỉ email <strong>{{ $user->email }}</strong>.</p>
            <p>Vui lòng bấm vào nút bên dưới để tiến hành thiết lập mật khẩu mới (Liên kết này có hiệu lực trong <strong>15 phút</strong>):</p>
        </div>

        <div style="text-align: center; margin: 35px 0;">
            <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" style="background-color: #ff7043; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 50px; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 4px 12px rgba(255,112,67,0.3);">
                Đặt Lại Mật Khẩu Ngay
            </a>
        </div>

        <div style="color: #777777; font-size: 12px; line-height: 1.5; border-top: 1px solid #eeeeee; margin-top: 30px; padding-top: 20px;">
            <p>Nếu nút trên không hoạt động, bạn có thể copy và dán liên kết sau vào trình duyệt:</p>
            <p style="word-break: break-all; color: #ff7043;">
                <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" style="color: #ff7043;">
                    {{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}
                </a>
            </p>
            <p style="margin-top: 15px;">Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này. Mật khẩu của bạn vẫn được bảo mật an toàn.</p>
        </div>
    </div>
</body>
</html>
