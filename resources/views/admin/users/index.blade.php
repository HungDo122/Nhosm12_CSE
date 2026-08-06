@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-users text-primary me-2"></i>Quản lý Người Dùng</h2>
        <p class="text-muted mb-0">Quản lý tài khoản, mã sinh viên và phân quyền hệ thống</p>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email, mã sinh viên..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="club_manager" {{ request('role') == 'club_manager' ? 'selected' : '' }}>Chủ nhiệm CLB</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Sinh viên</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold"><i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Mã Sinh Viên</th>
                        <th>Vai Trò</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $user->id }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $user->name }}</span>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $user->student_code ?? '-' }}</span>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-danger"><i class="fa-solid fa-user-shield me-1"></i>Admin</span>
                            @elseif($user->isClubManager())
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-user-tie me-1"></i>Chủ nhiệm CLB</span>
                            @else
                                <span class="badge bg-info text-dark"><i class="fa-solid fa-user me-1"></i>Sinh viên</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="fa-solid fa-user-pen"></i> Sửa
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-user-slash fa-2x mb-2 d-block"></i>
                            Không tìm thấy người dùng phù hợp.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
