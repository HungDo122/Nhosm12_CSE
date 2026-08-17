@extends('layouts.app')

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="fa-solid fa-calendar-day text-primary me-2"></i>
            {{ $event->name }}
        </h2>
        <p class="text-muted mb-0">Chi tiết sự kiện & danh sách đăng ký</p>
    </div>
    <div class="d-flex gap-2">
        {{-- Nút duyệt / từ chối (nếu đang pending) --}}
        @if($event->status === 'pending')
            <form action="{{ route('admin.events.approve', $event->id) }}" method="POST"
                  onsubmit="return confirm('Duyệt sự kiện này?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check me-1"></i>Duyệt
                </button>
            </form>
            <form action="{{ route('admin.events.reject', $event->id) }}" method="POST"
                  onsubmit="return confirm('Từ chối sự kiện này?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fa-solid fa-xmark me-1"></i>Từ chối
                </button>
            </form>
        @endif
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Thông tin sự kiện --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fa-solid fa-info-circle me-1"></i>Thông Tin Sự Kiện
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Câu lạc bộ</dt>
                    <dd class="fw-semibold mb-3">{{ $event->club->name ?? 'N/A' }}</dd>

                    <dt class="text-muted small">Danh mục</dt>
                    <dd class="mb-3">
                        <span class="badge bg-info text-dark">{{ $event->category->name ?? 'Chưa phân loại' }}</span>
                    </dd>

                    <dt class="text-muted small">Địa điểm</dt>
                    <dd class="mb-3">{{ $event->location ?? 'Chưa xác định' }}</dd>

                    <dt class="text-muted small">Thời gian bắt đầu</dt>
                    <dd class="mb-3">
                        {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') : 'Chưa xác định' }}
                    </dd>

                    <dt class="text-muted small">Thời gian kết thúc</dt>
                    <dd class="mb-3">
                        {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m/Y H:i') : 'Chưa xác định' }}
                    </dd>

                    <dt class="text-muted small">Sức chứa</dt>
                    <dd class="mb-3">
                        <span class="fw-semibold">{{ $event->registrations->count() }}</span>
                        <span class="text-muted">/ {{ $event->capacity }} người</span>
                        <div class="progress mt-1" style="height: 6px;">
                            @php $pct = $event->capacity > 0 ? min(100, ($event->registrations->count() / $event->capacity) * 100) : 0; @endphp
                            <div class="progress-bar {{ $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </dd>

                    <dt class="text-muted small">Trạng thái</dt>
                    <dd class="mb-0">
                        @if($event->status === 'pending')
                            <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-clock me-1"></i>Chờ duyệt</span>
                        @elseif($event->status === 'approved')
                            <span class="badge bg-success fs-6"><i class="fa-solid fa-circle-check me-1"></i>Đã duyệt</span>
                        @else
                            <span class="badge bg-danger fs-6"><i class="fa-solid fa-circle-xmark me-1"></i>Đã từ chối</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        {{-- Mô tả --}}
        @if($event->description)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="fa-solid fa-align-left text-primary me-1"></i>Mô Tả
            </div>
            <div class="card-body text-muted">
                {{ $event->description }}
            </div>
        </div>
        @endif
    </div>

    {{-- Danh sách đăng ký --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-users text-primary me-2"></i>Danh Sách Đăng Ký ({{ $event->registrations->count() }})</span>
                @php $checkedInCount = $event->registrations->whereNotNull('checkinLog')->count(); @endphp
                <span class="badge bg-success">{{ $checkedInCount }} đã check-in</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Sinh viên</th>
                                <th>Mã SV</th>
                                <th>Đăng ký lúc</th>
                                <th class="text-center">Check-in</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->registrations as $i => $reg)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $reg->user->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $reg->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="font-monospace text-muted small">{{ $reg->user->student_code ?? '—' }}</span>
                                </td>
                                <td class="small text-muted">
                                    {{ $reg->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-center">
                                    @if($reg->checkinLog)
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-circle-check me-1"></i>
                                            {{ \Carbon\Carbon::parse($reg->checkinLog->checkin_time)->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Chưa check-in</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-user-slash fa-2x mb-2 d-block"></i>
                                    Chưa có sinh viên nào đăng ký sự kiện này.
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
