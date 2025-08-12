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
                                @if ($item->type == 2)
                                    <span class="badge bg-danger">Polygon</span>
                                @elseif ($item->type == 1)
                                    <span class="badge bg-primary">Radius</span>
                                @else
                                    Tidak diketahui
                                @endif
                              </li>
                              @if ($item->type == 2)
                                <li class="list-group-item"><b>Area : </b>{{ $item->area }} m²</li>
                              @else
                                <li class="list-group-item"><b>Radius : </b>{{ $item->radius }} m</li>
                              @endif
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
            var type = {{ $item->type }};
            var name = "{{ $item->name }}";
            var allBounds = [];

            if (type === 1 && {!! $item->center_point ? 'true' : 'false' !!}) {
                var center = JSON.parse('{!! $item->center_point !!}').coordinates;
                var radius = {{ $item->radius ?? 0 }};

                var circle = L.circle([center[1], center[0]], {
                    color: 'red',
                    radius: radius
                }).addTo(map);

                circle.bindPopup(`
                    <b>Nama:</b> ${name}<br>
                    <b>Radius:</b> ${radius.toLocaleString()} meter
                `);
                allBounds.push([center[1], center[0]]);

                map.fitBounds(circle.getBounds());
            }

            if (type === 2 && {!! $item->polygon_area ? 'true' : 'false' !!}) {
                var polygon = JSON.parse('{!! $item->polygon_area !!}');
                var coordinates = polygon.coordinates[0].map(coord => [coord[1], coord[0]]);
                var area = {{ $item->area ?? 0 }};

                var shape = L.polygon(coordinates, { color: 'blue' }).addTo(map);
                shape.bindPopup(`
                    <b>Nama:</b> ${name}<br>
                    <b>Luas:</b> ${area.toFixed(2).toLocaleString()} m²
                `);
                allBounds.push(...coordinates);

                map.fitBounds(shape.getBounds());
            }
        @endif
    </script>

@endpush
