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
            background-color: #ffffff !important;
        }
        #customer + .select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }
        #dataTable_processing {
            display: none !important;
        }
  </style>
@endpush

@extends('layouts.admin')
@section('title', 'Laporan Posisi Kendaraan Untuk Admin')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="col-lg-12 col-sm-6">
                <div class="card h-100">
                  <div class="row">
                    <div class="col-12">
                      <div class="card-body mb-0">
                        <div class="card-info mb-0 py-2 mb-lg-1 mb-xl-3">
                            <h5>Laporan Posisi Akhir</h5><hr>
                            <form class="mb-0 mt-3" action="{{ route('report.exportLastPositionAdmin') }}" method="GET" onsubmit="return validateCustomer()">
                                <div class="row">
                                    <div class="col-lg-4">
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
                                                                                                            
                                    <div class="col-lg-4">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-download-circle me-sm-1"></i> Unduh</button>
                                    
                                        <button id="showButton" type="button" class="btn btn-warning">
                                            <i class="mdi mdi-eye me-sm-1"></i> Tampilkan
                                        </button>
                                    </div>
                                </div>
                                <!-- Notifikasi -->
                                <div id="customerAlert" class="alert alert-danger alert-dismissible d-none" role="alert">
                                  Silahkan pilih customer dahulu
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
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Update</th>
                            <th>Nopol</th>
                            <th>Kecepatan (Km/h)</th>
                            <th>Jarak (KM)</th>
                            <th>No. Tlp</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Maps</th>
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
        $(document).ready(function () {
            $('#dataTable_processing2').hide();
            $('#customer').select2({
                allowClear: true,
                placeholder: 'Pilih Customer',
                dropdownAutoWidth: true,
                width: '100%',
            });

            // Inisialisasi DataTable tanpa memuat data otomatis
            var dataTable; // Deklarasi variabel di luar

            $('#showButton').click(function() {
                if (validateCustomer()) {
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
                                url: '/admin/report-last-position',
                                type: 'GET',
                                data: function(d) {
                                    d.customer = $('#customer').val();
                                },
                                dataSrc: function(json) {
                                    // Sembunyikan loading setelah data dimuat
                                    $('#dataTable_processing2').hide();
                                    return json.data; 
                                }
                            },
                            columns: [
                                { data: 'time', name: 'time' },
                                {
                                    data: 'time_diff', 
                                    name: 'time_diff',
                                    render: function(data, type, row) {
                                        return '<span>' + data + '</span>';
                                    }
                                },
                                { data: 'no_pol', name: 'no_pol' },
                                { data: 'speed', name: 'speed' },
                                { data: 'total_distance', name: 'total_distance', 
                                    render: function(data) {
                                        return formatRupiah(data);
                                    }
                                },
                                { data: 'sim_number', name: 'sim_number' },
                                { data: 'address', name: 'address' },
                                {
                                    data: 'status',
                                    name: 'status',
                                    render: function(data) {
                                        let statusText = '';
                                        switch (data) {
                                            case 'mati':
                                                statusText = '<span class="badge rounded bg-danger">Mati</span>';
                                                break;
                                            case 'berhenti':
                                                statusText = '<span class="badge rounded bg-warning">Berhenti</span>';
                                                break;
                                            case 'bergerak':
                                                statusText = '<span class="badge rounded bg-success">Bergerak</span>';
                                                break;
                                            case 'diam':
                                                statusText = '<span class="badge rounded bg-dark">Diam</span>';
                                                break;
                                            default:
                                                statusText = '<span class="badge rounded bg-secondary">Status tidak valid</span>';
                                        }
                                        return statusText;
                                    }
                                },
                                {
                                    data: 'latitude',  // Menggunakan latitude untuk membuat link
                                    name: 'latitude',
                                    render: function(data, type, row) {
                                        // Ambil latitude dan longitude dari baris yang sama
                                        let latitude = data;
                                        let longitude = row.longitude;
                                        
                                        // Membuat URL Google Maps
                                        let googleMapsUrl = `https://www.google.com/maps?q=${latitude},${longitude}`;
                                        
                                        // Menampilkan link Google Maps
                                        return `<a href="${googleMapsUrl}" target="_blank" class="btn btn-icon btn-label-success waves-effect"><span class="tf-icons mdi mdi-google-maps"></span></a>`;
                                    }
                                }
                            ],
                            createdRow: function(row, data, dataIndex) {
                                // Tambahkan kelas CSS berdasarkan nilai time_diff_class
                                if (data.time_diff_class) {
                                    $('td', row).eq(1).addClass(data.time_diff_class);
                                }
                            }
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
    </script>

    <script>
        function validateCustomer() {
            var customer = document.getElementById("customer");
            var customerAlert = document.getElementById("customerAlert");
    
            // Cek apakah input tanggal kosong
            if (customer.value === "") {
                customerAlert.classList.remove("d-none"); // Tampilkan notifikasi
                customerAlert.innerHTML = 'Silahkan pilih customer dahulu. <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>';
                return false; // Cegah pengiriman formulir
            }
    
                
            customerAlert.classList.add("d-none"); // Sembunyikan notifikasi jika customer valid
            return true; // Izinkan pengiriman formulir
        }
    
        function closeAlert() {
            var customerAlert = document.getElementById("customerAlert");
            customerAlert.classList.add("d-none"); // Sembunyikan notifikasi
        }
    </script>
 @endpush