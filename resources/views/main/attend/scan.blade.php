@extends('layouts.main')

@section('content')
<div class="container text-center mt-5">
    <h1 class="mb-4">📷 Scan Student QR Code</h1>

    <div id="reader" style="width: 300px; margin: auto;"></div>

    <div id="scan-result" class="mt-4 alert alert-info d-none">Scanning...</div>
</div>
@endsection

@section('outside')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function showResult(message, type = 'info') {
        $('#scan-result')
            .removeClass('d-none alert-info alert-success alert-danger')
            .addClass(`alert-${type}`)
            .text(message);
    }

    function onScanSuccess(decodedText, decodedResult) {
        showResult(`✅ Scanned: ${decodedText}`, 'success');

        $.ajax({
            url: `/subjects/{{ $room->id }}/subject/{{ $subject->id }}/attend/scan`,
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

    // Start scanner
    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess
    ).catch(err => {
        showResult(`Camera error: ${err}`, 'danger');
    });
</script>
@endsection
