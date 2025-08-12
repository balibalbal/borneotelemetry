@extends('layouts.admin')

@section('title', 'Clustering Lokasi Berhenti')

@push('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 90vh;
            width: 100%;
        }

        #filter {
            margin: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div id="filter">
            <form method="GET" action="{{ url('/grafik/clustering-location') }}">
                <label for="vehicle_id">Kendaraan:</label>
                <select name="vehicle_id">
                    <option value="">-- Pilih --</option>
                    @foreach ($vehicles as $id)
                        <option value="{{ $id }}" {{ $vehicleId == $id ? 'selected' : '' }}>{{ $id }}</option>
                    @endforeach
                </select>

                <label>Speed Min:</label>
                <input type="number" name="min_speed" step="0.1" value="{{ $minSpeed }}" placeholder="0">

                <label>Speed Max:</label>
                <input type="number" name="max_speed" step="0.1" value="{{ $maxSpeed }}" placeholder="100">

                <button type="submit">Tampilkan</button>
            </form>
        </div>

        @if ($vehicleId && count($locations))
            <div id="map"></div>
        @else
            <p style="text-align:center;">Silakan pilih kendaraan dan filter untuk melihat lokasi.</p>
        @endif
    </div>
@endsection

@push('scripts')
    @if ($vehicleId && count($locations))
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
        <script>
            var map = L.map('map').setView([-6.2, 106.8], 10); // Set default map view
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            var markers = L.markerClusterGroup();

            // Adding markers to the cluster group
            @foreach ($locations as $loc)
                markers.addLayer(L.marker([{{ $loc->latitude }}, {{ $loc->longitude }}])
                    .bindPopup('Cluster ID: {{ $loc->cluster_id }}<br>Speed: {{ $loc->speed }} km/h'));
            @endforeach

            // Add cluster markers to the map
            map.addLayer(markers);
        </script>
    @endif
@endpush
