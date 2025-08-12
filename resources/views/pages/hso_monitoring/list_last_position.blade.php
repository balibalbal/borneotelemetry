@extends('layouts.admin')
@section('title', 'Posisi Akhir Kendaraan HSO')
@section('content')
<div class="container-fluid">    
    {{-- <div class="card shadow mb-3"> 
        <div id="map" style="height: 400px;"></div>               
    </div> --}}
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
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Informasi Log File Pengiriman Data</h5>
                          <div><small>List ini untuk memantau proses pengiriman data tracking posisi akhir ke Sistem HSO</small> </div>
                        </div>
                        <div>              
                            {{-- <a href="/devices" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-router-wireless me-sm-1"></i> List Device</a>    --}}
                            <a href="/hso" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-map-marker-distance me-sm-1"></i> Kembali Ke Monitoring Kendaraan</a>   
                            {{-- <a href="/list-traccars" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-truck-flatbed me-sm-1"></i> List Traccar</a>    --}}
                        </div>
                      </div>
                    </div>
                    <div class="col-4 text-end d-flex align-items-end justify-content-center">
                      <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                        <img src="{{ url('backend/assets/img/illustrations/card-ratings-illustration.png') }}" alt="transmission" width="125">
                      </div>
                    </div>
                    
                  </div>
                </div>
            </div>            
        </div>       
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-xl-4 col-md-4 col-xs-6 col-sm-6 mb-4">
                    <a href="" class="text-white">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">                  
                                <b>File Log HSO Tracking : {{ $logSizeKB }} KB</b>                  
                            {{-- <div class="text-white small" id="total-mati-count"><i class="mdi mdi-truck-remove"></i> </div> --}}
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-xl-4 col-md-4 col-xs-6 col-sm-6 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">                  
                                <b>File Log Sistem Mtrack : {{ $logSizeKB2 }} KB</b>                  
                            {{-- <div class="text-white small" id="total-mati-count"><i class="mdi mdi-truck-remove"></i> </div> --}}
                        </div>
                    </div>
                </div>                
                <div class="col-xl-4 col-md-4 col-xs-6 col-sm-6 mb-4">
                    <div class="card bg-dark text-white shadow">
                        <div class="card-body">                  
                                <b>File Log Login dan GPS : {{ $logSizeKB3 }} KB</b>                  
                            {{-- <div class="text-white small" id="total-mati-count"><i class="mdi mdi-truck-remove"></i> </div> --}}
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5>Isi File Log HSO Tracking Terbaru</h5>
            <div class="col-xl-6 col-md-6 col-xs-6 col-sm-6mb-4">
                <form action="{{ route('log.delete') }}" method="POST">
                    @csrf
                    <button class="btn rounded-pill btn-danger waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Hapus Log</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($logs as $log)
                    <li class="list-group-item">
                        {{ $log }}
                    </li>
                @endforeach
            </ul>
        </div>                
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5>Isi File Log Pengiriman Data Ke Server Login dan GPS</h5>
            <div class="col-xl-6 col-md-6 col-xs-6 col-sm-6 mb-2">
                <form action="{{ route('log.deleteGpsLogin') }}" method="POST">
                    @csrf
                    <button class="btn rounded-pill btn-danger waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Hapus Log</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($logGpsLogins as $logGpsLogin)
                    <li class="list-group-item">
                        {{ $logGpsLogin }}
                    </li>
                @endforeach
            </ul>
        </div>                
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5>Isi File Log Sistem Terbaru</h5>
            <div class="col-xl-6 col-md-6 col-xs-6 col-sm-6 mb-2">
                <form action="{{ route('log.deleteSistem') }}" method="POST">
                    @csrf
                    <button class="btn rounded-pill btn-danger waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Hapus Log</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($logLaravels as $logsistem)
                    <li class="list-group-item">
                        {{ $logsistem }}
                    </li>
                @endforeach
            </ul>
        </div>                
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data parkir ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                order: [[0, 'desc']] // 0 adalah index kolom id (kolom pertama)
            });
        });
    </script>
    
 @endpush