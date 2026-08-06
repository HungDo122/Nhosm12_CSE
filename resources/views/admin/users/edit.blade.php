@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-user-pen text-warning me-2"></i>Chỉnh Sửa Người Dùng</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="student_code" class="form-label fw-semibold">Mã Sinh Viên</label>
                        <input type="text" class="form-control @error('student_code') is-invalid @enderror" id="student_code" name="student_code" value="{{ old('student_code', $user->student_code) }}" placeholder="Ví dụ: A35001">
                        @error('student_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-semibold">Vai Trò Hệ Thống <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Sinh viên (Student)</option>
                            <option value="club_manager" {{ old('role', $user->role) == 'club_manager' ? 'selected' : '' }}>Chủ nhiệm CLB (Club Manager)</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold">
                            <i class="fa-solid fa-save me-1"></i> Cập Nhật Người Dùng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
