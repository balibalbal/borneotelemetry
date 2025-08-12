@extends('layouts.admin')
@section('title', 'View Supir')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data Supir</strong> <a href="{{ route('drivers.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nama Supir :</b> {{ $item->name }}</li>
                              <li class="list-group-item"><b>Kode Supir :</b> {{ $item->driver_code }}</li>
                              {{-- <li class="list-group-item"><b>Divisi :</b>@if($item->divisi == 'J')
                                DIVISI KOTA-KOTA JAKARTA
                                @elseif($item->divisi == 'S')
                                DIVISI BPPI
                                @elseif($item->divisi == 'A')
                                DIVISI KOTA-KOTA SURABAYA
                                @elseif($item->divisi == 'M')
                                DIVISI INDORAMA
                                @elseif($item->divisi == 'G')
                                DIVISI GANDENGAN
                                @elseif($item->divisi == 'T')
                                DIVISI TANGKI
                                @endif
                              </li> --}}
                              <li class="list-group-item"><b>Tanggal Mulai Kerja :</b> {{ substr($item->start_date, 0, 10) }}</li>
                              <li class="list-group-item"><b>Tanggal Habis Kontrak :</b> {{ substr($item->contract_end_date, 0, 10) }}</li>
                              <li class="list-group-item"><b>ID SIM :</b> {{ $item->sim_number }}</li>
                              <li class="list-group-item"><b>Tipe SIM :</b> {{ $item->sim_type }}</li>
                              <li class="list-group-item"><b>Tanggal Habis SIM :</b> {{ substr($item->expired_sim, 0, 10) }}</li>
                              <li class="list-group-item"><b>Nomor Rekening :</b> {{ $item->rekening_number }}</li>
                              <li class="list-group-item"><b>Nama Rekening :</b> {{ $item->rekening_name }}</li>
                              <li class="list-group-item"><b>Alamat :</b> {{ $item->address }}</li>
                              <li class="list-group-item"><b>Nomor Telepon :</b> {{ $item->phone }}</li>
                              {{-- <li class="list-group-item"><b>Hutang :</b> <span class="badge rounded-pill  bg-label-danger">Rp {{ number_format($item->debt, 0, ',', '.' ?? '0') }},-</span></li> --}}
                              <li class="list-group-item"><b>Keterangan :</b> {{ $item->note }}</li>
                              <li class="list-group-item"><b>Posisi Supir :</b> {{ $item->driver_position }}</li>
                              <li class="list-group-item"><b>Nomor Sertifikat :</b> {{ $item->no_certificate }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Sertifikat :</b> {{ substr($item->masa_berlaku_certificate, 0, 10) }}</li>
                              {{-- <li class="list-group-item"><b>Nama PT :</b> {{ $item->nama_pt }}</li> --}}
                              <li class="list-group-item"><b>Status :</b> @if($item->status == 0)
                              <span class="badge rounded-pill  bg-label-danger">Tidak Aktif</span>
                              @else
                              <span class="badge rounded-pill  bg-label-primary">Aktif</span>
                              @endif</li>
                            </ul> 
                    </div> 
                    <div class="col-md-6"> 
                        <div class="row">
                            <div class="col-md-6">
                                @if($item->photo_driver == '')
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header">Photo Supir</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/driver.png') }}" class="card-img-top card-img-bottom">
                                        </div>                                        
                                    </div>
                                @else
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Photo Supir</span>
                                            <a href="{{ url('storage/' . $item->photo_driver) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        
                                        <div class="card-body">
                                            <img src="{{ url('storage/' . $item->photo_driver) }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($item->photo_certificate_driver == '')
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header">Photo Sertifikat</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/certificate.png') }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @else
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Photo Sertifikat</span>
                                            <a href="{{ url('storage/' . $item->photo_certificate_driver) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ url('storage/' . $item->photo_certificate_driver) }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mt-3">
                                @if($item->photo_ktp == '')
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header">Photo KTP</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/ktp.png') }}" class="card-img-top card-img-bottom">
                                        </div>                                        
                                    </div>
                                @else
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Photo KTP</span>
                                            <a href="{{ url('storage/' . $item->photo_ktp) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ url('storage/' . $item->photo_ktp) }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mt-3">
                                @if($item->photo_sim == '')
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header">Photo SIM</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/sim.png') }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @else
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Photo SIM</span>
                                            <a href="{{ url('storage/' . $item->photo_sim) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ url('storage/' . $item->photo_sim) }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>                                                
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('drivers.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
        </div>
    </div>
</div>

@endsection