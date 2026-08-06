@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Khám phá Sự kiện</h2>

    <div class="row">
        @foreach($events as $event)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary">{{ $event->name }}</h5>
                    <h6 class="card-subtitle mb-3 text-muted">{{ $event->category->name ?? 'Chung' }}</h6>
                    <p class="card-text text-secondary">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                    <p class="mb-1 small"><strong>📍 Địa điểm:</strong> {{ $event->location }}</p>
                    <p class="mb-1 small"><strong>🕒 Thời gian:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}</p>
                    <div class="mt-3">
                        @php
                            // Sử dụng registrations_count đã được load sẵn bằng withCount() — không gọi DB thêm
                            $registered = $event->registrations_count;
                            $percent = $event->capacity > 0 ? ($registered / $event->capacity) * 100 : 100;
                        @endphp
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Slot đăng ký</small>
                            <small class="fw-bold">{{ $registered }} / {{ $event->capacity }}</small>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : 'bg-primary' }}" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                    <form action="{{ route('student.events.register', $event->id) }}" method="POST">
                        @csrf
                        {{-- Dùng accessor $event->is_full thay vì kiểm tra thủ công --}}
                        <button type="submit" class="btn btn-primary w-100 shadow-sm" style="border-radius: 8px;" {{ $event->is_full ? 'disabled' : '' }}>
                            {{ $event->is_full ? 'Đã hết chỗ' : 'Đăng ký tham gia' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        
        @if($events->isEmpty())
        <div class="col-12 text-center text-muted mt-5">
            <p>Hiện không có sự kiện nào sắp diễn ra.</p>
        </div>
        @endif
    </div>
</div>

<style>
    .card:hover { transform: translateY(-5px); }
</style>
@endsection
