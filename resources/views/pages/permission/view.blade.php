@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data User Role</strong> <a href="{{ route('assign_roles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nama User :</b> {{ $item->name }}</li>
                              <li class="list-group-item"><b>Email :</b> {{ $item->email }}</li>
                              <li class="list-group-item"><b>Area :</b> {{ $item->area->name ?? '-' }}</li>
                              <li class="list-group-item"><b>Role :</b> @foreach ($item->getRoleNames() as $role)
                                                                            <span class="badge bg-primary">{{ $role }}</span>
                                                                        @endforeach</li>
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('assign_roles.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
        </div>
    </div>
</div>

@endsection