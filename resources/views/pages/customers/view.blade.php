@push('style')


@endpush

@extends('layouts.admin')
@section('title', 'View Customer')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-3"> 
                      
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data Customer</strong> <a href="{{ route('customers.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Customer : </b>{{ $item->name }}</li>
                              <li class="list-group-item"><b>Alamat : </b>{{ $item->address }}</li>
                              <li class="list-group-item"><b>Telepon : </b>{{ $item->phone }}</li>
                              <li class="list-group-item"><b>Email :</b> {{ $item->email }}</li>
                              <li class="list-group-item"><b>Status :</b> 
                                @if(is_null($item->status))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status == 0)
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @elseif($item->status == 1)
                                        <span class="badge bg-primary">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Status Tidak Dikenal</span>
                                    @endif
                              </li>
                              {{-- <li class="list-group-item">Customer : {{ $item->customer->name ?? '-' }}</li> --}}
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('customers.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush