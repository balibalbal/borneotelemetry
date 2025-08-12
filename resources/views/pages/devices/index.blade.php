@extends('layouts.admin')
@section('title', 'List Device')
@section('content')
<div class="container-fluid">
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
    @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            
            <h5>Daftar Device</h5>
            {{-- @if(auth()->user()->customer_id == 1)
                <form action="{{ route('devices.updateData') }}" method="post">
                    @csrf
                    <button type="submit" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-sync-circle me-sm-1"></i> Sycn Data GPS</button>
                </form>
                <form action="{{ route('devices.updateDataLogin') }}" method="post">
                    @csrf
                    <button type="submit" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-sync-circle me-sm-1"></i> Sycn Data Login</button>
                </form>
            @endif --}}
            @if (auth()->user()->hasPermissionTo('tambah-device'))
                <a href="{{ route('devices.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Tambah Device</a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Device</th>
                            <th>ID Kendaraan</th>
                            <th>Nomor Polisi</th>
                            <th>Imei</th>
                            <th>Nomor Telepon</th>
                            <th>Status</th>
                            @if(auth()->user()->customer_id == 1)
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->vehicle_id }}</td>
                                <td>{{ $item->vehicle->no_pol ?? '-' }}</td>
                                <td>{{ $item->imei }}</td> 
                                <td>{{ $item->sim_number }}</td> 
                                <td>
                                    @if(is_null($item->status))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status == 0)
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @elseif($item->status == 1)
                                        <span class="badge bg-primary">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Status Tidak Dikenal</span>
                                    @endif
                                </td>
                                @if(auth()->user()->customer_id == 1)
                                <td>
                                    {{-- <a href="{{ route('devices.show', $item->id) }}" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Data">
                                        <span class="tf-icons mdi mdi-eye-outline"></span>
                                    </a> --}}
                                    <a href="{{ route('devices.edit', $item->id) }}" class="btn btn-icon btn-label-dark waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-icon btn-label-danger waves-effect"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteModal" 
                                            data-url="{{ route('devices.destroy', $item->id) }}"
                                            data-bs-placement="top"
                                            title="Hapus Data">
                                            <span class="tf-icons mdi mdi-delete"></span>
                                        </button>
                                    </form>
                                </td>                                    
                                @endif                    
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
                    Apakah Anda yakin ingin menghapus device ini?
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
            "order": [[0, 'desc']]
        });
        });
    </script>
 @endpush




