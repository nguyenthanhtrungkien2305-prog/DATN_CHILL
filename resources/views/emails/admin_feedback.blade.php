<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FAF7F2;
            color: #2B2623;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(43, 38, 35, 0.05);
        }
        .header {
            background-color: #2B2623;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #e8634a;
            margin: 0;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .content {
            padding: 40px 30px;
        }
        .info-card {
            background-color: #FAF7F2;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .info-item {
            margin-bottom: 12px;
            font-size: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #e8634a;
            display: inline-block;
            width: 100px;
        }
        .message-box {
            background-color: #ffffff;
            border-left: 4px solid #e8634a;
            padding: 15px;
            border-radius: 4px;
            font-style: italic;
            font-size: 15px;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #888;
            background-color: #FAF7F2;
        }
        .btn {
            display: inline-block;
            background-color: #e8634a;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CHILL CHILL COFFEE</h1>
        </div>
        <div class="content">
            <h2 style="margin-top:0;">Bạn có phản hồi mới từ khách hàng!</h2>
            <p>Hệ thống vừa nhận được thông tin liên hệ mới với nội dung chi tiết dưới đây:</p>
            
            <div class="info-card">
                <div class="info-item">
                    <span class="info-label">Khách hàng:</span>
                    <span>{{ $feedback->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span>{{ $feedback->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Điện thoại:</span>
                    <span>{{ $feedback->phone ?? 'Không cung cấp' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Thời gian:</span>
                    <span>{{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <h3 style="color: #2B2623;">Nội dung tin nhắn:</h3>
            <div class="message-box">
                "{!! nl2br(e($feedback->message)) !!}"
            </div>

            <div style="text-align: center;">
                <a href="{{ route('feedbacks.show', $feedback->id) }}" class="btn">Xem & Trả lời ngay</a>
            </div>
        </div>
        <div class="footer">
            Đây là email tự động từ hệ thống quản lý Chill Chill Coffee. Vui lòng không trả lời trực tiếp email này.
        </div>
    </div>
</body>
</html>
