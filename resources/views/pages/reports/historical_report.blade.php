<style>
    #dataTable_processing {
      display: none !important;
    }
    #no_pol + .select2-container .select2-selection--single {
        height: 45px;
        padding: 10px;
    }
  </style>
  @extends('layouts.admin')
  @section('title', 'Historical Report')
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
                            <h5>Laporan Historical Perjalanan Kendaraan</h5><hr>
                            <form class="mb-0 mt-3" action="{{ route('report.downloadHistorical') }}" method="GET" onsubmit="return validateDates()">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select name="no_pol" id="no_pol" class="form-select form-control @error('no_pol') is-invalid @enderror" data-allow-clear="true">
                                                <option value="">Pilih Kendaraan</option>
                                                @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}" data-no-pol="{{ $vehicle->id }}">
                                                    {{ $vehicle->no_pol }} 
                                                </option>
                                                @endforeach
                                            </select>
                                            <label for="no_pol">Nomor Polisi</label>
                                            @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="date" id="start_date" class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}" required/>
                                            <label for="start_date">Tanggal Awal</label>
                                            @error('start_date')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="date" id="end_date" class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" required/>
                                            <label for="end_date">Tanggal Akhir</label>
                                            @error('end_date')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">                                 
                                    <div class="col-lg-12 d-flex justify-content-between mb-0 mt-3">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-download-circle me-sm-1"></i> Unduh Laporan Excel</button>
                                        
                                        <button id="showButton" type="button" class="btn btn-warning">
                                            <i class="mdi mdi-eye me-sm-1"></i> Tampilkan List
                                        </button>
                                    </div>
                                </div>
                                <!-- Notifikasi -->
                                <div id="dateAlert" class="alert alert-danger alert-dismissible d-none mt-4" role="alert">
                                  Tanggal awal dan tanggal akhir harus berada dalam bulan yang sama.
                                  <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>
                                </div>
                              </form>
                        </div>
                      </div>
                    </div>
                    {{-- <div class="col-4 text-end d-flex align-items-end justify-content-center">
                      <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                        <img src="{{ url('backend/assets/img/illustrations/card-ratings-illustration.png') }}" alt="Assign Order" width="125">
                      </div>
                    </div> --}}
                  </div>
                </div>
            </div>            
        </div>
          <div class="card-body">
            <div id="tableContainer" class="d-none">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                          <tr>
                              <th>No</th>
                              <th>Tanggal</th>
                              <th>Nopol</th>
                              <th>Latitude</th>
                              <th>Longitude</th>
                              <th>Speed(Km/h)</th>
                              <th>Distance (KM)</th>
                              <th>Alamat</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                  </table>
              </div>
            </div>
          </div>
          <div id="dataTable_processing2" class="dataTables_processing" style="width: 6rem;">
            <img class="card-img-top" src="/backend/assets/img/icons/mtrack-logo-animasi.gif" alt="Card image cap">
          </div>
      </div>
  </div>
  @endsection
  
  @push('scripts')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
      <script>
          $(document).ready(function() {
            $('#dataTable_processing2').hide();

            $('#no_pol').select2({
                allowClear: true,
                placeholder: 'Pilih Kendaraan',
                dropdownAutoWidth: true,
                width: '100%',
            });

            $('#showButton').click(function () {
                if (validateDates()) {
                    // Tampilkan loading indicator
                    $('#dataTable_processing2').show();

                    $.ajax({
                        url: '/report-historical',
                        type: 'GET',
                        data: {
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                            no_pol: $('#no_pol').val()
                        },
                        success: function (response) {
                            console.log(response);
                            
                            // Sembunyikan loading setelah data dimuat
                            $('#dataTable_processing2').hide();

                            // Tampilkan tabel
                            $('#tableContainer').removeClass('d-none');

                            // Hapus data lama di tabel
                            var dataTable = $('#dataTable').DataTable();
                            dataTable.clear().draw();

                            // Tambahkan data baru
                            response.data.forEach(function (item, index) {
                                dataTable.row.add([
                                    index + 1, // Nomor urut
                                    item.time,
                                    item.no_pol,
                                    item.latitude,
                                    item.longitude,
                                    item.speed,
                                    item.distance,
                                    // formatDateTime(item.start_time),
                                    // formatDateTime(item.end_time),
                                    item.address,
                                    item.status
                                ]).draw(false);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching data:', error);
                            $('#dataTable_processing2').hide();
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
            var noPol = document.getElementById("no_pol");
            var dateAlert = document.getElementById("dateAlert");
    
            if (noPol.value === "") {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Nomor polisi tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }

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