<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 👉 BẠN HÃY THÊM DÒNG NÀY VÀO

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Kiểm tra xem role của người dùng có nằm trong danh sách cho phép không
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Nếu không đúng role thì báo lỗi 403
        abort(403, 'Bạn không có quyền truy cập khu vực này.');
    }
}