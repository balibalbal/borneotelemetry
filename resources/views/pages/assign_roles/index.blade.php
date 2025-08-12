@extends('layouts.admin')
@section('title', 'Assign Role')
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
        <div class="card-header mt-3 d-flex justify-content-center">
            <div class="col-lg-12 col-sm-6">
                <div class="card h-100">
                  <div class="row">
                    <div class="col-6">
                      <div class="card-body">
                        <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">List Role User</h5>
                          <div><small>Pengaturan akses user sesuai kebutuhan</small> </div>
                        </div>
                        <div>
                            @if(auth()->user()->customer_id == 163)
                            <a href="{{ route('permissions.index') }}" class="btn rounded-pill btn-danger waves-effect waves-light"><i class="mdi mdi-sync-circle me-sm-1"></i> Assign Hak Akses</a>
                            <a href="{{ route('roles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left me-sm-1"></i> Kembali ke Role</a>
                            @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-6 text-end d-flex align-items-end justify-content-center">
                      <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                        <img src="{{ url('backend/assets/img/illustrations/card-ratings-illustration.png') }}" alt="Ratings" width="95">
                        <img src="{{ url('backend/assets/img/illustrations/card-session-illustration.png') }}" alt="Ratings" width="81">
                        <img src="{{ url('backend/assets/img/illustrations/card-customers-illustration.png') }}" alt="Ratings" width="81">
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
                            <th>Nama User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>@foreach ($item->getRoleNames() as $role)
                                    <span class="badge bg-primary">{{ $role }}</span>
                                @endforeach</td>
                                <td>
                                    {{-- <a href="{{ route('assign_roles.show', $item->id) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon item-view"><i class="mdi mdi-eye-circle"></i></a> --}}
                                    <a href="{{ route('assign_roles.edit', $item->id) }}" class="btn btn-sm btn-text-dark rounded-pill btn-icon item-edit"><i class="mdi mdi-pencil"></i></a>
                                    
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-sm btn-text-danger rounded-pill btn-icon item-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-url="{{ route('assign_roles.destroy', $item->id) }}">
                                            <i class="mdi mdi-delete-circle me-sm-1"></i>
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
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus role user ini?
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