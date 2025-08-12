@extends('layouts.admin')
@section('title', 'View User')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data User</strong> <a href="{{ route('users.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nama User :</b> {{ $item->name }}</li>
                              <li class="list-group-item"><b>Email :</b> {{ $item->email }}</li>
                              {{-- <li class="list-group-item"><b>Area :</b> {{ $item->area->name ?? '-' }}</li>
                              <li class="list-group-item"><b>Jabatan :</b> {{ $item->jabatan }}</li> --}}
                              <li class="list-group-item"><b>Akses Device :</b> @if($item->akses == 0)
                                <span class="badge bg-danger">All</span>
                                @elseif($item->akses == 1)
                                <span class="badge bg-info">Web</span>   
                                @elseif($item->akses == 2)                  
                                <span class="badge bg-dark">Mobile</span>         
                                @endif</li>
                              <li class="list-group-item"><b>Status :</b> @if($item->status == 0)
                                <span class="badge bg-danger">Tidak Aktif</span>
                                @elseif($item->status == 1)
                                <span class="badge bg-primary">Aktif</span>   
                                @else                    
                                <span class="badge bg-warning">Tidak Ada Status</span>         
                                @endif</li>
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            @if (auth()->user()->hasPermissionTo('edit-user'))
                <a href="{{ route('users.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
            @endif
        </div>
    </div>
</div>

@endsection