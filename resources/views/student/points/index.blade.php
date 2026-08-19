@extends('layouts.app')

@section('title', 'Điểm Hoạt Động - TLU Club Manager')

@section('content')
<div class="container py-4">

    {{-- Tiêu đề + Tổng điểm --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Điểm Hoạt Động</h2>
            <p class="text-muted mb-0 small">Lịch sử điểm rèn luyện từ các sự kiện đã tham gia.</p>
        </div>
        <div class="text-center px-4 py-3 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
            <div class="text-white small fw-semibold">TỔNG ĐIỂM</div>
            <div class="text-white fw-bold" style="font-size: 2rem; line-height: 1.1;">{{ $total }}</div>
        </div>
    </div>

    {{-- Bảng điểm --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            @if($points->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-star fa-3x mb-3 opacity-25"></i>
                    <p class="mb-2">Bạn chưa có điểm hoạt động nào.</p>
                    <a href="{{ route('student.events.index') }}" class="btn btn-primary rounded-pill px-4">
                        Khám phá sự kiện <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Sự kiện</th>
                                <th>Thời gian tham gia</th>
                                <th class="text-center">Điểm nhận</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($points as $i => $point)
                            <tr>
                                <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $point->event->name ?? 'Sự kiện đã bị xóa' }}</div>
                                    @if($point->event)
                                        <small class="text-muted">
                                            <i class="fa-solid fa-location-dot me-1"></i>{{ $point->event->location }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $point->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2 fs-6"
                                          style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
                                        +{{ $point->points }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="ps-4 fw-bold text-end pe-3">Tổng cộng:</td>
                                <td class="text-center">
                                    <span class="fw-bold text-primary fs-5">{{ $total }} điểm</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
