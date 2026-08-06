@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-tags text-primary me-2"></i>Quản lý Danh Mục Sự Kiện</h2>
        <p class="text-muted mb-0">Phân loại các hoạt động và sự kiện cho sinh viên</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm fw-semibold">
        <i class="fa-solid fa-plus me-1"></i> Thêm danh mục mới
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Mô Tả</th>
                        <th>Số sự kiện</th>
                        <th class="text-end pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $category->id }}</td>
                        <td>
                            <span class="fw-semibold text-primary"><i class="fa-solid fa-folder me-2"></i>{{ $category->name }}</span>
                        </td>
                        <td>{{ $category->description ?? 'Không có mô tả' }}</td>
                        <td>
                            <span class="badge bg-info text-dark rounded-pill">{{ $category->events_count }} sự kiện</span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="fa-solid fa-pen"></i> Sửa
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                            Chưa có danh mục sự kiện nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
