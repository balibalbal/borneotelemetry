@push('style')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
  <style>
    #no_pol + .select2-container .select2-selection--single {
        height: 45px;
        padding: 10px;
    }

    #loading {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000; /* Pastikan elemen ini berada di atas elemen lainnya */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #dataTable_processing {
        display: none !important;
    }
  </style>
@endpush

@extends('layouts.admin')
@section('title', 'Tracking History')
@section('content')
<div class="container-fluid">
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="col-lg-12 col-12">
        <div class="card mb-3">
            <form action="" method="GET">
                <div class="card-header header-elements d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Speed Tracking</h5>
                        <small class="text-muted">Speed tracking yang dapat di-generate ke maksimal 1 bulan</small>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline mb-3">
                                <select name="no_pol" id="no_pol" class="form-select form-control @error('no_pol') is-invalid @enderror" data-allow-clear="true">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->no_pol }}" data-no-pol="{{ $vehicle->no_pol }}">
                                        {{ $vehicle->no_pol }} 
                                    </option>
                                    @endforeach
                                </select>
                                <label for="no_pol">Plat Number</label>
                                @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="datetime-local" id="start_date" class="form-control datetimepicker @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}" required/>
                                <label for="start_date">Start Date</label>
                                @error('start_date')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="datetime-local" id="end_date" class="form-control datetimepicker @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" />
                                <label for="end_date">End Date</label>
                                @error('end_date')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <button class="btn rounded-pill btn-dark waves-effect waves-light" type="submit" id="trackButton"><i class="mdi mdi-car-search-outline me-sm-1"></i> Track Now</button>
                </div>
            </form>
        </div>
    </div>

    <div id="dataTable_processing2" class="dataTables_processing" style="width: 6rem;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div> 

    <div class="row">
        <!-- Peta -->
        <div class="col-md-12">
            <div id="map" style="height: 500px;"></div>
        </div>
    </div>

    <!-- Box Informasi -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card shadow mb-3" id="infoBox">
                <div class="card-body">
                    <h5 class="card-title">Informasi Perjalanan</h5>
                    <p><strong>Total Jarak: </strong><span id="totalDistance">0 km</span></p>
                    <p><strong>Kecepatan Rata-rata: </strong><span id="avgSpeed">0 km/h</span></p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker@0.2.0/leaflet.rotatedMarker.min.js"></script>

<script>
    $(document).ready(function () {
        $('#no_pol').select2({
            allowClear: true,
            placeholder: 'Select Vehicle',
            dropdownAutoWidth: true,
            width: '100%',
        });

        $('#dataTable_processing2').hide();
    });

    const vehicleIcon = L.icon({
        iconUrl: 'backend/assets/img/illustrations/on.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    const vehicleEndIcon = L.icon({
        iconUrl: 'backend/assets/img/illustrations/engine.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    let map = L.map('map').setView([-2.5, 117], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let allLayers = [];

    function clearMapLayers() {
        allLayers.forEach(layer => map.removeLayer(layer));
        allLayers = [];
    }

    $('#trackButton').on('click', function (e) {
        e.preventDefault();

        let no_pol = $('#no_pol').val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();

        if (!no_pol || !start_date || !end_date) {
            alert('Mohon lengkapi semua data.');
            return;
        }

        $('#dataTable_processing2').show();
        clearMapLayers(); // Bersihkan peta sebelumnya

        $.ajax({
            url: '/grafik/speed-map',
            method: 'GET',
            data: { no_pol, start_date, end_date },
            success: function (response) {
                $('#dataTable_processing2').hide();

                const data = response.data;
                if (!data.length) {
                    alert("Tidak ada data ditemukan.");
                    return;
                }

                let latlngs = [];
                let totalDistance = 0;

                // Buat polyline per segmen
                for (let i = 1; i < data.length; i++) {
                    const prev = data[i - 1];
                    const curr = data[i];

                    const segment = [
                        [prev.latitude, prev.longitude],
                        [curr.latitude, curr.longitude]
                    ];
                    latlngs.push([curr.latitude, curr.longitude]);

                    // Hitung jarak
                    const dist = L.latLng(prev.latitude, prev.longitude).distanceTo(L.latLng(curr.latitude, curr.longitude));
                    totalDistance += dist;

                    // Warna berdasarkan speed
                    const speed = curr.speed;
                    let color = 'gray';
                    if (speed <= 30) color = 'blue';
                    else if (speed <= 40) color = 'green';
                    else if (speed <= 50) color = 'darkorange';
                    else color = 'red';

                    const poly = L.polyline(segment, { color, weight: 10 }).addTo(map);
                    allLayers.push(poly);
                }

                // Marker awal & akhir
                const start = data[0];
                const end = data[data.length - 1];

                const startMarker = L.marker([start.latitude, start.longitude], { icon: vehicleIcon }).addTo(map).bindPopup("Start Point");
                const endMarker = L.marker([end.latitude, end.longitude], { icon: vehicleEndIcon }).addTo(map).bindPopup("End Point");
                startMarker.setRotationAngle(start.course);
                endMarker.setRotationAngle(end.course);
                allLayers.push(startMarker, endMarker);

                // Fit map ke jalur
                map.fitBounds(L.polyline(latlngs).getBounds());

                // Info
                const totalKm = (totalDistance / 1000).toFixed(2);
                const timeStart = new Date(start_date);
                const timeEnd = new Date(end_date);
                const durSeconds = (timeEnd - timeStart) / 1000;
                const avgSpeed = durSeconds > 0 ? ((totalDistance / durSeconds) * 3.6).toFixed(2) : '0';

                $('#totalDistance').text(totalKm + ' km');
                $('#avgSpeed').text(avgSpeed + ' km/h');
            },
            error: function () {
                $('#dataTable_processing2').hide();
                alert("Terjadi kesalahan saat memuat data.");
            }
        });
    });

    // LEGEND untuk speed
    const legend = L.control({ position: 'bottomleft' });

    legend.onAdd = function (map) {
        const div = L.DomUtil.create('div', 'info legend bg-white shadow rounded p-2 small');
        const grades = [
            { label: "0–30 km/h", color: "blue" },
            { label: "31–40 km/h", color: "green" },
            { label: "41–50 km/h", color: "darkorange" },
            { label: ">50 km/h", color: "red" }
        ];
        let html = '<strong>Speed Legend</strong><br>';
        grades.forEach(grade => {
            html += `<i style="background:${grade.color}; width:12px; height:12px; display:inline-block; margin-right:6px;"></i>${grade.label}<br>`;
        });
        div.innerHTML = html;
        return div;
    };

    legend.addTo(map);
</script>

@endpush
