@extends('layouts.app')

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-calendar-check text-primary me-2"></i>Quản Lý Sự Kiện</h2>
        <p class="text-muted mb-0">Xem, duyệt và quản lý toàn bộ sự kiện từ các câu lạc bộ</p>
    </div>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tạo sự kiện</a>
</div>


{{-- Thống kê nhanh --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-dark">{{ $stats['total'] }}</div>
                <div class="small text-muted">Tổng sự kiện</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.events.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center h-100 {{ request('status') === 'pending' ? 'border-warning border-2' : '' }}">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $stats['pending'] }}</div>
                    <div class="small text-muted">Chờ duyệt</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.events.index', ['status' => 'approved']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center h-100 {{ request('status') === 'approved' ? 'border-success border-2' : '' }}">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-success">{{ $stats['approved'] }}</div>
                    <div class="small text-muted">Đã duyệt</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.events.index', ['status' => 'rejected']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center h-100 {{ request('status') === 'rejected' ? 'border-danger border-2' : '' }}">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $stats['rejected'] }}</div>
                    <div class="small text-muted">Đã từ chối</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Bộ lọc --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.events.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên sự kiện..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Câu lạc bộ</label>
                <select name="club_id" class="form-select">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="fa-solid fa-search me-1"></i>Lọc</button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Bảng sự kiện --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 fw-bold">
        <i class="fa-solid fa-list text-primary me-2"></i>Danh Sách Sự Kiện
        <span class="badge bg-secondary ms-1">{{ $events->total() ?? 0 }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Tên sự kiện</th>
                        <th>Câu lạc bộ</th>
                        <th>Thời gian</th>
                        <th class="text-center">Đăng ký</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end pe-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $event->name }}</div>
                            @if($event->location)
                                <div class="small text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $event->location }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $event->club->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($event->start_time)
                                <div class="small">
                                    <i class="fa-regular fa-clock me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}
                                </div>
                            @else
                                <span class="text-muted small">Chưa xác định</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">{{ $event->registrations_count ?? 0 }}</span>
                            <span class="text-muted small">/ {{ $event->capacity }}</span>
                        </td>
                        <td class="text-center">
                            @if($event->status === 'pending')
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chờ duyệt</span>
                            @elseif($event->status === 'approved')
                                <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Đã duyệt</span>
                            @else
                                <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Từ chối</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                {{-- Nút duyệt --}}
                                @if($event->status === 'pending' && Auth::user()->isAdmin())
                                    <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Duyệt sự kiện: {{ $event->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.events.reject', $event->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Từ chối sự kiện: {{ $event->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Từ chối">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Nút xem chi tiết --}}
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Nút sửa --}}
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-outline-secondary" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                {{-- Nút xóa --}}
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xóa sự kiện này? Hành động không thể hoàn tác!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block"></i>
                            Không tìm thấy sự kiện nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(isset($events) && method_exists($events, 'hasPages') && $events->hasPages())
    <div class="card-footer bg-white">
        {{ $events->links() }}
    </div>
    @endif
</div>
</div>
@endsection
