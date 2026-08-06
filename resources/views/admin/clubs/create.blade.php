@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Thêm Câu Lạc Bộ Mới</h2>
            <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.clubs.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên Câu Lạc Bộ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: CLB Tin Học CSE" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label fw-semibold">Mã CLB</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="Ví dụ: CLB-TINHOC">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Mô Tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Giới thiệu về mục tiêu, hoạt động của CLB...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold">Trạng Thái</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.clubs.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-save me-1"></i> Lưu CLB
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection