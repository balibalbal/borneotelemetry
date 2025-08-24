@push('style')
  <style>
        .status-red {
            background-color: #fd051a !important;
            color: #faf7f7 !important;
        }

        .status-yellow {
            background-color: #ffc70d !important;
            color: #fffefc !important;
        }

        .status-default {
            background-color: #ffffff;
        }
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
@section('title', 'Laporan Posisi Kendaraan')
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
                            <h5>Laporan Posisi Akhir Kendaraan</h5><hr>
                            <form class="mb-0 mt-3" action="{{ route('report.exportLastPosition') }}" method="GET" onsubmit="return validateDates()">
                                <div class="row">
                                    <div class="col-lg-12">
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
                                <th>Update</th>
                                <th>Nopol</th>
                                {{-- <th>Latitude</th>
                                <th>Longitude</th> --}}
                                <th>Speed (Km/h)</th>
                                <th>Distance (KM)</th>
                                <th>No. Tlp</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Maps</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            {{-- @foreach($items as $item)
                            @php
                                $now = \Carbon\Carbon::now();
                                $time = \Carbon\Carbon::parse($item->time);
                                $diffInHours = $time->diffInHours($now);
                                $statusClass = 'status-default'; // Kelas default
                    
                                if ($diffInHours > 24) {
                                    $statusClass = 'status-red'; // Lebih dari 24 jam
                                } elseif ($diffInHours > 12) {
                                    $statusClass = 'status-yellow'; // Lebih dari 12 jam
                                }
                            @endphp
                            <tr>
                                <td>{{ $item->time }}</td>
                                <td class="{{ $statusClass }}">{{ $item->time_diff }}</td>
                                <td>{{ $item->no_pol }}</td>
                                <td>{{ $item->speed }} </td>
                                <td>{{ $item->total_distance }} </td>
                                <td>{{ $item->sim_number }} </td>
                                <td>{{ $item->address }}</td>
                                <td>
                                    @if($item->status == 'bergerak')
                                        <span class="badge bg-success">Bergerak</span>
                                    @elseif($item->status == 'mati')
                                        <span class="badge bg-danger">Mati</span>
                                    @elseif($item->status == 'diam')
                                        <span class="badge bg-dark">Diam</span>
                                    @elseif($item->status == 'berhenti')
                                        <span class="badge bg-warning">Berhenti</span>
                                    @endif
                                </td>                            
                                <td>
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn btn-icon btn-label-success waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Di Google Map">
                                        <span class="tf-icons mdi mdi-google-maps"></span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach --}}
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
        $(document).ready(function () {
            $('#dataTable_processing2').hide();

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
            });
           
            $('#showButton').click(function () {
                if (validateGroup()) {
                    $('#dataTable_processing2').show();

                    $.ajax({
                        url: '/report-last-position',
                        type: 'GET',
                        data: {
                            group_id: $('#group_id').val()
                        },
                        success: function (response) {
                            console.log(response);

                            $('#dataTable_processing2').hide();
                            $('#tableContainer').removeClass('d-none');

                            var dataTable = $('#dataTable').DataTable();
                            dataTable.clear().draw();

                            response.data.forEach(function (item, index) {
                                let mapsLink = `<a href="https://www.google.com/maps?q=${item.latitude},${item.longitude}" target="_blank"><i class="mdi mdi-google-maps me-sm-1"></i></a>`;

                                // **Tambahkan badge untuk status**
                                let statusBadge = '';
                                if (item.status === 'bergerak') {
                                    statusBadge = `<span class="badge bg-success">Bergerak</span>`;
                                } else if (item.status === 'diam') {
                                    statusBadge = `<span class="badge bg-dark">Diam</span>`;
                                } else if (item.status === 'mati') {
                                    statusBadge = `<span class="badge bg-danger">Mati</span>`;
                                } else if (item.status === 'berhenti') {
                                    statusBadge = `<span class="badge bg-warning text-dark">Berhenti</span>`;
                                }
                                
                                let newRow = dataTable.row.add([
                                    index + 1, // Nomor urut
                                    item.time,
                                    item.time_diff, // Kolom selisih waktu
                                    item.no_pol,
                                    item.speed,
                                    item.total_distance,
                                    item.sim_number,
                                    item.address,
                                    statusBadge,
                                    mapsLink
                                ]).draw(false).node(); // Dapatkan elemen <tr>

                                // Simpan diff_hours sebagai atribut data di <tr>
                                $(newRow).attr('data-diff-hours', item.diff_hours);
                            });

                            // **Ubah warna setelah DataTable selesai menggambar ulang tabel**
                            dataTable.draw();
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching data:', error);
                            $('#dataTable_processing2').hide();
                        }
                    });
                }
            });

            // **Gunakan drawCallback untuk mewarnai kolom time_diff**
            $('#dataTable').DataTable({
                drawCallback: function () {
                    $('#dataTable tbody tr').each(function () {
                        let diffHours = $(this).attr('data-diff-hours');

                        if (diffHours > 24) {
                            $(this).find('td:eq(2)').css('background-color', 'red').css('color', 'white');
                        } else if (diffHours > 12) {
                            $(this).find('td:eq(2)').css('background-color', 'orange').css('color', 'white');
                        }
                    });
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
    </script>
 @endpush