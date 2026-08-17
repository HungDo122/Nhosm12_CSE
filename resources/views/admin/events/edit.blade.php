@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white pb-0 border-0 pt-4 px-4">
                    <h4 class="mb-0">Cập Nhật Sự Kiện</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.events.update', $event) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Câu Lạc Bộ</label>
                                <select name="club_id" class="form-select @error('club_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn Câu Lạc Bộ --</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" {{ old('club_id', $event->club_id) == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                                    @endforeach
                                </select>
                                @error('club_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Danh Mục Sự Kiện</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn Danh Mục --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên Sự Kiện</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $event->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô Tả</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Địa Điểm Tổ Chức</label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $event->location) }}" required>
                                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số Lượng (Capacity)</label>
                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $event->capacity) }}" min="1" required>
                                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Thời Gian Bắt Đầu</label>
                                <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', date('Y-m-d\TH:i', strtotime($event->start_time))) }}" required>
                                @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Thời Gian Kết Thúc</label>
                                <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', date('Y-m-d\TH:i', strtotime($event->end_time))) }}" required>
                                @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if(Auth::user()->isAdmin())
                        <div class="mb-4 p-3 bg-light rounded border">
                            <label class="form-label fw-bold text-primary">Phê Duyệt Sự Kiện (Chỉ Admin)</label>
                            <select name="status" class="form-select border-primary shadow-sm">
                                <option value="pending" {{ old('status', $event->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt (Pending)</option>
                                <option value="approved" {{ old('status', $event->status) == 'approved' ? 'selected' : '' }}>Đã duyệt (Approved)</option>
                                <option value="rejected" {{ old('status', $event->status) == 'rejected' ? 'selected' : '' }}>Từ chối (Rejected)</option>
                            </select>
                        </div>
                        @else
                            <div class="mb-4">
                                <label class="form-label">Trạng Thái</label>
                                <div>
                                    @if($event->status === 'approved')
                                        <span class="badge bg-success">Đã duyệt (Approved)</span>
                                    @elseif($event->status === 'pending')
                                        <span class="badge bg-warning text-dark">Chờ duyệt (Pending)</span>
                                    @else
                                        <span class="badge bg-danger">Từ chối (Rejected)</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-2">Chỉ Admin mới có quyền thay đổi trạng thái sự kiện.</small>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.events.index') }}" class="btn btn-light">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary">Cập Nhật Sự Kiện</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
