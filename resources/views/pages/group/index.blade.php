@extends('layouts.admin')
@section('title', 'Group')
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
        <div class="card-header py-3 d-flex justify-content-end">
            @if (auth()->user()->hasPermissionTo('tambah-group')) 
            <a href="{{ route('groups.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Tambah Group</a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Group</th>
                            @if(auth()->user()->customer_id == 1)
                            <th>Nama Customer</th>
                            @endif
                            <th>Deskripsi Group</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                @if(auth()->user()->customer_id == 1)
                                <td>{{ $item->customer_name }}</td>
                                @endif
                                <td>{{ $item->description }}</td>
                                <td>
                                    @if (auth()->user()->hasPermissionTo('lihat-group'))
                                    <a href="{{ route('groups.show', $item->id) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon item-view"><i class="mdi mdi-eye-circle"></i></a>
                                    @endif
                                    @if (auth()->user()->hasPermissionTo('edit-group'))
                                    <a href="{{ route('groups.edit', $item->id) }}" class="btn btn-sm btn-text-dark rounded-pill btn-icon item-edit"><i class="mdi mdi-pencil"></i></a>
                                    @endif
                                    @if (auth()->user()->hasPermissionTo('hapus-group'))
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-sm btn-text-danger rounded-pill btn-icon item-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-url="{{ route('groups.destroy', $item->id) }}">
                                            <i class="mdi mdi-delete-circle me-sm-1"></i>
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
                    Apakah Anda yakin ingin menghapus group ini?
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