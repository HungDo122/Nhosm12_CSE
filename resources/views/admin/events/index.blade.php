@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Sự kiện</h2>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tạo sự kiện</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên sự kiện</th>
                            <th>Câu lạc bộ</th>
                            <th>Danh mục</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td><strong>{{ $event->name }}</strong></td>
                            <td>{{ $event->club->name }}</td>
                            <td>{{ $event->category->name }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}<br>
                                <small class="text-muted">{{ $event->location }}</small>
                            </td>
                            <td>
                                @if($event->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @elseif($event->status === 'pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có sự kiện nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
