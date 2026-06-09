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
        .reply-box {
            background-color: #ffffff;
            border: 1px solid rgba(232, 99, 74, 0.2);
            border-left: 5px solid #e8634a;
            border-radius: 16px;
            padding: 25px;
            font-size: 15px;
            color: #2B2623;
            margin: 20px 0;
            line-height: 1.7;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .divider {
            height: 1px;
            background-color: rgba(43, 38, 35, 0.1);
            margin: 30px 0;
        }
        .original-box {
            background-color: #FAF7F2;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 13px;
            color: #666;
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
            <p>Cảm ơn bạn đã liên hệ và gửi đóng góp cho <span class="highlight">Chill Chill</span>. Chúng tôi xin phản hồi về ý kiến của bạn như sau:</p>
            
            <div class="reply-box">
                {!! nl2br(e($feedback->reply_content)) !!}
            </div>
            
            <p>Chúng tôi hy vọng câu trả lời này đã giải đáp được thắc mắc của bạn. Nếu bạn cần hỗ trợ thêm thông tin nào khác, vui lòng phản hồi lại email này hoặc gọi hotline <span class="highlight">1800 6936</span>.</p>
            <p>Chúc bạn một ngày tuyệt vời và hy vọng sẽ được phục vụ bạn tại Chill Chill Coffee & Tea!</p>
            <p>Thân mến,<br><strong style="color: #2B2623;">Đội ngũ CSKH Chill Chill</strong></p>
            
            <div class="divider"></div>
            
            <div class="original-box">
                <strong style="color:#2B2623; display:block; margin-bottom:5px;">Tin nhắn gốc của bạn:</strong>
                "{!! nl2br(e($feedback->message)) !!}"
            </div>
        </div>
        <div class="footer">
            © {{ date('Y') }} Chill Chill Coffee & Tea. All rights reserved.<br>
            Địa chỉ: 123 Đường Cà Phê, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh.<br>
            Website: <a href="http://localhost">chillchill.vn</a>
        </div>
    </div>
</body>
</html>
