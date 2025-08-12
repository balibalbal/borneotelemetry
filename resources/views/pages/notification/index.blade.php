<style>
  #dataTable_processing {
    display: none !important;
}
</style>
@extends('layouts.admin')
@section('title', 'Pesan Notifikasi')
@section('content')
<div class="container-fluid">
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
    @endif

    <div class="col-lg-12 col-12">
      <div class="card mb-3">
        <div class="card-header header-elements">
          <div>
            <h5 class="card-title mb-0">
              Unduh Pesan
            </h5>
            <small class="text-muted">Pesan yang dapat di generate ke Excel maksimal 1 bulan</small>
          </div>                              
        </div>

        <div class="card-body">    
          <form class="mb-3 mt-3" action="{{ route('notification.export') }}" method="GET">
              <div class="row">
                  <div class="col-lg-5">
                    <div class="form-floating form-floating-outline mb-3">
                      <input
                                  type="date"
                                  id="start_date"
                                  class="form-control datepicker @error('start_date') is-invalid @enderror"
                                  name="start_date" value="{{ old('start_date') }}" />
                      <label for="start_date">Tanggal Awal</label>
                      @error('start_date')<div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="form-floating form-floating-outline">
                        <input
                        type="date"
                        id="end_date"
                        class="form-control datepicker @error('end_date') is-invalid @enderror"
                        aria-describedby="basic-icon-default-phone2"
                        name="end_date" value="{{ old('end_date') }}" />
                      <label for="end_date">Tanggal Akhir</label>
                      @error('end_date')<div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                  </div>
                  
                  <div class="col-lg-2">
                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-download-circle me-sm-1"></i> Unduh</button>
                  </div>
              </div>
          </form>
        </div>
      </div>
    </div>       

    <div class="card shadow mb-4">  
        <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal/Waktu</th>
                            <th>Nomor Polisi</th>
                            <th>Pesan</th>
                            <th>Status Pesan</th>
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

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  Apakah Anda yakin ingin menghapus data ini?
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                  <form id="deleteForm" method="POST" action="">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Ya</button>
                  </form>
              </div>
          </div>
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
              ajax: '/list-data-notification',
              
              columns: [
                  { data: 'id', name: 'id' },
                  { data: 'time', name: 'time' },
                  { data: 'no_pol', name: 'no_pol' },
                  { data: 'message', name: 'message' },
                  { data: 'status', name: 'status' },
                  { data: 'actions', name: 'actions', orderable: false, searchable: false }
              ]
          });
          
        });

    </script>
 @endpush