@extends('layouts.admin')

@section('title', 'Grafik Kecepatan Kendaraan')

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
        /* Legend styling */
        .info.legend {
            background: white;
            padding: 8px;
            line-height: 18px;
            color: #333;
            border-radius: 4px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            font-size: 12px;
        }
        .info.legend i {
            float: left;
            width: 18px;
            height: 18px;
            margin-right: 8px;
            opacity: 0.8;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div id="filter">
            <form method="GET" action="{{ url('/grafik/heatmap-speed-status') }}">
                <label>Kendaraan:</label>
                <select name="vehicle_id">
                    <option value="">-- Pilih --</option>
                    @foreach ($vehicles as $id)
                        <option value="{{ $id }}" {{ $vehicleId == $id ? 'selected' : '' }}>{{ $id }}</option>
                    @endforeach
                </select>

                <label>Status Mesin:</label>
                <select name="ignition_status">
                    <option value="">Semua</option>
                    <option value="On">ON</option>
                    <option value="Off">OFF</option>
                </select>

                <label>Speed Min:</label>
                <input type="number" name="min_speed" step="0.1" placeholder="0">

                <label>Speed Max:</label>
                <input type="number" name="max_speed" step="0.1" placeholder="100">

                <button type="submit">Tampilkan</button>
            </form>
        </div>

        @if ($vehicleId && count($locations))
            <div id="map"></div>
        @else
            <p style="text-align:center;">Silakan pilih kendaraan dan filter untuk melihat heatmap.</p>
        @endif
    </div>
@endsection

@push('scripts')
    @if ($vehicleId && count($locations))
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
        <script>
            // Inisialisasi peta
            var map = L.map('map').setView([-6.2, 106.8], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            // Siapkan data heatmap dan marker
            var heatData = [];
            var zeroSpeedIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/61/61168.png',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });

            @foreach ($locations as $loc)
                @if ($loc->speed > 0)
                    heatData.push([{{ $loc->latitude }}, {{ $loc->longitude }}, {{ round($loc->speed / 50, 2) }}]);
                @else
                    L.marker([{{ $loc->latitude }}, {{ $loc->longitude }}], {icon: zeroSpeedIcon})
                        .bindPopup("Speed: 0 km/h<br>Status: {{ $loc->ignition_status }}")
                        .addTo(map);
                @endif
            @endforeach

            console.log([
                @foreach ($locations as $loc)
                    [{{ $loc->latitude }}, {{ $loc->longitude }}, {{ max(0.1, $loc->speed / 50) }}],
                @endforeach
            ]);

            // Fit bounds ke semua titik
            var latlngs = heatData.map(function(p) { return [p[0], p[1]]; });
            @foreach ($locations as $loc)
                latlngs.push([{{ $loc->latitude }}, {{ $loc->longitude }}]);
            @endforeach
            if (latlngs.length) {
                map.fitBounds(latlngs);
            }

            // Tambahkan heatmap jika ada data
            if (heatData.length > 0) {
                L.heatLayer(heatData, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 17,
                    gradient: {
                        0.1: 'blue',
                        0.3: 'lime',
                        0.5: 'yellow',
                        0.7: 'orange',
                        1.0: 'red'
                    }
                }).addTo(map);
            }

            // Legend di pojok kanan bawah
            var legend = L.control({position: 'bottomright'});
            legend.onAdd = function (map) {
                var div = L.DomUtil.create('div', 'info legend');
                var grades = [0.1, 0.3, 0.5, 0.7, 1.0];
                var labels = ['<strong>Speed (km/h)</strong><br>'];
                var colors = {
                    0.1: 'blue',
                    0.3: 'lime',
                    0.5: 'yellow',
                    0.7: 'orange',
                    1.0: 'red'
                };
                grades.forEach(function(grade) {
                    labels.push(
                        '<i style="background:' + colors[grade] + ';"></i> ' +
                        Math.round(grade * 100) + '+'
                    );
                });
                div.innerHTML = labels.join('<br>');
                return div;
            };
            legend.addTo(map);
        </script>
    @endif
@endpush
