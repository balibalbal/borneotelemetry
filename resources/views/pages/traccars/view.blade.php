@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Traccars Detail</strong> <a href="{{ url('/list-traccars') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item">ID : {{ $item->id }}</li>
                              <li class="list-group-item">Time : {{ $item->time }}</li>
                              <li class="list-group-item">No. Polisi : <span class="badge bg-dark">{{ $item->no_pol }}</span></li>
                              <li class="list-group-item">Latitude : {{ $item->lat }}</li>
                              <li class="list-group-item">Longitude : {{ $item->long }}</li>
                              <li class="list-group-item">Speed : {{ $item->speed }} kph</li>
                              <li class="list-group-item">Total Distance : {{ $item->total_distance }}</li>
                              
                            </ul>
                    </div>
                    <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item">Geofence : <span class="badge bg-primary">{{ $item->geofence }}</span></li>
                              <li class="list-group-item">Nama Geofence : {{ $item->geofence_name }}</li>
                              <li class="list-group-item">Enter Time Geofence : {{ $item->enter_time }}</li>
                              <li class="list-group-item">Out Time Geofence : {{ $item->out_time }}</li>
                              <li class="list-group-item">Vendor GPS : {{ $item->vendor_gps }}</li>
                              <li class="list-group-item">Alamat : {{ $item->address }} </li>
                              <li class="list-group-item">Tanggal Update : <span class="badge bg-danger">{{ $item->updated_at }}</span></li>
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('traccars.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
        </div>
    </div>
</div>

@endsection