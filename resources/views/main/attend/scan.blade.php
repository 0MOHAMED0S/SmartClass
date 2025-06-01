@extends('layouts.main')

@section('content')
<div class="container text-center mt-5">
    <h1 class="mb-4">Simple QR Code Scanner Test</h1>
    <div id="reader" style="width: 300px; margin: auto;"></div>
    <div id="result" class="mt-4 alert alert-info d-none">Waiting for scan...</div>
</div>
@endsection

@section('outside')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('result').classList.remove('d-none');
        document.getElementById('result').classList.remove('alert-info');
        document.getElementById('result').classList.add('alert-success');
        document.getElementById('result').innerText = "Scanned Code: " + decodedText;
    }

    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" }, // use rear camera
        { fps: 10, qrbox: 250 },
        onScanSuccess
    ).catch(err => {
        document.getElementById('result').classList.remove('d-none');
        document.getElementById('result').classList.add('alert-danger');
        document.getElementById('result').innerText = "Error: " + err;
    });
</script>
@endsection
