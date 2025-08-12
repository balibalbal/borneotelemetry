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
            <strong>Data Parkir</strong> <a href="{{ route('hso_parking.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nopol : </b>{{ $item->no_pol }}</li>
                              <li class="list-group-item"><b>Acc : </b>{{ $item->acc }}</li>
                              <li class="list-group-item"><b>Enter Parking : </b>{{ $item->off }}</li>
                              <li class="list-group-item"><b>Exit Parking :</b> {{ $item->off }}</li>
                              <li class="list-group-item"><b>Durasi :</b> {{ $item->duration }}</li>
                              <li class="list-group-item"><b>Latitude :</b> {{ $item->latitude }}</li>
                              <li class="list-group-item"><b>Longitude :</b> {{ $item->longitude }}</li>
                              <li class="list-group-item"><b>Lokasi :</b> {{ $item->address }}</li>
                              <li class="list-group-item"><b>Status :</b> 
                                @if(is_null($item->status))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status == 0)
                                        <span class="badge bg-primary">Belum di Kirim</span>
                                    @elseif($item->status == 1)
                                        <span class="badge bg-success">Sukses Kirim</span>
                                    @elseif($item->status == 2)
                                        <span class="badge bg-danger">Gagal Kirim</span>
                                    @endif
                              </li>
                              <li class="list-group-item"><b>Informasi :</b> {{ $item->info }}</li>
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('hso_parking.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush