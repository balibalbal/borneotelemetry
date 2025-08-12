@push('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 90vh;
        }

        #vehicle_id+.select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }
    </style>
@endpush

@extends('layouts.admin')
@section('title', 'Grafik Kecepatan Kendaraan')
@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-header header-elements">
                <div>
                    <h5 class="card-title mb-0">Grafik Heatmap Vehicle</h5>
                    <small class="text-muted">Grafik yang dapat di-generate maksimal 1 bulan</small>
                </div>
            </div>

            <div class="card-body">
                <div class="card border border-primary shadow-sm">
                    <div class="card-body">
                        <div class="row">

                            <!-- Pilih Nopol -->
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select name="vehicle_id" id="vehicle_id" class="form-select">
                                        <option value="">Select Vehicle</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->no_pol }}</option>
                                        @endforeach
                                    </select>
                                    <label for="vehicle_id">Plat Number</label>
                                </div>
                            </div>

                            <!-- Tanggal Mulai -->
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" id="start_date" name="start_date" class="form-control" required
                                        value="{{ old('start_date') }}">
                                    <label id="label_start_date" for="start_date">Start Date</label>
                                </div>
                            </div>

                            <!-- Tanggal Akhir -->
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" id="end_date" name="end_date" class="form-control" required
                                        value="{{ old('end_date') }}">
                                    <label id="label_end_date" for="end_date">End Date</label>
                                </div>
                            </div>

                            <!-- Tombol Search -->
                            <div class="col-lg-2 mt-3">
                                <button class="btn rounded-pill btn-primary waves-effect waves-light" type="button"
                                    id="searchButton">
                                    <i class="mdi mdi-car-search me-sm-1"></i> Search
                                </button>
                            </div>

                            <!-- Alert Error -->
                            <div class="col-12">
                                <div id="dateAlert" class="alert alert-danger alert-dismissible d-none mt-3" role="alert">
                                    <button type="button" class="btn-close" aria-label="Close"
                                        onclick="closeAlert()"></button>
                                </div>
                            </div>

                        </div> <!-- row -->
                    </div> <!-- card-body -->
                </div>
            </div>
        </div>

        <div id="map"></div>
    </div>
@endsection

@push('scripts')
    <!-- Leaflet dan Heatmap Plugin -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Include SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#vehicle_id').select2({
                allowClear: true,
                placeholder: 'Select Vehicle',
                dropdownAutoWidth: true,
                width: '100%',
            });

            var map = L.map('map').setView([-6.2, 106.8], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Variabel untuk menyimpan heatmap layer
            var heat;

            // Event listener untuk tombol pencarian
            $('#searchButton').on('click', function() {
                // Disable tombol search untuk mencegah multiple klik
                $('#searchButton').prop('disabled', true);

                if (!validateDates()) {
                    $('#searchButton').prop('disabled', false);
                    return;
                }

                // Ambil nilai vehicle_id dari dropdown
                var vehicleId = $('#vehicle_id').val();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                // Validasi apakah vehicle_id telah dipilih
                if (!vehicleId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Plat Number Empty',
                        text: 'Plat number must be fill',
                    }).then(() => {
                        // Mengaktifkan kembali tombol jika user menutup alert
                        $('#searchButton').prop('disabled', false);
                    });
                    return;
                }

                // Bangun URL dengan query string untuk request AJAX
                var url = '/grafik/heatmap-per-vehicle?vehicle_id=' + vehicleId + '&start_date=' +
                    startDate + '&end_date=' + endDate;

                // Kirim request AJAX
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        // Cek jika data kosong
                        if (response.locations.length === 0) {                            
                            map.removeLayer(heat);

                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Not Found',
                                text: 'Data in range date not found',
                            }).then(() => {
                                // Mengaktifkan kembali tombol setelah alert kedua
                                $('#searchButton').prop('disabled', false);
                            });
                            return;
                        }

                        // Menghapus heatmap yang lama jika ada
                        if (heat) {
                            map.removeLayer(heat);
                        }

                        var heatData = response.locations.map(function(loc) {
                            return [loc.latitude, loc.longitude];
                        });

                        // Membuat heatmap layer
                        heat = L.heatLayer(heatData, {
                            radius: 25,
                            blur: 15,
                            maxZoom: 17,
                        }).addTo(map);

                        // Menghitung bounding box berdasarkan data lokasi heatmap
                        var bounds = L.latLngBounds(heatData);

                        // Fokus peta ke area yang di-cover oleh heatmap
                        map.fitBounds(bounds, {
                            padding: [50, 50]
                        });

                        // Mengaktifkan tombol kembali setelah data ditampilkan
                        $('#searchButton').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                        alert('Gagal mendapatkan data. Silakan coba lagi.');
                        $('#searchButton').prop('disabled', false);
                    }
                });
            });
        });

        function validateDates() {
            var startDateInput = document.getElementById("start_date");
            var endDateInput = document.getElementById("end_date");
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            var dateAlert = document.getElementById("dateAlert");

            // Cek apakah input tanggal kosong
            if (startDateInput.value === "" || endDateInput.value === "") {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML =
                    'Tanggal awal dan tanggal akhir tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }

            // Cek apakah kedua tanggal berada dalam rentang maksimal 31 hari
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffInTime = end.getTime() - start.getTime();
            const diffInDays = diffInTime / (1000 * 3600 * 24); // Konversi ms ke hari

            if (diffInDays > 31) {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML =
                    'Rentang tanggal tidak boleh lebih dari 31 hari. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }

            dateAlert.classList.add("d-none"); // Sembunyikan notifikasi jika tanggal valid
            return true; // Izinkan pengiriman formulir
        }

        function closeAlert() {
            var dateAlert = document.getElementById("dateAlert");
            dateAlert.classList.add("d-none"); // Sembunyikan notifikasi
        }

    </script>
@endpush
