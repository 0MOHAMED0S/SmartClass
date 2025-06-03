<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Code Scanner</title>
</head>
<body>
    <h2>Scan a Code</h2>
    <input type="text" id="code" placeholder="Scan code..." autofocus>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // AJAX setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Listen for scan or Enter key
        $('#code').on('keypress', function(e) {
            if (e.which == 13) { // Enter key
                let code = $(this).val();

                $.ajax({
                    url: "{{ route('scan.code') }}",
                    method: "POST",
                    data: { code: code },
                    success: function(response) {
                        // Optional: handle success if dd is not used
                        console.log(response);
                    }
                });
            }
        });
    </script>
</body>
</html>
