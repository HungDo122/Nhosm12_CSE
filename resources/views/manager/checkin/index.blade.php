@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center fw-bold text-primary">Quét QR Check-in</h2>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="eventSelect" class="form-label fw-bold">Chọn sự kiện đang diễn ra:</label>
                        <select id="eventSelect" class="form-select form-select-lg">
                            <option value="">-- Vui lòng chọn sự kiện --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }} - {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="reader" style="width: 100%; display: none; border-radius: 12px; overflow: hidden;" class="shadow-sm border"></div>
                    
                    <div id="manualCheckinWrapper" class="mt-4" style="display: none;">
                        <hr>
                        <p class="text-center text-muted mb-2">Hoặc nhập mã thủ công nếu không quét được ảnh:</p>
                        <div class="input-group">
                            <input type="text" id="manualCode" class="form-control" placeholder="Nhập mã vé (VD: EVENT_...)">
                            <button class="btn btn-outline-primary" type="button" id="btnManualCheckin">Check-in</button>
                        </div>
                    </div>

                    <div id="resultBox" class="alert mt-4 text-center fs-5" style="display: none; border-radius: 8px;"></div>
                </div>
            </div>
            
            <div class="text-center mt-3 text-muted small">
                Vui lòng cấp quyền sử dụng Camera cho trình duyệt để có thể quét mã QR.
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const eventSelect = document.getElementById('eventSelect');
        const readerDiv = document.getElementById('reader');
        const manualCheckinWrapper = document.getElementById('manualCheckinWrapper');
        const resultBox = document.getElementById('resultBox');
        
        let html5QrcodeScanner = null;
        let isProcessing = false;

        eventSelect.addEventListener('change', function() {
            if (this.value) {
                readerDiv.style.display = 'block';
                manualCheckinWrapper.style.display = 'block';
                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader", { fps: 10, qrbox: {width: 250, height: 250} }, /* verbose= */ false);
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                }
            } else {
                readerDiv.style.display = 'none';
                manualCheckinWrapper.style.display = 'none';
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                }
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;
            
            const eventId = eventSelect.value;
            
            // Tạm dừng quét
            try {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.pause();
                }
            } catch (e) {
                console.warn('Cannot pause scanner:', e);
            }
            
            resultBox.style.display = 'block';
            resultBox.className = 'alert alert-info';
            resultBox.innerHTML = `Đang xử lý...`;

            fetch("{{ route('manager.checkin.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    qr_code_string: decodedText,
                    event_id: eventId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultBox.className = 'alert alert-success fw-bold';
                    resultBox.innerHTML = `✅ ${data.message}`;
                } else {
                    resultBox.className = 'alert alert-danger fw-bold';
                    resultBox.innerHTML = `❌ ${data.message}`;
                }
                
                setTimeout(() => {
                    resultBox.style.display = 'none';
                    isProcessing = false;
                    try {
                        if (html5QrcodeScanner) {
                            html5QrcodeScanner.resume();
                        }
                    } catch (e) {
                        console.warn('Cannot resume scanner:', e);
                    }
                }, 2500);
            })
            .catch(error => {
                console.error(error);
                resultBox.className = 'alert alert-warning';
                resultBox.innerHTML = `Lỗi kết nối máy chủ!`;
                
                setTimeout(() => {
                    resultBox.style.display = 'none';
                    isProcessing = false;
                    try {
                        if (html5QrcodeScanner) {
                            html5QrcodeScanner.resume();
                        }
                    } catch (e) {
                        console.warn('Cannot resume scanner:', e);
                    }
                }, 2500);
            });
        }

        function onScanFailure(error) {
            // Do nothing
        }

        document.getElementById('btnManualCheckin').addEventListener('click', function() {
            const manualCode = document.getElementById('manualCode').value.trim();
            if (manualCode) {
                onScanSuccess(manualCode, null);
                document.getElementById('manualCode').value = '';
            } else {
                alert('Vui lòng nhập mã vé!');
            }
        });
    });
</script>
@endsection
