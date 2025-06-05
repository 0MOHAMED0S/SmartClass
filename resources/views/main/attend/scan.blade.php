@extends('layouts.main')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg rounded-4 p-4 w-100" style="max-width: 100%;">
        <h2 class="text-center mb-4 fs-3">📷 Scan Student QR Code</h2>

        <!-- Responsive QR Scanner Box -->
        <div class="d-flex justify-content-center">
            <div id="reader" class="border rounded-3" style="width: 100%; max-width: 100%;"></div>
        </div>

        <!-- Scan Result -->
        <div id="scan-result" class="mt-4 alert d-none text-center fs-5 fw-semibold">
            <div class="spinner-border text-primary me-2" role="status" style="width: 1rem; height: 1rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>Scanning...</span>
        </div>
    </div>
</div>
@endsection

@section('outside')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function showResult(message, type = 'info') {
        $('#scan-result')
            .removeClass('d-none alert-info alert-success alert-danger')
            .addClass(`alert alert-${type}`)
            .html(`<strong>${message}</strong>`);
    }

    function onScanSuccess(decodedText, decodedResult) {
        showResult(`✅ Scanned: ${decodedText}`, 'success');

        $.ajax({
            url: "{{ route('subjects.attend.scan', [$room->id, $subject->id, $attend]) }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                qr_code: decodedText,
                room_id: {{ $room->id }},
                subject_id: {{ $subject->id }},
                attend_id: {{ $attendance->id }},
            },
            success: function(response) {
                showResult(response.message || '✅ Attendance marked.', 'success');
            },
            error: function(xhr) {
                showResult(xhr.responseJSON?.message || '❌ Error marking attendance.', 'danger');
            }
        });
    }

    // Automatically resize scanner for screen size
    const screenWidth = window.innerWidth;
    const qrSize = screenWidth < 500 ? screenWidth * 0.85 : 400;

    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: qrSize, height: qrSize } },
        onScanSuccess
    ).catch(err => {
        showResult(`❌ Camera error: ${err}`, 'danger');
    });
</script>
@endsection
