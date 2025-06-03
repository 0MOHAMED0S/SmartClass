<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Scanner</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- html5-qrcode CDN -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">📷 Scan a QR Code</h2>

        <div id="reader" class="rounded overflow-hidden border border-gray-300"></div>

        <p id="result" class="mt-4 text-center text-green-600 font-semibold"></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        function onScanSuccess(qrCodeMessage) {
            $('#result').text("✅ Scanned: " + qrCodeMessage);

            $.ajax({
                url: "{{ route('scan.code') }}",
                method: "POST",
                data: { code: qrCodeMessage },
                success: function(response) {
                    console.log("Sent to server");
                },
                error: function(err) {
                    console.error("Error:", err);
                }
            });

            html5QrcodeScanner.clear(); // Stop scanning after success
        }

        const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
