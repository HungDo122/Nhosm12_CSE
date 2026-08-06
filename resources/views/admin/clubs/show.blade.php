@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-users-gear text-primary me-2"></i>{{ $club->name }}</h2>
        <p class="text-muted mb-0">Quản lý chi tiết và danh sách thành viên câu lạc bộ</p>
    </div>
    <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row">
    <!-- Thống kê & Thông tin CLB -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fa-solid fa-info-circle me-1"></i> Thông Tin Câu Lạc Bộ
            </div>
            <div class="card-body">
                <p><strong>Mã CLB:</strong> <span class="badge bg-secondary font-monospace">{{ $club->code ?? 'N/A' }}</span></p>
                <p><strong>Trạng thái:</strong> 
                    @if($club->status === 'active')
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-secondary">Tạm dừng</span>
                    @endif
                </p>
                <p><strong>Mô tả:</strong></p>
                <p class="text-muted border rounded p-2 bg-light">{{ $club->description ?? 'Chưa có mô tả' }}</p>
            </div>
        </div>

        <!-- Form Thêm Thành Viên -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-bold">
                <i class="fa-solid fa-user-plus me-1"></i> Thêm Thành Viên Mới
            </div>
            <div class="card-body">
                <form action="{{ route('admin.clubs.members.store', $club->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-semibold">Chọn người dùng</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                            <option value="">-- Chọn thành viên --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) {{ $user->student_code ? '- ' . $user->student_code : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Vai trò trong CLB</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="member">Thành viên (Member)</option>
                            <option value="leader">Chủ nhiệm / Quản lý (Leader)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i> Thêm vào CLB
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Danh sách thành viên -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-list text-primary me-2"></i>Danh Sách Thành Viên ({{ $club->members->count() }})</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Họ và tên</th>
                                <th>Email</th>
                                <th>Mã Sinh Viên</th>
                                <th>Vai trò</th>
                                <th class="text-end pe-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($club->members as $member)
                            <tr>
                                <td class="ps-3 fw-semibold">
                                    {{ $member->user->name ?? 'N/A' }}
                                </td>
                                <td>{{ $member->user->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="font-monospace text-muted">{{ $member->user->student_code ?? '-' }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.clubs.members.update', [$club->id, $member->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-select form-select-sm d-inline-block w-auto border-0 bg-light fw-bold {{ $member->role === 'leader' ? 'text-danger' : 'text-primary' }}" onchange="this.form.submit()">
                                            <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>Member</option>
                                            <option value="leader" {{ $member->role === 'leader' ? 'selected' : '' }}>Leader (Chủ nhiệm)</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('admin.clubs.members.destroy', [$club->id, $member->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này khỏi CLB?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa khỏi CLB">
                                            <i class="fa-solid fa-user-minus"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-users-slash fa-2x mb-2 d-block"></i>
                                    Câu lạc bộ này chưa có thành viên nào.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
