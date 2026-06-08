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
            padding: 35px 30px;
            text-align: center;
        }
        .header h1 {
            color: #FAF7F2;
            margin: 0;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 900;
        }
        .header span {
            color: #e8634a;
            display: block;
            margin-top: 5px;
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .content h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: #2B2623;
            font-size: 22px;
            margin-top: 0;
        }
        .highlight {
            color: #e8634a;
            font-weight: bold;
        }
        .divider {
            height: 1px;
            background-color: rgba(43, 38, 35, 0.1);
            margin: 30px 0;
        }
        .quote-box {
            background-color: #FAF7F2;
            border-radius: 16px;
            padding: 20px;
            font-style: italic;
            font-size: 14px;
            color: #555;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 30px;
            font-size: 12px;
            color: #888;
            background-color: #2B2623;
            color: #FAF7F2;
        }
        .footer a {
            color: #e8634a;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chill Chill</h1>
            <span>Coffee & Tea</span>
        </div>
        <div class="content">
            <h2>Chào {{ $feedback->name }},</h2>
            <p>Cảm ơn bạn đã gửi ý kiến phản hồi/đóng góp cho <span class="highlight">Chill Chill Coffee & Tea</span>.</p>
            <p>Chúng tôi đã nhận được thông tin liên hệ của bạn và sẽ phản hồi trong thời gian sớm nhất có thể (thông thường trong vòng 24 giờ làm việc).</p>
            
            <div class="divider"></div>
            
            <p style="font-weight: bold; color: #2B2623; margin-bottom: 5px;">Tóm tắt nội dung bạn đã gửi:</p>
            <div class="quote-box">
                "{!! nl2br(e($feedback->message)) !!}"
            </div>
            
            <p>Nếu bạn có bất kỳ thay đổi nào hoặc cần hỗ trợ gấp, vui lòng liên hệ hotline: <span class="highlight">1800 6936</span>.</p>
            <p>Chúc bạn một ngày tràn đầy năng lượng tích cực!</p>
            <p>Trân trọng,<br><strong style="color: #2B2623;">Đội ngũ Chill Chill</strong></p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Chill Chill Coffee & Tea. All rights reserved.<br>
            Địa chỉ: 123 Đường Cà Phê, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh.<br>
            Website: <a href="http://localhost">chillchill.vn</a>
        </div>
    </div>
</body>
</html>
