@extends('layouts.admin')
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
                          <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">List Hak Akses Role</h5>
                          <div><small>Pengaturan hak akses untuk role sesuai kebutuhan</small> </div>
                        </div>
                        <div>
                            <a href="{{ route('assign_roles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left me-sm-1"></i> Kembali ke List Role</a>
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
                            <th>Nama Role</th>
                            <th>Hak Akses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>@foreach ($item->getPermissionNames() as $role)
                                    <span class="badge bg-primary">{{ $role }}</span>
                                @endforeach</td>
                                <td style="width: 12%">
                                    {{-- <a href="{{ route('permissions.show', $item->id) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon item-view"><i class="mdi mdi-eye-circle"></i></a> --}}
                                    <a href="{{ route('permissions.edit', $item->id) }}" class="btn btn-icon btn-label-secondary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Edit Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-icon btn-label-danger waves-effect" 
                                        data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" 
                                        data-url="{{ route('permissions.destroy', $item->id) }}"
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
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus Hak Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus hak akses ini?
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