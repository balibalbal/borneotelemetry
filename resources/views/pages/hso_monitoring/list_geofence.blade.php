@extends('layouts.admin')
@section('title', 'Geofence HSO')
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
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Informasi Pengiriman Data Geofence</h5>
                          <div><small>List ini untuk memantau proses pengiriman data geofence ke Sistem HSO</small> </div>
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
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Geofence</th>
                            <th>Nopol</th>
                            <th>Enter Time</th>
                            <th>Exit Time</th>
                            <th>Durasi</th>
                            <th>Status Geofence</th>
                            <th>Status Kirim</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->geofence_name }}</td>
                                <td>{{ $item->no_pol }}</td>
                                <td>{{ $item->time_entered }}</td>
                                <td>{{ $item->time_exited }}</td>
                                <td>{{ $item->duration }}</td>
                                <td>
                                    @if(is_null($item->status_geofence))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status_geofence == 2)
                                        <span class="badge bg-success">Exit</span>
                                    @elseif($item->status_geofence == 1)
                                        <span class="badge bg-primary">Enter</span>
                                    @else
                                        <span class="badge bg-secondary">Status Tidak Dikenal</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($item->status_kirim))
                                        <span class="badge bg-warning">Belum di kirim</span>
                                    @elseif($item->status_kirim == 2)
                                        <span class="badge bg-success">Fence Exit Terkirim</span>
                                    @elseif($item->status_kirim == 1)
                                        <span class="badge bg-primary">Fence Enter Terkirim</span>
                                    @elseif($item->status_kirim == 3)
                                        <span class="badge bg-danger">Gagal Kirim</span>
                                    @elseif($item->status_kirim == 0)
                                        <span class="badge bg-info">Belum di kirim</span>
                                    @endif
                                </td>
                                <td style="width: 15%">
                                    <a href="{{ route('histories_geofence.show', $item->id) }}" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Data">
                                        <span class="tf-icons mdi mdi-eye-outline"></span>
                                    </a>
                                    <a href="{{ route('histories_geofence.edit', $item->id) }}" class="btn btn-icon btn-label-secondary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Edit Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" 
                                        class="btn btn-icon btn-label-danger waves-effect" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        data-url="{{ route('histories_geofence.destroy', $item->id) }}"
                                        data-bs-placement="top"
                                        title="Hapus Data">
                                        <span class="tf-icons mdi mdi-delete"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                    Apakah Anda yakin ingin menghapus geofence ini?
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