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
  @extends('layouts.admin')
  @section('title', 'Laporan Parkir Admin')
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
                            <h5>Laporan Parkir Kendaraan</h5><hr>
                            <form class="mb-0 mt-3" action="{{ route('report.downloadParkir') }}" method="GET" onsubmit="return validateDates()">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select name="customer" id="customer" class="form-select form-control @error('customer') is-invalid @enderror" data-allow-clear="true" required>
                                                <option value="">Pilih Customer</option>
                                                @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" data-no-pol="{{ $customer->id }}">
                                                    {{ $customer->name }} 
                                                </option>
                                                @endforeach
                                            </select>
                                            <label for="customer">Nama Customer</label>
                                            @error('customer')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select name="no_pol" id="no_pol" class="form-select form-control @error('no_pol') is-invalid @enderror" data-allow-clear="true" required>
                                                <option value="">Pilih Kendaraan</option>
                                                {{-- @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->no_pol }}" data-no-pol="{{ $vehicle->no_pol }}">
                                                    {{ $vehicle->no_pol }} 
                                                </option>
                                                @endforeach --}}
                                            </select>
                                            <label for="no_pol">Nomor Polisi</label>
                                            @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="date" id="start_date" class="form-control datepicker @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}" required/>
                                            <label for="start_date">Tanggal Awal</label>
                                            @error('start_date')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="date" id="end_date" class="form-control datepicker @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" required/>
                                            <label for="end_date">Tanggal Akhir</label>
                                            @error('end_date')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12 d-flex justify-content-between">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-download-circle me-sm-1"></i> Unduh</button>

                                        <button id="showButton" type="button" class="btn btn-warning">
                                            <i class="mdi mdi-eye me-sm-1"></i> Tampilkan
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
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"><thead>
                        <tr>
                            {{-- <th>Nama Customer</th> --}}
                            <th>Nopol</th>
                            <th>Waktu Mulai Parkir</th>
                            <th>Waktu Berakhir Parkir</th>
                            <th>Durasi Parkir</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                  </table>
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

            $('#customer').select2({
                allowClear: true,
                placeholder: 'Pilih Customer',
                width: '100%'
            });

            $('#no_pol').select2({
                allowClear: true,
                placeholder: 'Pilih Kendaraan',
                dropdownAutoWidth: true,
                width: '100%',
            });

            // Mengisi opsi nopol berdasarkan customer yang dipilih
            $('#customer').change(function() {
                var customerId = $(this).val();
                if (customerId) {
                    $.ajax({
                        url: '/get-vehicles/' + customerId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#no_pol').empty().append('<option value="">Pilih Kendaraan</option>');
                            $.each(data, function(index, vehicle) {
                                $('#no_pol').append('<option value="' + vehicle.no_pol + '">' + vehicle.no_pol + '</option>');
                            });
                            $('#no_pol').val(null).trigger('change'); // Clear selection
                            $('#no_pol').select2(); // Re-initialize Select2
                        }
                    });
                } else {
                    $('#no_pol').empty().append('<option value="">Pilih Kendaraan</option>').select2({
                        allowClear: true,
                        placeholder: 'Pilih Kendaraan',
                        width: '100%'
                    });
                }
            });

            
            $('#showButton').click(function() {
                if (validateDates()) {
                    // Tampilkan loading indicator
                    $('#dataTable_processing2').show();

                    // Jika DataTable sudah ada, reload data
                    if ($.fn.DataTable.isDataTable('#dataTable')) {
                        dataTable.ajax.reload(function() {
                            // Sembunyikan loading setelah data dimuat
                            $('#dataTable_processing2').hide();
                        }, false); // false agar tidak mengganti state pagination
                    } else {
                        // Inisialisasi DataTable jika belum ada
                        dataTable = $('#dataTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '/admin/datatable-parking',
                                type: 'GET',
                                data: function(d) {
                                    d.customer = $('#customer').val();
                                    d.no_pol = $('#no_pol').val();
                                    d.start_date = $('#start_date').val();
                                    d.end_date = $('#end_date').val();
                                },
                                dataSrc: function(json) {
                                    // Sembunyikan loading setelah data dimuat
                                    $('#dataTable_processing2').hide();
                                    return json.data; 
                                }
                            },
                            columns: [
                                // { data: 'customer_name', name: 'customer_name' },
                                { data: 'no_pol', name: 'no_pol' },
                                { data: 'start_time', name: 'start_time' },
                                { data: 'end_time', name: 'end_time' },
                                { data: 'durasi', name: 'durasi' },
                                { data: 'alamat', name: 'alamat' },
                            ]
                        });
                    }
                }
            });
            
        });
  
        // Fungsi untuk memformat angka menjadi format Indonesia
    function formatRupiah(value) {
            if (value === null || value === '') {
                return '0';
            }
            return parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function validateDates() {
            var startDateInput = document.getElementById("start_date");
            var endDateInput = document.getElementById("end_date");
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            var dateAlert = document.getElementById("dateAlert");
            var customer = document.getElementById("customer");
            var nopol = document.getElementById("no_pol");

            // Cek apakah customer kosong
            if (customer.value === "" ) {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Customer tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }

            // Cek apakah customer kosong
            if (nopol.value === "" ) {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Nopol tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
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