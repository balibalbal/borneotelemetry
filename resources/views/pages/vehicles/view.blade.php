@push('style')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker/leaflet.rotatedMarker.js"></script>
@endpush

@extends('layouts.admin')
@section('title', 'View Kendaraan')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data Kendaraan</strong> <a href="{{ route('vehicles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block"> 
                <div id="map" style="height: 300px;"></div>           
                <hr>
                <div class="row">
                    <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nomor Polisi :</b> {{ $item->no_pol }}</li>
                              <li class="list-group-item"><b>Nama PT :</b> {{ $item->nama_pt }}</li>
                              <li class="list-group-item"><b>Alamat :</b> {{ $item->address }}</li>
                              <li class="list-group-item"><b>Jenis Kendaraan :</b> 
                                    @if(is_null($item->vehicle_type))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->vehicle_type == 0)
                                        <span class="badge bg-dark"><i class="mdi mdi-dump-truck"></i> Mobil</span>
                                    @elseif($item->vehicle_type == 1)
                                        <span class="badge bg-success"><i class="mdi mdi-motorbike"></i> Sepeda Motor</span>
                                    @else
                                        <span class="badge bg-secondary">Jenis Kendaraan Tidak Dikenal</span>
                                    @endif
                              </li>
                              <li class="list-group-item"><b>Divisi :</b> {{ $item->divisi }}</li>
                              <li class="list-group-item"><b>Tahun Kendaraan :</b> {{ $item->tahun_kendaraan }}</li>
                              <li class="list-group-item"><b>Warna :</b> {{ $item->warna }}</li>
                              <li class="list-group-item"><b>Masa Berlaku STNK :</b> {{ substr($item->expired_stnk, 0, 10) }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Pajak :</b> {{ substr($item->expired_pajak, 0, 10) }}</li>
                              <li class="list-group-item"><b>Nomor Rangka :</b> {{ $item->no_rangka }}</li>
                              <li class="list-group-item"><b>Nomor Mesin :</b> {{ $item->no_mesin }}</li>
                              <li class="list-group-item"><b>Merk/Type :</b> {{ $item->type }}</li>
                              <li class="list-group-item"><b>Head KIR :</b> {{ $item->head_kir }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Head KIR :</b> {{ substr($item->expired_head_kir, 0, 10) }}</li>
                              <li class="list-group-item"><b>Chasis KIR :</b> {{ $item->chasis_kir }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Chasis KIR :</b> {{ substr($item->expired_chasis_kir, 0, 10) }}</li>
                              <li class="list-group-item"><b>Nama PT Chasis KIR :</b> {{ $item->nama_pt_chasis_kir }}</li>
                              <li class="list-group-item"><b>Jenis Chasis :</b> {{ $item->jenis_chasis }}</li>
                              <li class="list-group-item"><b>Nomor Chasis :</b> {{ $item->nomor_chasis }}</li>
                              <li class="list-group-item"><b>Model Chasis :</b> {{ $item->model_chasis }}</li>
                              <li class="list-group-item"><b>Divisi Chasis :</b> {{ $item->divisi_chasis }}</li>
                              <li class="list-group-item"><b>Nomor Rekom B3 KLHK :</b> {{ $item->no_rekom_b3_klhk }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Rekom B3 KLHK :</b> {{ substr($item->expired_rekom, 0, 10) }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Kartu Kemenhub :</b> {{ substr($item->expired_kartu_kemenhub, 0, 10) }} </li>
                              <li class="list-group-item"><b>Nomor Single TID :</b> {{ $item->no_single_tid }}</li>
                              <li class="list-group-item"><b>Masa Berlaku Single TID :</b> {{ substr($item->expired_single_tid, 0, 10) }}</li>
                              <li class="list-group-item"><b>Nama GPS :</b> {{ $item->nama_gps }}</li>
                              <li class="list-group-item"><b>Keterangan :</b> {{ $item->keterangan }}</li>
                              <li class="list-group-item"><b>Driver :</b> {{ $item->driver->name ?? '-' }}</li> 
                              <li class="list-group-item"><b>Latitude :</b> {{ $item->latitude }}</li>
                              <li class="list-group-item"><b>Longitude :</b> {{ $item->longitude }}</li>                                                 
                            </ul>
                    </div> 
                    <div class="col-md-6"> 
                        <div class="row">
                            <div class="col-md-6">
                                @if($item->photo_stnk == '')
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header">STNK</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/stnk.png') }}" class="card-img-top card-img-bottom">
                                        </div>                                        
                                    </div>
                                @else
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>STNK</span>
                                            <a href="{{ url('storage/' . $item->photo_stnk) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                                                        
                                        <div class="card-body">
                                            @if(pathinfo($item->photo_stnk, PATHINFO_EXTENSION) == 'pdf')
                                                <img src="{{ url('backend/img/file_pdf.png') }}" class="card-img-top card-img-bottom preview-pdf" data-pdf-url="{{ url('storage/' . $item->photo_stnk) }}">
                                            @else
                                                <img src="{{ url('storage/' . $item->photo_stnk) }}" class="card-img-top card-img-bottom">
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="col-md-6">
                                @if($item->photo_head_kir == '')
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header">Head KIR</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/head_kir.png') }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @else
                                    <div class="card" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Head KIR</span>
                                            <a href="{{ url('storage/' . $item->photo_head_kir) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        <div class="card-body">
                                            @if(pathinfo($item->photo_head_kir, PATHINFO_EXTENSION) == 'pdf')
                                                <img src="{{ url('backend/img/file_pdf.png') }}" class="card-img-top card-img-bottom preview-pdf" data-pdf-url="{{ url('storage/' . $item->photo_head_kir) }}">
                                            @else
                                            <img src="{{ url('storage/' . $item->photo_head_kir) }}" class="card-img-top card-img-bottom">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                @if($item->photo_chasis_kir == '')
                                    <div class="card mt-3" style="width: 100%;">
                                        <div class="card-header">Chasis KIR</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/chasis_kir.png') }}" class="card-img-top card-img-bottom">
                                        </div>                                        
                                    </div>
                                @else
                                    <div class="card mt-3" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Chasis KIR</span>
                                            <a href="{{ url('storage/' . $item->photo_chasis_kir) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        
                                        <div class="card-body">
                                            @if(pathinfo($item->photo_chasis_kir, PATHINFO_EXTENSION) == 'pdf')
                                                <img src="{{ url('backend/img/file_pdf.png') }}" class="card-img-top card-img-bottom preview-pdf" data-pdf-url="{{ url('storage/' . $item->photo_chasis_kir) }}">
                                            @else
                                            <img src="{{ url('storage/' . $item->photo_chasis_kir) }}" class="card-img-top card-img-bottom">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($item->photo_head_kir == '')
                                    <div class="card mt-3" style="width: 100%;">
                                        <div class="card-header">B3 KLHK</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/b3_klhk.png') }}" class="card-img-top card-img-bottom">
                                        </div>
                                    </div>
                                @else
                                    <div class="card mt-3" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>B3 KLHK</span>
                                            <a href="{{ url('storage/' . $item->photo_b3_klhk) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        <div class="card-body">
                                            @if(pathinfo($item->photo_b3_klhk, PATHINFO_EXTENSION) == 'pdf')
                                                <img src="{{ url('backend/img/file_pdf.png') }}" class="card-img-top card-img-bottom preview-pdf" data-pdf-url="{{ url('storage/' . $item->photo_b3_klhk) }}">
                                            @else
                                            <img src="{{ url('storage/' . $item->photo_b3_klhk) }}" class="card-img-top card-img-bottom">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                @if($item->photo_stnk == '')
                                    <div class="card mt-3" style="width: 100%;">
                                        <div class="card-header">Kartu Pengawasan Kemenhub</div>
                                        <div class="card-body">
                                            <img src="{{ url('backend/img/kemenhub.png') }}" class="card-img-top card-img-bottom">
                                        </div>                                        
                                    </div>
                                @else
                                    <div class="card mt-3" style="width: 100%;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>Kartu Pengawasan Kemenhub</span>
                                            <a href="{{ url('storage/' . $item->photo_kartu_pengawasan_kemenhub) }}" download class="badge rounded-pill bg-label-info"><i class="mdi mdi-download-circle"></i> Unduh</a>
                                        </div>
                                        
                                        <div class="card-body">
                                            @if(pathinfo($item->photo_kartu_pengawasan_kemenhub, PATHINFO_EXTENSION) == 'pdf')
                                                <img src="{{ url('backend/img/file_pdf.png') }}" class="card-img-top card-img-bottom preview-pdf" data-pdf-url="{{ url('storage/' . $item->photo_kartu_pengawasan_kemenhub) }}">
                                            @else
                                                <img src="{{ url('storage/' . $item->photo_kartu_pengawasan_kemenhub) }}" class="card-img-top card-img-bottom">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                
                            </div>
                        </div>                        
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-google-maps me-sm-1"></i> Cek Posisi di Google Map</a> 
            @if (auth()->user()->hasPermissionTo('edit-kendaraan'))
                <a href="{{ route('vehicles.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
            @endif
        </div>
    </div>

    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Preview PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <embed id="pdfEmbed" src="" type="application/pdf" width="100%" height="600px" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>        
    </div>
    
</div>

@endsection

@push('scripts')
<script>
    var map = L.map('map').setView([{{ $item->latitude }}, {{ $item->longitude }}], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([{{ $item->latitude }}, {{ $item->longitude }}]).addTo(map)
        .bindPopup('{{ $item->no_pol }}').openPopup();
</script>

<script>
    $(document).ready(function() {
        $('.preview-pdf').click(function() {
            var pdfUrl = $(this).data('pdf-url');
            $('#pdfEmbed').attr('src', pdfUrl);
            $('#pdfModal').modal('show');
        });
    });
</script>

@endpush