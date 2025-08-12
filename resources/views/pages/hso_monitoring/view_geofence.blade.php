@push('style')


@endpush

@extends('layouts.admin')
@section('title', 'View Parkir')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-3"> 
                      
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data Geofence</strong> <a href="{{ route('histories_geofence.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nopol : </b>{{ $item->no_pol }}</li>
                              <li class="list-group-item"><b>Acc : </b>{{ $item->acc }}</li>
                              <li class="list-group-item"><b>Enter Geofence : </b>{{ $item->enter_time }}</li>
                              <li class="list-group-item"><b>Exit Geofence :</b> {{ $item->exit_time }}</li>
                              <li class="list-group-item"><b>Geofence Name :</b> {{ $item->geofence_name }}</li>
                              <li class="list-group-item"><b>Status Geofence:</b> 
                                @if(is_null($item->status_geofence))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status_geofence == 1)
                                        <span class="badge bg-primary">Enter Geofence</span>
                                    @elseif($item->status_geofence == 2)
                                        <span class="badge bg-success">Exit Geofence</span>
                                    @endif
                              </li>
                              <li class="list-group-item"><b>Status Kirim:</b> 
                                @if(is_null($item->status_kirim))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status_kirim == 0)
                                        <span class="badge bg-info">Fence Belum Dikirim</span>
                                    @elseif($item->status_kirim == 1)
                                        <span class="badge bg-primary">Fence Enter Sukses Terkirim</span>
                                    @elseif($item->status_kirim == 2)
                                        <span class="badge bg-success">Fence Exit Sukses Terkirim</span>
                                    @elseif($item->status_kirim == 3)
                                        <span class="badge bg-danger">Gagal Kirim</span>
                                    @endif
                              </li>
                              <li class="list-group-item"><b>Keterangan :</b> {{ $item->note }}</li> 
                              {{-- <li class="list-group-item">Customer : {{ $item->customer->name ?? '-' }}</li> --}}
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('histories_geofence.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush