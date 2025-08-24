@push('style')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <style>
    #group_id + .select2-container .select2-selection--multiple {
            /* height: 45px; */
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
@section('title', 'Laporan Posisi Akhir')

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
            <h5 class="card-title mb-0">Laporan Posisi Akhir</h5>
            {{-- <small class="text-muted">Laporan yang dapat di-generate ke Excel maksimal 1 bulan</small> --}}
          </div>
        </div>

        <div class="card-body">
          <form action="{{ route('report.exportLastPosition') }}" method="GET" onsubmit="return validateDates()">
            <div class="card border border-primary shadow-sm">
              <div class="card-body">
                <div class="row">

                  <div class="col-lg-12">
                        <div class="input-group input-group-merge mb-4">
                            <div class="form-floating form-floating-outline">
                                <select name="group_id[]" id="group_id" class="select2 form-select form-control @error('group_id') is-invalid @enderror" data-allow-clear="true" required multiple>
                                                {{-- <option value="">Pilih Group</option> --}}
                                                <option value="0">Semua Group</option>
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
        $('#group_id').select2({
                    allowClear: true,
                    placeholder: 'Pilih Group',
                    dropdownAutoWidth: true,
                    width: '100%',
                    multiple: true,
                });

                $('#group_id').on('change', function() {
                    var selectedValuesGroup = $(this).val(); // Ambil nilai yang dipilih

                    // console.log('Selected group_id:', selectedValuesGroup);

                    if (selectedValuesGroup && selectedValuesGroup.includes('1')) {
                        // Jika "All" dipilih, nonaktifkan semua opsi selain "All"
                        $('#group_id option').not('[value="0"]').prop('disabled', true);
                    } else {
                        // Jika "All" tidak dipilih, aktifkan semua opsi
                        $('#group_id option').prop('disabled', false);
                    }

                    // Jika selain "All" yang dipilih, maka "All" menjadi disable
                    if (selectedValuesGroup.length > 1) {
                        $('#group_id option[value="0"]').prop('disabled', true); // Nonaktifkan "All"
                    } else {
                        $('#group_id option[value="0"]').prop('disabled', false); // Aktifkan "All" jika tidak ada yang dipilih
                    }
                });

        $('form').on('submit', function() {
            // Pastikan group_id dikirim sebagai array
            if ($('#group_id').val() && !Array.isArray($('#group_id').val())) {
            $('#group_id').val([$('#group_id').val()]);
            }
        });
    });

  function validateGroup() {
            var groupID = document.getElementById("group_id");
            var dateAlert = document.getElementById("dateAlert");

            if (groupID.value === "") {
                dateAlert.classList.remove("d-none"); // Tampilkan notifikasi
                dateAlert.innerHTML = 'Group tidak boleh kosong. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
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
    if (validateGroup()) {
        const params = {
        group_id: $('#group_id').val(),
        };

        // Reset konten & tampilkan loading
        $('#previewTableContainer').addClass('d-none');
        $('#previewHead, #previewBody').html('');
        $('#loading').show();

        $.ajax({
        url: '/report-last-position',
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

            // Sisipkan 'update_time' setelah 'time'
            const timeIndex = headers.indexOf('time');
            if (timeIndex !== -1) {
            headers.splice(timeIndex + 1, 0, 'update_time');
            }

            // Tambah map_link di akhir
            headers.push('map_link');

            const labelMapping = {
                time: 'Tanggal',
                update_time: 'Update',
                no_pol: 'Nopol',
                speed: 'Kecepatan',
                course: 'Angle/Arah',
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

                        // Kolom waktu relatif
                        if (key === 'update_time') {
                            const now = new Date();
                            const updateTime = new Date(row['time']);
                            const diffMs = now - updateTime;
                            const diffHours = diffMs / (1000 * 60 * 60);

                            let text = '';
                            if (diffMs < 60 * 1000) {
                                text = `${Math.floor(diffMs / 1000)} detik yang lalu`;
                            } else if (diffMs < 3600 * 1000) {
                                text = `${Math.floor(diffMs / (1000 * 60))} menit yang lalu`;
                            } else if (diffMs < 86400 * 1000) {
                                text = `${Math.floor(diffMs / (1000 * 60 * 60))} jam yang lalu`;
                            } else {
                                text = `${Math.floor(diffMs / (1000 * 60 * 60 * 24))} hari yang lalu`;
                            }

                            let bgColor = '';
                            if (diffHours >= 24) {
                                bgColor = 'background-color: red; color: white;';
                            } else if (diffHours >= 12) {
                                bgColor = 'background-color: yellow; color: black;';
                            }

                            value = text;
                            tdStyle = `style="text-align:center; ${bgColor}"`;
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
                    title: 'Laporan Posisi Akhir',
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









