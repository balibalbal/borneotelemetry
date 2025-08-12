<style>
    #dataTable_processing {
      display: none !important;
  }
  </style>
  @extends('layouts.admin')
  @section('title', 'List Event')
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
                    <div class="col-8">
                      <div class="card-body">
                        <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Informasi Kondisi Modem</h5>
                          <div><small>List ini untuk memantau kondisi Modem terkini</small> </div>
                        </div>
                        <div>              
                            {{-- <a href="/devices" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-router-wireless me-sm-1"></i> List Device</a>    --}}
                            <a href="/traccars" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-map-marker-distance me-sm-1"></i> Kembali Ke Monitoring Kendaraan</a>   
                            {{-- <a href="/list-traccars" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-truck-flatbed me-sm-1"></i> List Traccar</a>    --}}
                        </div>
                      </div>
                    </div>
                    <div class="col-4 text-end d-flex align-items-end justify-content-center">
                      <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                        <img src="{{ url('backend/assets/img/illustrations/card-ratings-illustration.png') }}" alt="Assign Order" width="125">
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
                              {{-- <th>ID</th> --}}
                              <th>Nopol</th>
                              {{-- <th>Imei</th> --}}
                              <th>Ignition</th>
                              <th>Charging</th>
                              <th>Alarm</th>
                              <th>Voltage Level</th>
                              <th>Sinyal GSM</th>
                              {{-- <th>Relay</th> --}}
                              <th>GPS Tracking</th>
                              <th>Update</th>
                              {{-- <th style="width: 15%">Aksi</th> --}}
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
      <script>
          $(document).ready(function() {
            // Kode tambahan untuk menangani proses loading
            $('#dataTable').on('processing.dt', function (e, settings, processing) {
                  if (processing) {
                      $('#dataTable_processing2').show();
                  } else {
                      $('#dataTable_processing2').hide();
                  }
              });
  
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/list-data-information',
                
                columns: [
                    // { data: 'id', name: 'id' },
                    { data: 'no_pol', name: 'no_pol' },
                    // { data: 'imei', name: 'imei' },
                    { 
                        data: 'ignition', 
                        name: 'ignition',
                        render: function(data, type, full, meta) {
                            var badge = '';
                            switch (data) {
                                case 'true':
                                    badge = '<span class="badge rounded bg-success">On</span>';
                                    break;
                                case 'false':
                                    badge = '<span class="badge rounded bg-danger">Off</span>';
                                    break;
                                default:
                                    badge = 'Unknown status';
                            }
                            return badge;
                        }
                    },
                    { 
                        data: 'charging', 
                        name: 'charging',
                        render: function(data, type, full, meta) {
                            var badge = '';
                            switch (data) {
                                case 'true':
                                    badge = '<span class="badge rounded bg-primary">Ya</span>';
                                    break;
                                case 'false':
                                    badge = '<span class="badge rounded bg-danger">Tidak</span>';
                                    break;
                                default:
                                    badge = 'Unknown status';
                            }
                            return badge;
                        }
                    },
                    { data: 'alarmType', name: 'alarmType' },
                    { data: 'voltageLevel', name: 'voltageLevel' },
                    { data: 'gsmSigStrength', name: 'gsmSigStrength' },
                    // { data: 'relayState', name: 'relayState' },
                    { 
                        data: 'gpsTracking', 
                        name: 'gpsTracking',
                        render: function(data, type, full, meta) {
                            var badge = '';
                            switch (data) {
                                case 'true':
                                    badge = '<span class="badge rounded bg-success">Ya</span>';
                                    break;
                                case 'false':
                                    badge = '<span class="badge rounded bg-danger">Tidak</span>';
                                    break;
                                default:
                                    badge = 'Unknown status';
                            }
                            return badge;
                        }
                    },
                    // { data: 'updated_at', name: 'updated_at' },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        render: function (data, type, row) {
                            // Jika data adalah string ISO 8601, konversikan ke format lokal yang diinginkan
                            if (type === 'display' && data) {
                                var date = new Date(data);
                                
                                // Format tanggal Indonesia
                                var day = String(date.getDate()).padStart(2, '0');
                                var month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
                                var year = date.getFullYear();
                                
                                // Format waktu
                                var hours = String(date.getHours()).padStart(2, '0');
                                var minutes = String(date.getMinutes()).padStart(2, '0');
                                var seconds = String(date.getSeconds()).padStart(2, '0');
                                
                                return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
                            }
                            return data; // Untuk tipe selain 'display', kembalikan data asli
                        }
                    }


                    
                    // { 
                    //     data: 'status', 
                    //     name: 'status',
                    //     render: function(data, type, full, meta) {
                    //         var statusText = '';
                    //         switch (parseInt(data)) {
                    //             case 0:
                    //             statusText = '<span class="badge rounded bg-danger">Menunggu Assign</span>';
                    //                 break;
                    //             case 1:
                    //                 statusText = '<span class="badge rounded bg-primary">Menuju Depo</span>';
                    //                 break;
                    //             case 2:
                    //                 statusText = '<span class="badge rounded  bg-warning">Tiba di Depo</span>';
                    //                 break;
                    //             case 3:
                    //                 statusText = '<span class="badge rounded  bg-success">OTW</span>';
                    //                 break;
                    //             case 4:
                    //                 statusText = '<span class="badge rounded  bg-dark">Di Customer</span>';
                    //                 break;
                    //             case 5:
                    //                 statusText = '<span class="badge rounded  bg-info">Back</span>';
                    //                 break;
                    //             case 7:
                    //                 statusText = '<span class="badge rounded  bg-secondary">Menunggu Antrian</span>';
                    //                 break;
                    //             default:
                    //                 statusText = 'Status tidak valid';
                    //         }
                    //         return statusText;
                    //     }
                    // },
                    // { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ]
            });  
            
        });
  
      </script>
   @endpush