<style>
    #dataTable_processing {
      display: none !important;
  }
  </style>
  @extends('layouts.admin')
  @section('title', 'List Traccar')
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
          <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                          <tr>
                              <th>ID</th>
                              <th>Nomor Polisi</th>
                              <th>Geofence</th>
                              <th>Nama Geofence</th>
                              <th>Waktu Masuk</th>
                              <th>Waktu Keluar</th>
                              <th style="width: 15%">Aksi</th>
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
                ajax: '/list-data-traccar',
                
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'no_pol', name: 'no_pol' },
                    { data: 'geofence', name: 'geofence' },
                    { data: 'geofence_name', name: 'geofence_name' },
                    { data: 'enter_time', name: 'enter_time' },
                    { data: 'out_time', name: 'out_time' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ]
            });
  
            
        });
  
      </script>
   @endpush