<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP Đặt Lại Mật Khẩu</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #FAF7F2;
            margin: 0;
            padding: 20px;
            color: #2B2623;
        }
        .container {
            max-width: 550px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #EFEAE4;
        }
        .header {
            background-color: #2B2623;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #FAF7F2;
            font-size: 24px;
            margin: 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header span {
            color: #e8634a;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content h2 {
            font-size: 20px;
            color: #2B2623;
            margin-top: 0;
        }
        .content p {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
        }
        .otp-box {
            background: linear-gradient(135deg, #FAF7F2 0%, #FFF5ED 100%);
            border: 2px dashed #e8634a;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            display: inline-block;
            width: 80%;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #e8634a;
            letter-spacing: 8px;
            font-family: 'Courier New', Courier, monospace;
        }
        .footer {
            background-color: #FAF7F2;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #888;
            border-top: 1px solid #EFEAE4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chill Chill <span>Coffee</span></h1>
        </div>
        <div class="content">
            <h2>Xin chào {{ $userName }},</h2>
            <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại <strong>Chill Chill Coffee</strong>.</p>
            <p>Dưới đây là mã OTP xác thực của bạn:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p style="font-size: 13px; color: #999;">Mã OTP này có hiệu lực trong vòng <strong>5 phút</strong>. Vui lòng không chia sẻ mã này cho bất kỳ ai để bảo vệ tài khoản của bạn.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Chill Chill Coffee & Tea. Tất cả các quyền được bảo lưu.</p>
            <p>Nếu bạn không gửi yêu cầu này, vui lòng bỏ qua email này.</p>
        </div>
    </div>
</body>
</html>
