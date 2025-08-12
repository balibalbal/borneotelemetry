@push('style')
    <!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

@endpush

@extends('layouts.admin')
@section('title', 'View Geofence')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-3"> 
        <div id="map" style="height: 400px;"></div>               
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Data Geofence</strong> <a href="{{ route('geofence.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Nama Geofence :</b> {{ $item->name }}</li>
                              <li class="list-group-item"><b>Tipe Geofence :</b> 
                                @if ($item->type == 0)
                                    <span class="badge bg-danger">Jalan</span>
                                @elseif ($item->type == 1)
                                    <span class="badge bg-primary">Customer/Pabrik</span>
                                @elseif ($item->type == 2)
                                    <span class="badge bg-dark">Depo/Pelabuhan</span>
                                @else
                                    Depo/PelabuhanTidak diketahui
                                @endif
                              </li>
                              <li class="list-group-item"><b>Geofence : </b>{{ $item->polygon }}</li>
                              
                            </ul>
                    </div>
                </div>   
            </div>
        <div class="card-footer d-flex justify-content-end">
            @if ($item->type == 0)
                <a href="{{ route('geofence.edit', $item->id) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-database-edit me-sm-1"></i> Edit Data</a>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
    var map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    @if($item)
        var coordinates = '{{ $item->polygon }}'.replace('(', '').replace(')', '');
        var latlngs = coordinates.split(', ').map(function(point) {
            var [lng, lat] = point.split(' ');
            return [parseFloat(lat), parseFloat(lng)];
        });

        // Tambahkan poligon ke peta
        var polygon = L.polygon(latlngs, { color: 'red' }).addTo(map);

        // Tambahkan event click pada polygon
        polygon.on('click', function(e) {
            // Tampilkan informasi
            var info = "<b>Nama:</b> {{ $item->name }}<br>" +
                       "<b>Alamat:</b> {{ $item->address }}<br>" +
                       "<b>Telepon:</b> {{ $item->phone }}";
            L.popup()
                .setLatLng(e.latlng)
                .setContent(info)
                .openOn(map);
        });

        map.fitBounds(polygon.getBounds());
    @endif
</script>
@endpush