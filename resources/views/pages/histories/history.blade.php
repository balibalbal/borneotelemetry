

@push('style')
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    #dataTable_processing {
        display: none !important;
    }
  </style>
@endpush
  @extends('layouts.admin')
  @section('title', 'History Perjalanan')
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
                              <th>Tanggal/Waktu</th>
                              <th>ID Device</th>
                              <th>Nomor Polisi</th>
                              <th>Alamat</th>
                              <th>Vendor GPS</th>
                              <th>Status</th>
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
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
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
                ajax: '/list-data-history',
                
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'time', name: 'time' },
                    { data: 'device_id', name: 'device_id' },
                    { data: 'no_pol', name: 'no_pol' },
                    { data: 'address', name: 'address' },
                    { data: 'vendor_gps', name: 'vendor_gps' },
                    { data: 'status', name: 'status' },
                ]
            });
  
            
        });
  
      </script>
    
   @endpush