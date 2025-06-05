@extends('layouts.main')
@section('styles')
<style>
    #reader {
    min-height: 250px;
    background-color: #f8f9fa;
}

</style>
@endsection
@section('content')
<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="card shadow-lg rounded-4 p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4">📷 Scan Student QR Code</h2>

        <div id="reader" class="border rounded-3" style="width: 100%; height: auto;"></div>

        <div id="scan-result" class="mt-4 alert text-center d-none">
            <div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;">
                <span class="visually-hidden">Scanning...</span>
            </div>
            <span class="ms-2">Scanning...</span>
        </div>
    </div>
</div>
@endsection

@section('outside')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function showResult(message, type = 'info') {
        const resultEl = $('#scan-result');
        resultEl.removeClass('d-none alert-info alert-success alert-danger')
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

    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
        },
        onScanSuccess
    ).catch(err => {
        showResult(`📷 Camera error: ${err}`, 'danger');
    });
</script>
@endsection
