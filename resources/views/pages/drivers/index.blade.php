@extends('layouts.admin')
@section('title', 'List Supir')
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
            {{-- <a href="{{ route('drivers.export') }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-file-document-outline"></i> Export Ke Excel</a> --}}
            <a href="{{ route('drivers.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Tambah Supir</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>KTP</th>
                            <th>Tipe SIM</th>
                            <th>Nomor SIM</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($drivers as $driver )
                            <tr>
                                <td>{{ $driver->driver_code }}</td>
                                <td>{{ $driver->name }}</td>
                                <td>
                                    @if($driver->photo_ktp == '')
                                        <img src="{{ url('backend/img/ktp.png') }}" class="img-fluid" style="width: 70px">
                                    @else
                                        <img src="{{ url('storage/' . $driver->photo_ktp) }}" class="img-fluid" style="width: 70px">
                                    @endif                                    
                                </td>
                                <td>{{ $driver->sim_type }}</td>
                                <td>{{ $driver->sim_number }}</td>      
                                <td>@if($driver->status == 0)
                                    <span class="badge rounded-pill  bg-label-danger">Tidak Aktif</span>
                                    @else
                                    <span class="badge rounded-pill  bg-label-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Data">
                                        <span class="tf-icons mdi mdi-eye-outline"></span>
                                    </a>
                                    <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-icon btn-label-dark waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Ubah Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-icon btn-label-danger waves-effect"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteModal" 
                                            data-url="{{ route('drivers.destroy', $driver->id) }}"
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
                    Apakah Anda yakin ingin menghapus supir ini?
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
        $('#dataTable').DataTable();
        });
    </script>
 @endpush