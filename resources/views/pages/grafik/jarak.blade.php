@push('style')
    <style>
        #dataTable_processing {
        display: none !important;
        }
        #no_pol + .select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }

        #customer + .select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }
    </style>
@endpush
  @extends('layouts.admin')
  @section('title', 'Laporan Jarak Tempuh')
  @section('content')
  <div class="container-fluid">
      @if(session('pesan'))
      <div class="alert alert-success alert-dismissible" role="alert">
          <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
      </div>
      @endif
  
      <div class="card shadow mb-4">  
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="col-lg-12 col-sm-6">
                <div class="card h-100">
                  <div class="row">
                    <div class="col-12">
                      <div class="card-body mb-0">
                        <div class="card-info mb-0 py-2 mb-lg-1 mb-xl-3">
                            <h5>Grafik Jarak Tempuh Kendaraan</h5><hr>
                            <form class="mb-0 mt-3" action="" method="GET" onsubmit="">
                                <div class="row">                                    
                                    <div class="col-lg-6">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="date" id="start_date" class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}" required/>
                                            <label for="start_date">Tanggal Awal</label>
                                            @error('start_date')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="date" id="end_date" class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" required/>
                                            <label for="end_date">Tanggal Akhir</label>
                                            @error('end_date')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>                                      
                                </div>
                                <div class="row">                                  
                                    <div class="col-lg-12 d-flex justify-content-between mb-3">
                                        <button id="graphButton" type="button" class="btn btn-success">
                                            <i class="mdi mdi-chart-bar me-sm-1"></i> Tampilkan Grafik Bar
                                        </button>

                                        <button id="graphLineButton" type="button" class="btn btn-dark">
                                            <i class="mdi mdi-chart-line me-sm-1"></i> Tampilkan Grafik Line
                                        </button>
                                    </div>
                                </div>
                                <!-- Notifikasi -->
                                <div id="dateAlert" class="alert alert-danger alert-dismissible d-none" role="alert">
                                  Tanggal awal dan tanggal akhir harus berada dalam bulan yang sama.
                                  <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>
                                </div>
                              </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>            
        </div>
          <div class="card-body">
            <div id="chartContainer" class="d-none mb-5">
                <canvas id="myChart"></canvas>
            </div>
          </div>          
        
          <div id="dataTable_processing2" class="dataTables_processing" style="width: 6rem;">
            <img class="card-img-top" src="/backend/assets/img/icons/mtrack-logo-animasi.gif" alt="Card image cap">
          </div>
      </div>
  </div>
  @endsection
  
  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
      <script>
        $(document).ready(function() {
            $('#dataTable_processing2').hide();
            
            // Inisialisasi Chart.js (grafik)
            var ctx = document.getElementById('myChart').getContext('2d');
            var chartInstance;

                // Tombol Grafik Bar
                $('#graphButton').click(function() {
                    if (validateDates()) {
                        // Tampilkan loading indicator
                        $('#dataTable_processing2').show();

                        // Ambil data dari server menggunakan AJAX
                        $.ajax({
                            url: '/grafik/distance', 
                            type: 'GET',
                            data: {
                                start_date: $('#start_date').val(),
                                end_date: $('#end_date').val()
                            },
                            success: function(response) {
                                // Sembunyikan loading setelah data dimuat
                                $('#dataTable_processing2').hide();

                                // Cek apakah data yang diterima ada
                                if (response.data && response.data.length > 0) {
                                    // Ambil data untuk grafik
                                    var labels = [];
                                    var distanceTodayData = [];
                                    var totalDistanceData = [];

                                    response.data.forEach(function(row) {
                                        labels.push(row.nopol);
                                        distanceTodayData.push(row.distance_today);
                                        totalDistanceData.push(row.total_distance);
                                    });

                                    // Tampilkan chart container
                                    $('#chartContainer').removeClass('d-none');

                                    // Hapus chart lama jika ada
                                    if (chartInstance) {
                                        chartInstance.destroy();
                                    }

                                    // Buat grafik dengan Chart.js
                                    chartInstance = new Chart(ctx, {
                                        type: 'bar',  // Ganti jenis grafik menjadi bar
                                        data: {
                                            labels: labels,  // Label per nopol
                                            datasets: [{
                                                label: 'Jarak Hari Ini (KM)',
                                                data: distanceTodayData,  // Jarak Hari Ini
                                                backgroundColor: 'rgba(75, 192, 192, 0.6)',  // Warna bar
                                                borderColor: 'rgba(75, 192, 192, 1)',  // Warna border
                                                borderWidth: 1,
                                                hoverBackgroundColor: 'rgba(75, 192, 192, 0.8)',  // Warna saat hover
                                                hoverBorderColor: 'rgba(75, 192, 192, 1)',  // Border saat hover
                                            },
                                            {
                                                label: 'Total Jarak (KM)',
                                                data: totalDistanceData,  // Total Jarak
                                                backgroundColor: 'rgba(153, 102, 255, 0.6)',  // Warna bar
                                                borderColor: 'rgba(153, 102, 255, 1)',  // Warna border
                                                borderWidth: 1,
                                                hoverBackgroundColor: 'rgba(153, 102, 255, 0.8)',  // Warna saat hover
                                                hoverBorderColor: 'rgba(153, 102, 255, 1)',  // Border saat hover
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        stepSize: 100  // Menentukan interval sumbu y
                                                    }
                                                }
                                            },
                                            plugins: {
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            var value = tooltipItem.raw;
                                                            return value.toLocaleString('id-ID') + ' KM';  // Format angka dalam format Indonesia
                                                        }
                                                    }
                                                },
                                                legend: {
                                                    display: true, // Menampilkan legend
                                                    position: 'top' // Posisi legend
                                                }
                                            }
                                        }
                                    });

                                } else {
                                    // Jika tidak ada data, sembunyikan chart
                                    $('#chartContainer').addClass('d-none');
                                    dateAlert.classList.remove("d-none");
                                    dateAlert.innerHTML = 'Tidak ada data untuk rentang tanggal ini. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                                    return false;
                                }
                            },
                            error: function() {
                                // Jika terjadi kesalahan, sembunyikan loading dan tampilkan pesan error
                                $('#dataTable_processing2').hide();
                                
                                dateAlert.classList.remove("d-none");
                                dateAlert.innerHTML = 'Terjadi kesalahan saat mengambil data grafik. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                                return false;
                            }
                        });
                    }
                });

                // Tombol Grafik Line
                $('#graphLineButton').click(function() {
                    if (validateDates()) {
                        // Tampilkan loading indicator
                        $('#dataTable_processing2').show();

                        // Ambil data dari server menggunakan AJAX
                        $.ajax({
                            url: '/grafik/distance',
                            type: 'GET',
                            data: {
                                start_date: $('#start_date').val(),
                                end_date: $('#end_date').val()
                            },
                            success: function(response) {
                                console.log(response);
                                // Sembunyikan loading setelah data dimuat
                                $('#dataTable_processing2').hide();

                                // Cek apakah data yang diterima ada
                                if (response.data && response.data.length > 0) {
                                    // Ambil data untuk grafik
                                    var labels = [];
                                    var distanceTodayData = [];
                                    var totalDistanceData = [];

                                    response.data.forEach(function(row) {
                                        labels.push(row.nopol);
                                        distanceTodayData.push(row.distance_today);
                                        totalDistanceData.push(row.total_distance);
                                    });

                                    // Tampilkan chart container
                                    $('#chartContainer').removeClass('d-none');

                                    // Hapus chart lama jika ada
                                    if (chartInstance) {
                                        chartInstance.destroy();
                                    }

                                    // Buat grafik Line dengan Chart.js
                                    chartInstance = new Chart(ctx, {
                                        type: 'line',  // Ganti jenis grafik menjadi line
                                        data: {
                                            labels: labels, // Label per nopol
                                            datasets: [{
                                                label: 'Jarak Hari Ini (KM)',
                                                data: distanceTodayData,  // Jarak Hari Ini
                                                backgroundColor: 'rgba(75, 192, 192, 0.2)',  // Warna latar belakang
                                                borderColor: 'rgba(75, 192, 192, 1)',  // Warna garis
                                                borderWidth: 2,
                                                fill: false, // Tidak isi area di bawah grafik
                                                tension: 0.1  // Membuat grafik lebih smooth
                                            },
                                            {
                                                label: 'Total Jarak (KM)',
                                                data: totalDistanceData,  // Total Jarak
                                                backgroundColor: 'rgba(153, 102, 255, 0.2)',  // Warna latar belakang
                                                borderColor: 'rgba(153, 102, 255, 1)',  // Warna garis
                                                borderWidth: 2,
                                                fill: false, // Tidak isi area di bawah grafik
                                                tension: 0.1  // Membuat grafik lebih smooth
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        stepSize: 100  // Menentukan interval sumbu y
                                                    }
                                                }
                                            },
                                            plugins: {
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            var value = context.raw;  // Ambil nilai tooltip
                                                            return value.toLocaleString('id-ID') + ' KM';  // Format dalam format IDR
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });


                                } else {
                                    // Jika tidak ada data, sembunyikan chart
                                    $('#chartContainer').addClass('d-none');
                                    dateAlert.classList.remove("d-none");
                                    dateAlert.innerHTML = 'Tidak ada data untuk nopol ini. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                                    return false;
                                }
                            },
                            error: function() {
                                // Jika terjadi kesalahan, sembunyikan loading dan tampilkan pesan error
                                $('#dataTable_processing2').hide();
                                
                                dateAlert.classList.remove("d-none");
                                dateAlert.innerHTML = 'Terjadi kesalahan saat mengambil data grafik. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                                return false;
                            }
                        });
                    }
                });
            
        });
       
        function validateDates() {
            var startDateInput = document.getElementById("start_date");
            var endDateInput = document.getElementById("end_date");
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            var dateAlert = document.getElementById("dateAlert");
            var customer = document.getElementById("customer");
    
            // Cek apakah input tanggal kosong
            if (startDateInput.value === "" || endDateInput.value === "") {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Tanggal awal dan tanggal akhir tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }
    
            // Cek apakah kedua tanggal berada di bulan yang sama
            if (startDate.getMonth() !== endDate.getMonth() || startDate.getFullYear() !== endDate.getFullYear()) {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Tanggal awal dan tanggal akhir harus berada dalam bulan yang sama. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
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