<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ Thống Quản Lý Câu Lạc Bộ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center glass-card p-5">
                <h1 class="display-4 fw-bold text-primary mb-3">Club & Event Manager</h1>
                <p class="lead text-muted mb-5">Hệ thống quản lý sự kiện và điểm rèn luyện dành cho sinh viên.</p>
                
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    @auth
                        <a href="{{ route('student.events.index') }}" class="btn btn-primary btn-lg px-4 gap-3 rounded-pill shadow-sm">Vào Trang Sinh Viên</a>
                        <a href="{{ route('manager.checkin.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill shadow-sm">Vào Trang Check-in</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold">Đăng Nhập</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">Đăng Ký</a>
                    @endauth
                </div>
                
                @auth
                <div class="mt-4">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger text-decoration-none">Đăng xuất</button>
                    </form>
                </div>
                @endauth
                
                <div class="mt-5 text-muted small">
                    &copy; 2026 Nhosm12_CSE
                </div>
            </div>
        </div>
    </div>
</body>
</html>
