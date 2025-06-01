@extends('layouts.main')

@section('content')
<div class="container text-center mt-5">
    <h1 class="mb-4">Scan Student QR Code</h1>
    <div id="reader" style="width: 300px; margin: auto;"></div>
    <div id="scan-result" class="mt-4 alert alert-info d-none">Scanning...</div>
</div>
@endsection

@section('outside')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function onScanSuccess(decodedText, decodedResult) {
        $('#scan-result')
            .removeClass('d-none alert-info alert-danger')
            .addClass('alert-success')
            .text(`Scanned: ${decodedText}`);

        $.ajax({
            url: "{{ route('subjects.attend.scan') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                qr_code: decodedText,
                room_id: {{ $room->id }},
                subject_id: {{ $subject->id }},
                attend_id: {{ $attend }},
            },
            success: function(response) {
                $('#scan-result').text(response.message || 'Attendance marked!');
            },
            error: function(xhr) {
                $('#scan-result')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text(xhr.responseJSON?.message || 'Error marking attendance.');
            }
        });
    }

    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess
    ).catch(err => {
        $('#scan-result')
            .removeClass('d-none')
            .addClass('alert-danger')
            .text(`Camera error: ${err}`);
    });
</script>
@endsection
