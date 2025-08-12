<!DOCTYPE html>
<html>
<head>
    <title>SSE Example</title>
    <style>
        #data {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Server-Sent Events (SSE) Example</h1>
    <div id="data">Waiting for updates...</div>

    <script>
        if (typeof(EventSource) !== "undefined") {
            const source = new EventSource('{{ url("/events") }}');

            source.onmessage = function(event) {
                const data = JSON.parse(event.data);

                document.getElementById('data').innerHTML = `
                    <strong>IMEI:</strong> ${data.imei} <br>
                    <strong>Alarm Type:</strong> ${data.alarmType} <br>
                    <strong>Timestamp:</strong> ${data.updated_at}
                `;
            };

            source.onerror = function(error) {
                console.error('SSE Error:', error);
                document.getElementById('data').innerHTML = "Error receiving updates.";
            };
        } else {
            document.getElementById('data').innerHTML = "Sorry, your browser does not support SSE.";
        }
    </script>
</body>
</html>
