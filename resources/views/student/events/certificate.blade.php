<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chứng nhận tham gia sự kiện</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            padding: 50px;
        }
        .certificate-container {
            border: 10px solid #0056b3;
            padding: 40px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }
        h1 {
            color: #0056b3;
            font-size: 36px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 24px;
            color: #333;
        }
        .student-name {
            font-size: 30px;
            font-weight: bold;
            color: #d9534f;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .event-name {
            font-size: 22px;
            font-style: italic;
            color: #000;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            font-size: 14px;
            color: #777;
        }
        .seal {
            margin-top: 30px;
            font-style: italic;
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <h1>GIẤY CHỨNG NHẬN</h1>
        <h2>Chứng nhận sinh viên</h2>
        
        <div class="student-name">{{ $registration->user->name }}</div>
        <p>Mã sinh viên: {{ $registration->user->student_code ?? 'N/A' }}</p>
        
        <p>Đã tham gia tích cực sự kiện:</p>
        <div class="event-name">"{{ $registration->event->name }}"</div>
        
        <p>Tổ chức vào ngày: {{ \Carbon\Carbon::parse($registration->event->start_time)->format('d/m/Y') }}</p>
        
        <div class="seal">
            <p>Hệ thống Quản lý Câu Lạc Bộ</p>
        </div>
        
        <div class="footer">
            <p>Xác nhận lúc: {{ \Carbon\Carbon::parse($registration->checkinLog->checkin_time)->format('d/m/Y H:i:s') }}</p>
            <p>Mã xác thực: {{ $registration->qr_code_string }}</p>
        </div>
    </div>
</body>
</html>
