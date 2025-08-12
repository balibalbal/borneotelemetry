@extends('layouts.admin')
@section('title', 'User')
@section('content')
<div class="container-fluid">
    @if(session('pesan'))    
    <div class="alert alert-success alert-dismissible" role="alert">
        <h4 class="alert-heading d-flex align-items-center">
          <i class="mdi mdi-check-circle-outline mdi-24px me-2"></i>Well done :)
        </h4>
        <hr>
        <p class="mb-0">
            <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    
    <div class="card shadow mb-4">
        @if (auth()->user()->hasPermissionTo('tambah-user'))
        <div class="card-header py-3 d-flex justify-content-end">
            <div class="col-lg-12 col-sm-6">
                <div class="card h-100">
                  <div class="row">
                    <div class="col-6">
                      <div class="card-body">
                        <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Tambah & Ubah Data User</h5>
                          <div><small>Pengaturan untuk tambah dan ubah data user</small> </div>
                        </div>
                        <div>              
                            <a href="{{ route('assign_roles.index') }}" class="btn rounded-pill btn-danger waves-effect waves-light"><i class="mdi mdi-sync-circle me-sm-1"></i> Assign Role User</a>
                            @if (auth()->user()->hasPermissionTo('tambah-user'))
                                <a href="{{ route('users.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Tambah User</a>          
                            @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-6 text-end d-flex align-items-end justify-content-center">
                      <div class="card-body pb-0 pt-3 position-absolute bottom-0"><img src="{{ url('backend/assets/img/illustrations/misc-not-authorized-illustration.png') }}" alt="Ratings" width="65">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
        @endif
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Customer</th>
                            <th>Status</th>
                            {{-- <th>Akses</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->customer->name ?? '-' }}</td>
                                <td>@if($item->status == 0)
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                    @elseif($item->status == 1)
                                    <span class="badge bg-primary">Aktif</span>   
                                    @else                    
                                    <span class="badge bg-warning">Tidak Ada Status</span>         
                                    @endif</td>
                                {{-- <td>@if($item->akses == 0)
                                    <span class="badge bg-success">All</span>
                                    @elseif($item->akses == 1)
                                    <span class="badge bg-info">Web</span>   
                                    @elseif($item->akses == 2)                   
                                    <span class="badge bg-dark">Mobile</span>         
                                    @endif</td> --}}
                                <td>
                                    <a href="{{ route('users.show', $item->id) }}" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Data">
                                        <span class="tf-icons mdi mdi-eye-outline"></span>
                                    </a>
                                    @if (auth()->user()->hasPermissionTo('edit-user'))
                                    <a href="{{ route('users.edit', $item->id) }}" class="btn btn-icon btn-label-secondary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Edit Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    @endif

                                    @if (auth()->user()->hasPermissionTo('hapus-user'))
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-icon btn-label-danger waves-effect" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        data-url="{{ route('users.destroy', $item->id) }}"
                                        data-bs-placement="top"
                                        title="Hapus Data">
                                        <span class="tf-icons mdi mdi-delete"></span>
                                        </button> 
                                    </form>
                                    @endif
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus user ini?
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