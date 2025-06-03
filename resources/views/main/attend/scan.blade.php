<!-- resources/views/scanner.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>QR Code Scanner</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Scan a QR Code</h2>
    <div id="reader" style="width: 300px;"></div>
    <p id="result"></p>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        function onScanSuccess(qrCodeMessage) {
            $('#result').text("Scanned: " + qrCodeMessage);

            // Send via AJAX to Laravel
            $.ajax({
                url: "{{ route('scan.code') }}",
                method: "POST",
                data: { code: qrCodeMessage },
                success: function(response) {
                    console.log("Success", response);
                },
                error: function(err) {
                    console.error("Error:", err);
                }
            });

            // Optionally stop scanning after first scan
            html5QrcodeScanner.clear();
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: 250
        });

        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
