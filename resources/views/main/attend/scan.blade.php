@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center">
        <h1 class="mb-4">📷 Scan Student QR Code</h1>
        <div id="reader" style="width: 300px; margin: auto;"></div>
        <div id="scan-result" class="mt-4 alert alert-info d-none">Scanning...</div>
    </div>
</div>
@endsection

@section('outside')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let html5QrCode;
    let scannerRunning = false;

    function showResult(message, type = 'info') {
        $('#scan-result')
            .removeClass('d-none alert-info alert-success alert-danger')
            .addClass(`alert-${type}`)
            .text(message);
    }

    function stopScanner() {
        if (html5QrCode && scannerRunning) {
            html5QrCode.stop().then(() => {
                scannerRunning = false;
            }).catch(err => {
                console.error("Failed to stop scanner", err);
            });
        }
    }

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess
        ).then(() => {
            scannerRunning = true;
        }).catch(err => {
            showResult(`Camera error: ${err}`, 'danger');
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        stopScanner(); // Stop scanning

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
                Swal.fire({
                    icon: 'success',
                    title: 'Successful!',
                    text: response.message || '✅ Attendance marked.',
                    confirmButtonText: 'OK',
                    timer: 10000,
                    timerProgressBar: true
                }).then(() => {
                    startScanner(); // Restart scanning
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed!',
                    text: xhr.responseJSON?.message || '❌ Error marking attendance.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    startScanner(); // Restart scanning
                });
            }
        });
    }

    // Start scanner on page load
    $(document).ready(function() {
        startScanner();
    });
</script>
@endsection
