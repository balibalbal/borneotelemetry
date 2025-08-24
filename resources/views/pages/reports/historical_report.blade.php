@push('style')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <style>
    #no_pol + .select2-container .select2-selection--single {
        height: 45px;
        padding: 10px;
    }
    #loading {
      display: none;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
@endpush

@extends('layouts.admin')
@section('title', 'Laporan Historical')

@section('content')
<div class="container-fluid">

  @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row">
    <div class="col-lg-12 col-12">
      <div class="card mb-3">
        <div class="card-header header-elements">
          <div>
            <h5 class="card-title mb-0">Laporan Historical</h5>
            <small class="text-muted">Laporan yang dapat di-generate ke Excel maksimal 1 bulan</small>
          </div>
        </div>

        <div class="card-body">
          <form action="{{ route('report.downloadHistorical') }}" method="GET" onsubmit="return validateDates()">
            <div class="card border border-primary shadow-sm">
              <div class="card-body">
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

                  <!-- Tombol Unduh -->
                  <div class="col-lg-2 mt-3">
                    <button class="btn btn-primary w-100" type="submit">
                      <i class="mdi mdi-download-circle me-sm-1"></i> Unduh
                    </button>
                  </div>

                  <!-- Tombol Preview -->
                  <div class="col-lg-2 mt-3">
                    <button class="btn btn-dark w-100" type="button" onclick="handlePreview()">
                      <i class="mdi mdi-eye-outline me-sm-1"></i> Tampilkan
                    </button>
                  </div>

                  <!-- Alert Error -->
                  <div class="col-12">
                    <div id="dateAlert" class="alert alert-danger alert-dismissible d-none mt-3" role="alert">
                      <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>
                    </div>
                  </div>

                </div> <!-- row -->
              </div> <!-- card-body -->
            </div> <!-- card -->
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- PREVIEW TABLE -->
  <div class="row">
    <div id="previewTableContainer" class="mt-2 mb-4 d-none">
      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover" id="previewTable">
            <thead>
              <tr id="previewHead"></tr>
            </thead>
            <tbody id="previewBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Spinner -->
  <div class="row">
    <div id="loading">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>

</div>

  
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {    
        $('#loading').hide();
         $('#no_pol').select2({
                allowClear: true,
                placeholder: 'Pilih Kendaraan',
                dropdownAutoWidth: true,
                width: '100%',
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
  

  function handlePreview() {
    if (validateDates()) {
        const params = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            no_pol: $('#no_pol').val()
        };

        // Reset konten & tampilkan loading
        $('#previewTableContainer').addClass('d-none');
        $('#previewHead, #previewBody').html('');
        $('#loading').show();

        $.ajax({
        url: '/report-historical',
        method: 'GET',
        data: params,
        success: function(res) {
            $('#loading').hide();

            if (res.success && Array.isArray(res.data) && res.data.length > 0) {
            const filtered = res.data.filter(row => Object.keys(row).length > 0);
            if (filtered.length === 0) {
                Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Data tidak ditemukan.',
                confirmButtonColor: '#3085d6',
                });
                return;
            }

            const firstValid = filtered[0];
            let headers = Object.keys(firstValid);
        
            // Tambah map_link di akhir
            headers.push('map_link');

            const labelMapping = {
                time: 'Tanggal',
                no_pol: 'Nopol',
                course: 'Angle/Arah',
                ignition_status: 'Igniton',
                speed: 'Kecepatan',
                map_link: 'Google Maps',
                address: 'Lokasi'
            };

            if ($.fn.DataTable.isDataTable('#previewTable')) {
                $('#previewTable').DataTable().clear().destroy();
            }

            $('#previewHead').html(
                headers.map(h => `<th>${labelMapping[h] || h}</th>`).join('')
            );

            $('#previewBody').html(
                filtered.map(row => {
                return '<tr>' +
                    headers.map(key => {
                        let value = row[key] ?? '';
                        let tdStyle = '';

                        // Status badge
                        if (key === 'status') {
                            let badgeClass = 'bg-secondary';
                            let text = 'Tidak Diketahui';
                            switch (value) {
                                case 'bergerak':
                                    badgeClass = 'bg-success';
                                    text = 'Bergerak';
                                    break;
                                case 'mati':
                                    badgeClass = 'bg-danger';
                                    text = 'Mati';
                                    break;
                                case 'berhenti':
                                    badgeClass = 'bg-warning';
                                    text = 'Berhenti';
                                    break;
                                case 'diam':
                                    badgeClass = 'bg-dark';
                                    text = 'Diam';
                                    break;
                            }
                            value = `<span class="badge ${badgeClass}">${text}</span>`;
                        }

                        // Kolom map_link
                        if (key === 'map_link') {
                            const lat = row.latitude;
                            const lon = row.longitude;
                            if (lat && lon) {
                                value = `<a href="https://www.google.com/maps?q=${lat},${lon}" target="_blank"><i class="mdi mdi-google-maps me-sm-1"></i></a>`;
                            } else {
                                value = '-';
                            }
                            tdStyle = 'style="text-align:center;"';
                        }

                        return `<td ${tdStyle}>${value}</td>`;
                    }).join('') +
                    '</tr>';

                }).join('')
            );

            $('#previewTableContainer').removeClass('d-none');

            // Inisialisasi atau reinit DataTable
            $('#previewTable').DataTable({
                //destroy: true, // penting agar bisa dipanggil ulang
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel',
                {
                    extend: 'pdf',
                    text: 'PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Laporan Historical',
                    customize: function (doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.styles.tableHeader.alignment = 'left';
                    }
                },
                'print'
                ],
                responsive: false,
                scrollX: true,
                pageLength: 25,
                ordering: false,
                language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: { previous: "Sebelumnya", next: "Berikutnya" }
                }
            });

            } else {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Data tidak ditemukan.',
                confirmButtonColor: '#3085d6',
            });

            }
        },
        error: function() {
            $('#loading').hide();
            alert('Terjadi kesalahan saat memuat data.');
        }
        });
    }
  }


</script>

@endpush









