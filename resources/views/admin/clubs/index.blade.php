@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-people-group text-primary me-2"></i>Quản lý Câu lạc bộ</h2>
        <p class="text-muted mb-0">Danh sách tất cả các câu lạc bộ trong nhà trường</p>
    </div>
    <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary shadow-sm fw-semibold">
        <i class="fa-solid fa-plus me-1"></i> Thêm CLB mới
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Mã CLB</th>
                        <th>Tên Câu Lạc Bộ</th>
                        <th>Chủ nhiệm</th>
                        <th>Số thành viên</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clubs as $club)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $club->id }}</td>
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ $club->code ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $club->name }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $club->description }}</small>
                        </td>
                        <td>
                            @if($club->leaders->count() > 0)
                                @foreach($club->leaders as $leader)
                                    <span class="badge bg-info text-dark me-1"><i class="fa-solid fa-user-shield me-1"></i>{{ $leader->name }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-light text-muted border">Chưa có</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary rounded-pill"><i class="fa-solid fa-users me-1"></i>{{ $club->members_count }}</span>
                        </td>
                        <td>
                            @if($club->status === 'active')
                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Hoạt động</span>
                            @else
                                <span class="badge bg-secondary"><i class="fa-solid fa-circle-pause me-1"></i>Tạm dừng</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.clubs.show', $club->id) }}" class="btn btn-sm btn-outline-info me-1" title="Quản lý thành viên">
                                <i class="fa-solid fa-users-gear"></i> Chi tiết
                            </a>
                            <a href="{{ route('admin.clubs.edit', $club->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Sửa">
                                <i class="fa-solid fa-pen"></i> Sửa
                            </a>
                            <form action="{{ route('admin.clubs.destroy', $club->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa CLB này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                            Chưa có câu lạc bộ nào được tạo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection