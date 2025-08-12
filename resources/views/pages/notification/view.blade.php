@extends('layouts.admin')
@section('title', 'View Notifikasi')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Pesan / Notifikasi</strong> <a href="{{ route('notification.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Tanggal : {{ $item->time }}</li>
                                <li class="list-group-item">Nomor Polisi : {{ $item->no_pol }}</li>
                                <li class="list-group-item">Pesan : {{ $item->message }}</li>
                                <li class="list-group-item">Status : {{ $item->status }}</li>
                            </ul>
                    </div>
                </div>   
            </div>
    </div>
</div>

@endsection