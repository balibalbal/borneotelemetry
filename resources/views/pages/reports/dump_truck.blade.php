@push('style')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <style>
    #driver_id + .select2-container .select2-selection--single {
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
@section('title', 'Laporan Dump Truck')

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
            <h5 class="card-title mb-0">Laporan Dump Truck</h5>
            <small class="text-muted">Laporan yang dapat di-generate ke Excel maksimal 1 bulan</small>
          </div>
        </div>

        <div class="card-body">
          <form action="{{ route('report.downloadDumpReport') }}" method="GET" onsubmit="return validateDates()">
            <div class="card border border-primary shadow-sm">
              <div class="card-body">
                <div class="row">

                  <!-- Tanggal Mulai -->
                  <div class="col-lg-4">
                    <div class="form-floating form-floating-outline mb-3">
                      <input type="date" id="start_date" name="start_date" class="form-control" required value="{{ old('start_date') }}">
                      <label id="label_start_date" for="start_date">Tanggal Transfer Awal</label>
                    </div>
                  </div>

                  <!-- Tanggal Akhir -->
                  <div class="col-lg-4">
                    <div class="form-floating form-floating-outline mb-3">
                      <input type="date" id="end_date" name="end_date" class="form-control" required value="{{ old('end_date') }}">
                      <label id="label_end_date" for="end_date">Tanggal Transfer Akhir</label>
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
          dateAlert.innerHTML = 'Tanggal awal dan tanggal akhir tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
          return false; // Cegah pengiriman formulir
      }

      // Cek apakah kedua tanggal berada dalam rentang maksimal 31 hari
      const start = new Date(startDate);
      const end = new Date(endDate);
      const diffInTime = end.getTime() - start.getTime();
      const diffInDays = diffInTime / (1000 * 3600 * 24); // Konversi ms ke hari

      if (diffInDays > 31) {
          dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
          dateAlert.innerHTML = 'Rentang tanggal tidak boleh lebih dari 31 hari. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
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
    if (!validateDates()) return;

    const params = {
      start_date: $('#start_date').val(),
      end_date: $('#end_date').val(),
    };

    // Reset konten & tampilkan loading
    $('#previewTableContainer').addClass('d-none');
    $('#previewHead, #previewBody').html('');
    $('#loading').show();

    $.ajax({
      url: "{{ route('report.previewDumpReport') }}",
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
          const headers = Object.keys(firstValid);

          const labelMapping = {
            created_at: 'Tanggal',
            // nama_customer: 'Nama Customer',
            // da_si: 'DA/SI',
            no_pol: 'Nopol',
            door_status: 'Dump Status',
            // nama_supir: 'Nama Supir',
            // in_customer: 'Jam Masuk',
            // pt: 'PT',
            // insentif: 'Insentif',
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
                headers.map(key => `<td>${row[key] ?? ''}</td>`).join('') +
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
                title: 'Mtrack - Laporan Insentif',
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


</script>

@endpush









