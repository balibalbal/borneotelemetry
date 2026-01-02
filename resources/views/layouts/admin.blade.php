<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="{{ url('ltr') }}"
  data-theme="{{ url('theme-default') }}"
  data-assets-path="{{ url('backend/assets/') }}"
  data-template="{{ url('vertical-menu-template') }}">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Borneotelemetry - @yield('title', 'Dashboard')</title>
    

    <meta name="description" content="" />
    @include('includes.style')
    
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <!-- Navbar -->
        @include('includes.navbar')
        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="main-content">
          <!-- Content wrapper -->
              @yield('content')
            {{-- </div> --}}
            <!--/ Content -->

            <!-- Footer -->
            @include('includes.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          {{-- </div> --}}
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    @include('includes.script')
  </body>
  {{-- @vite('resources/js/app.js') --}}

  <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    
  <script>
    // Mendapatkan customer_id dari pengguna saat ini
    // window.customerId = @json(auth()->user()->customer_id);

    // Menyimpan status interaksi pengguna
    let isUserInteracted = false;

    // Menunggu interaksi pengguna pertama kali
    document.addEventListener('click', function() {
      isUserInteracted = true;
    });

    document.addEventListener('DOMContentLoaded', function() {
      // const customerId = window.customerId; // Ambil customer_id dari variabel global
      const socket = io('wss://socket.speedtrack.id'); // Koneksi WebSocket

      // Ketika menerima data dari WebSocket
      socket.on('message', (data) => {
          // Ini untuk notifikasi roll
          @if(auth()->user()->settings['roll'] ?? false)
            // Ambil informasi dari data
            if (data.notifikasi == 'location' && data.roll < 3) {
              // const roll = data.roll;
              const roll = Math.round(data.roll);
              const no_pol = data.no_pol;
              const fixTime = data.fixTime;
              
              // Cek jika pengguna sudah aktifkan suara, baru putar audio
              @if(auth()->user()->settings['suara'] ?? false)
                if (isUserInteracted) {
                  const audio = new Audio('/backend/assets/audio/alarm.mp3');
                  audio.play().catch(error => {
                    console.error('Error playing audio:', error);
                  });
                }
              @endif

              // Membuat toast HTML
              const toastHtml = `
                <div class="bs-toast toast fade show toast-custom" role="alert" aria-live="assertive" aria-atomic="true"
                    style="position: fixed; bottom: 200px; right: 20px; z-index: 1050; background-color: #FBFFE4; color: grey;">
                  <div class="toast-header d-flex align-items-center justify-content-between w-100" style="background-color: #B22222; color: white;">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-car-traction-control text-white me-2"></i>
                        <strong class="me-3">Kemiringan</strong>
                    </div>

                    <div class="d-flex align-items-center">
                        <small class="text-white fw-bold fs-6 me-2">${no_pol}</small>
                        <button type="button" class="btn text-white p-0" data-bs-dismiss="toast" aria-label="Close">
                            <i class="mdi mdi-close fs-5"></i>
                        </button>
                    </div>
                </div>

                  <div class="toast-body">
                      Tanggal : ${fixTime}<br>
                      Kemiringan : ${roll} derajat
                  </div>
                </div>
              `;

              // Menambahkan toast ke body
              document.body.insertAdjacentHTML('beforeend', toastHtml);

              // Menampilkan toast menggunakan Bootstrap Toast
              const toastElements = document.querySelectorAll('.toast');
              toastElements.forEach(toast => {
                const toastInstance = new bootstrap.Toast(toast);
                toastInstance.show();
              });
            }
          @endif 
      });

      // Handle jika gagal terhubung
      socket.on('connect_error', (error) => {
        console.error('WebSocket connection failed: ', error);
      });
    });
  </script>
</html>
