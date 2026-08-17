@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Sự kiện của tôi</h2>

    <div class="row">
        @foreach($registrations as $reg)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-bold text-dark">{{ $reg->event->name }}</h5>
                        <p class="mb-1 small text-muted">
                            🕒 {{ \Carbon\Carbon::parse($reg->event->start_time)->format('d/m/Y H:i') }} <br>
                            📍 {{ $reg->event->location }}
                        </p>
                        @if($reg->checkinLog)
                            <span class="badge bg-success rounded-pill px-3 py-2 mt-2">Đã tham gia</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 mt-2">Chưa tham gia</span>
                        @endif
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-2 w-100" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#qrModal{{ $reg->id }}">
                            Mã QR
                        </button>
                        @if($reg->checkinLog)
                        <a href="{{ route('student.events.certificate', $reg->id) }}" class="btn btn-success btn-sm w-100" style="border-radius: 8px;">Tải PDF</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="qrModal{{ $reg->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content" style="border-radius: 16px;">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pb-4 pt-0">
                        <h5 class="mb-2 fw-bold">Vé Tham Gia</h5>
                        <p class="text-muted small mb-4">{{ $reg->event->name }}</p>
                        <div class="p-3 bg-white d-inline-block rounded shadow-sm border" style="background-color: white !important;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($reg->qr_code_string) }}&bgcolor=ffffff&color=000000&margin=10" alt="QR Code" class="img-fluid" style="filter: none !important;">
                        </div>
                        <p class="mt-4 small text-muted mb-0">Mã: {{ $reg->qr_code_string }}</p>
                        <p class="small text-muted">Vui lòng đưa mã này cho ban tổ chức.</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        @if($registrations->isEmpty())
        <div class="col-12 text-center text-muted mt-5">
            <p>Bạn chưa đăng ký sự kiện nào.</p>
            <a href="{{ route('student.events.index') }}" class="btn btn-primary mt-2">Khám phá sự kiện</a>
        </div>
        @endif
    </div>
</div>
@endsection
