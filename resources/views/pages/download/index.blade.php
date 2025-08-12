<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download APK</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('backend/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            padding: 20px;
            background: #0e4159;
            color: white;
        }
        .download-list {
            max-width: 600px;
            margin: auto;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #0e4159;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ url('backend/img/logo_mototrack3.png') }}" alt="Logo Mototrack" class="img-fluid" style="max-height: 100px;">
        {{-- <h2 style="color: yellow;">Mototrack by Mtrack</h2> --}}

        {{-- <p>Mototrack adalah</p> --}}
    </div>

    <div class="container mt-4">
        <h4 class="text-center mb-3">Download APK Mototrack</h4>
        <ul class="list-group download-list">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Mototrack Versi 1.0.0
                <a href="{{ url('backend/apk/mototrack-final-release.apk') }}" class="btn btn-primary btn-sm">Download</a>
            </li>
        </ul>
    </div>

    <div class="container mt-5">
        <h4>Cara Menggunakan APK Mototrack</h4><br>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">1. Download file APK yang terbaru.</li>
            <li class="list-group-item">2. Buka file APK yang sudah diunduh.</li>
            <li class="list-group-item">3. Jika muncul peringatan keamanan, izinkan instalasi dari sumber tidak dikenal.</li>
            <li class="list-group-item">4. Tunggu proses instalasi selesai.</li>
            <li class="list-group-item">5. Sebelum menggunakan aplikasi, silahkan registrasi dulu data anda untuk mendapatkan akun login</li>
            <li class="list-group-item">6. Hubungi customer service mototrack untuk pembuatan akun</li>
            <li class="list-group-item">7. Jika sudah memiliki akun, silahkan login dengan akun yang sudah ada</li>
            <li class="list-group-item">8. Kini kamu bisa memantau setiap pergerakan sepeda motormu bahkan mengontrolnya dari jarak jauh sekalipun</li>
        </ul>
    </div>

    <div class="footer">
        <p>&copy; 2025 Mototrack by Mtrack</p>
    </div>
</body>
</html>
