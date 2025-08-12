@extends('layouts.admin')
@section('title', 'Role')
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
        <div class="card-header py-3 d-flex justify-content-between">
            <div class="col-lg-12 col-sm-6">
                <div class="card h-100">
                  <div class="row">
                    <div class="col-6">
                      <div class="card-body">
                        <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">List Role</h5>
                          <div><small>Buat role untuk hak akses user</small> </div>
                        </div>
                        <div>
                            <a href="{{ route('assign_roles.index') }}" class="btn rounded-pill btn-danger waves-effect waves-light"><i class="mdi mdi-sync-circle me-sm-1"></i> Assign Role</a>
                            <a href="{{ route('roles.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Tambah Role</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-6 text-end d-flex align-items-end justify-content-center">
                      <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                        <img src="{{ url('backend/assets/img/illustrations/faq-illustration.png') }}" alt="Ratings" width="95">
                        <img src="{{ url('backend/assets/img/illustrations/card-orders-illustration.png') }}" alt="Ratings" width="81">
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
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->guard_name }}</td>
                                <td>
                                    {{-- <a href="{{ route('roles.show', $item->id) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon item-view"><i class="mdi mdi-eye-circle"></i></a> --}}
                                    <a href="{{ route('roles.edit', $item->id) }}" class="btn btn-icon btn-label-secondary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Edit Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-icon btn-label-danger waves-effect" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        data-url="{{ route('roles.destroy', $item->id) }}"
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus role ini?
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