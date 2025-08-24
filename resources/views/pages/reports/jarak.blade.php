@push('style')
    <style>
        #dataTable_processing {
        display: none !important;
        }
        #no_pol + .select2-container .select2-selection--multiple {
            /* min-height: 40px; */
            padding: 10px;
        }
        #group_id + .select2-container .select2-selection--multiple {
            /* height: 45px; */
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
                            <h5>Laporan Jarak Tempuh Kendaraan</h5><hr>
                            <form class="mb-0 mt-3" action="{{ route('report.downloadJarak') }}" method="GET" onsubmit="return validateDates()">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                              <select name="group_id" id="group_id" class="select2 form-select form-control @error('group_id') is-invalid @enderror" data-allow-clear="true" required multiple>
                                                {{-- <option value="">Pilih Group</option> --}}
                                                <option value="1">Semua Group</option>
                                                @foreach($groups as $group)
                                                <option value="{{ $group->id }}" data-no-pol="{{ $group->name }}">
                                                    {{ $group->name }} 
                                                </option>
                                                @endforeach
                                              </select>                        
                                              <label for="group_id">Group</label>
                                              @error('group_id')<div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                          </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select name="no_pol[]" id="no_pol" class="select2 form-select form-control @error('no_pol') is-invalid @enderror" data-allow-clear="true" required multiple>
                                                
                                            </select>                                            
                                            <label for="no_pol">Nomor Polisi</label>
                                            @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>                          
                                </div>
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
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-download-circle me-sm-1"></i> Unduh Laporan Excel</button>
                                        
                                        <button id="showButton" type="button" class="btn btn-warning">
                                            <i class="mdi mdi-eye me-sm-1"></i> Tampilkan List
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
            <div id="tableContainer" class="d-none">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                          <tr>
                              <th>Nopol</th>
                              <th>Tanggal</th>
                              <th>Jarak Harian (KM)</th>
                          </tr>
                      </thead>
                      <tbody>
                        <!-- Data akan dimasukkan di sini -->
                     </tbody>
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
                placeholder: 'Pilih Nopol',
                dropdownAutoWidth: true,
                width: '100%',
                multiple: true,
                minimumResultsForSearch: -1
            });


            $('#group_id').select2({
                allowClear: true,
                placeholder: 'Pilih Group',
                dropdownAutoWidth: true,
                width: '100%',
                multiple: true,
            });
           
            // Mengatasi pemilihan "All"
            $('#no_pol').on('change', function() {
                var selectedValuesNopol = $(this).val(); // Ambil nilai yang dipilih
                console.log('Selected selectedValuesNopol:', selectedValuesNopol);

                if (selectedValuesNopol.includes('1')) {
                    // Jika "All" dipilih, nonaktifkan semua opsi selain "All"
                    $('#no_pol option').not('[value="1"]').prop('disabled', true);
                } else {
                    // Jika "All" tidak dipilih, aktifkan semua opsi
                    $('#no_pol option').prop('disabled', false);
                }

                // Jika selain "All" yang dipilih, maka "All" menjadi disable
                if (selectedValuesNopol.length > 1) {
                    $('#no_pol option[value="1"]').prop('disabled', true); // Nonaktifkan "All"
                } else {
                    $('#no_pol option[value="1"]').prop('disabled', false); // Aktifkan "All" jika tidak ada yang dipilih
                }

            });

            $('#group_id').on('change', function() {
                var selectedValuesGroup = $(this).val(); // Ambil nilai yang dipilih

                // console.log('Selected group_id:', selectedValuesGroup);

                // Reset dropdown no_pol sebelum mengubah pilihan baru
                $('#no_pol').empty();  // Kosongkan dropdown no_pol sebelum diisi ulang

                if (selectedValuesGroup) {
                    // Jika ada yang dipilih, aktifkan select2 untuk no_pol
                    $('#no_pol').prop('disabled', false);
                    console.log('Ada Selected group_id yg dipilih:', selectedValuesGroup);
                } else {
                    // Jika tidak ada yang dipilih, disable select2 untuk no_pol
                    $('#no_pol').prop('disabled', true);
                    console.log('tidak ada Selected group_id yang dipilih');
                }

                if (selectedValuesGroup && selectedValuesGroup.includes('1')) {
                    // Jika "All" dipilih, nonaktifkan semua opsi selain "All"
                    $('#group_id option').not('[value="1"]').prop('disabled', true);
                } else {
                    // Jika "All" tidak dipilih, aktifkan semua opsi
                    $('#group_id option').prop('disabled', false);
                }

                // Jika selain "All" yang dipilih, maka "All" menjadi disable
                if (selectedValuesGroup.length > 1) {
                    $('#group_id option[value="1"]').prop('disabled', true); // Nonaktifkan "All"
                } else {
                    $('#group_id option[value="1"]').prop('disabled', false); // Aktifkan "All" jika tidak ada yang dipilih
                }

                // Mengirim request AJAX jika ada group yang dipilih
                if (selectedValuesGroup && selectedValuesGroup.length > 0) {
                    $.ajax({
                        url: '/vehiclebygroup/' + selectedValuesGroup.join(','), // Kirim groupId sebagai array
                        method: 'GET',
                        success: function(data) {
                            $('#no_pol').append('<option value="1">Pilih Semua</option>');
                            // Tambahkan opsi kendaraan berdasarkan data yang diterima
                            $.each(data, function(index, vehicle) {
                                $('#no_pol').append('<option value="' + vehicle.id + '">' + vehicle.no_pol + '</option>');
                            });

                            // Refresh select2 agar update opsi baru
                            $('#no_pol').trigger('change');

                            console.log('Data kendaraan berhasil dimuat:', data);
                            console.log('Group ID:', selectedValuesGroup);
                        },
                        error: function(xhr, status, error) {
                            console.error('Terjadi kesalahan saat mengambil data kendaraan:', error);
                            console.error('Status:', status);
                            console.error('Response:', xhr.responseText);
                            alert('Terjadi kesalahan saat mengambil data kendaraan.');
                        }
                    });
                } else {
                    // Jika tidak ada group yang dipilih, disable no_pol
                    $('#no_pol').prop('disabled', true);
                }
            });


            $('#showButton').click(function() {
                if (validateDates()) {
                    // Tampilkan loading indicator
                    $('#dataTable_processing2').show();

                    $('#no_pol').prop('disabled', false);

                    var selectedNopol = $('#no_pol').val();
                    // console.log('nopol dipilih:', selectedNopol);

                    // Mengambil data melalui Ajax
                    $.ajax({
                        url: '/report/distance',
                        type: 'GET',
                        data: {
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                            no_pol: selectedNopol,
                            group_id: $('#group_id').val()
                        },
                        success: function(json) {
                            // Sembunyikan loading setelah data dimuat
                            $('#dataTable_processing2').hide();
                            $('#tableContainer').removeClass('d-none');

                            // Bersihkan isi tabel sebelumnya
                            $('#dataTable tbody').empty();

                            // Variabel untuk menghitung total jarak per no_pol
                            let currentNoPol = '';
                            let totalDistancePerNoPol = 0;
                            let rowSpan = 0; // Untuk menghitung berapa baris yang perlu digabungkan

                            // Loop untuk menambah baris baru di tabel
                            $.each(json.data, function(index, item) {
                                // Cek apakah no_pol berbeda dengan no_pol sebelumnya
                                if (item.no_pol !== currentNoPol) {
                                    // Jika no_pol berubah, dan totalDistancePerNoPol sudah dihitung, tampilkan total jarak per kendaraan sebelumnya
                                    if (currentNoPol !== '') {
                                        // Baris total jarak per no_pol
                                        var totalRow = $('<tr></tr>');
                                        totalRow.append('<td colspan="2" style="text-align: center; font-weight: bold;">TOTAL JARAK ' + currentNoPol + '</td>');
                                        totalRow.append('<td style="text-align: left; font-weight: bold;">' + formatRupiah(totalDistancePerNoPol) + '</td>');
                                        $('#dataTable tbody').append(totalRow);
                                    }

                                    // Reset total distance dan update no_pol yang sedang diproses
                                    currentNoPol = item.no_pol;
                                    totalDistancePerNoPol = 0;
                                    rowSpan = 1; // Set rowSpan untuk no_pol pertama
                                } else {
                                    // Jika no_pol sama, tambahkan rowSpan
                                    rowSpan++;
                                }

                                // Tambahkan baris data untuk tiap row
                                var row = $('<tr></tr>');

                                // Jika ini adalah baris pertama untuk no_pol, gabungkan kolom pertama
                                if (rowSpan === 1) {
                                    row.append('<td rowspan="' + rowSpan + '">' + item.no_pol + '</td>'); // Merge no_pol
                                } else {
                                    row.append('<td></td>'); // Kosongkan kolom no_pol untuk baris berikutnya
                                }

                                row.append('<td>' + item.date + '</td>');
                                row.append('<td>' + formatRupiah(item.total_distance) + '</td>');
                                $('#dataTable tbody').append(row);

                                // Tambahkan total distance untuk no_pol ini
                                totalDistancePerNoPol += item.total_distance;
                            });

                            // Menambahkan baris total jarak terakhir jika ada
                            if (currentNoPol !== '') {
                                var totalRow = $('<tr></tr>');
                                totalRow.append('<td colspan="2" style="text-align: center; font-weight: bold;">TOTAL JARAK ' + currentNoPol + '</td>');
                                totalRow.append('<td style="text-align: left; font-weight: bold;">' + formatRupiah(totalDistancePerNoPol) + '</td>');
                                $('#dataTable tbody').append(totalRow);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data:', error);
                            $('#dataTable_processing2').hide();
                        }
                    });
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
            var groupID = document.getElementById("group_id");
            var noPol = document.getElementById("no_pol");
            var startDate = new Date(startDateInput.value);
            var endDate = new Date(endDateInput.value);
            var dateAlert = document.getElementById("dateAlert");

            if (groupID.value === "") {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Group tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }

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