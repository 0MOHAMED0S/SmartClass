<!DOCTYPE html>
<html>
<head>
    <title>Simple QR Scanner Test</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>
    <h1>QR Scanner Test</h1>
    <div id="reader" style="width:300px;"></div>
    <div id="result" style="margin-top:20px; font-weight:bold;"></div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('result').innerText = "Scanned: " + decodedText;
        }

        const html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).catch(err => {
            document.getElementById('result').innerText = "Error: " + err;
        });
    </script>
</body>
</html>
