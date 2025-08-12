@push('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
@endpush


@extends('layouts.admin')
@section('title', 'Tambah Geofence Polygon')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <strong>Create Geofence</strong> <a href="{{ route('geofence.index') }}"
                    class="btn rounded-pill btn-dark waves-effect waves-light"><i
                        class="mdi mdi-arrow-left-circle me-sm-1"></i> Back</a>
            </div>

            <form action="{{ route('geofence.store') }}" method="POST">
                <div class="card-body card-block">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            {{-- peta disini --}}
                            <div id="map" style="height: 500px;"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-merge mb-4">
                                <span id="name" class="input-group-text"><i class="mdi mdi-latitude"></i></span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="name" placeholder="Ketik nama geofence disini"
                                        aria-label="Ketik nama geofence disini" aria-describedby="name" name="name"
                                        value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"
                                        required />
                                    <label for="name">Nama Geofence</label>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="input-group input-group-merge mb-4">
                              <div class="form-floating form-floating-outline">
                                  <select name="type" id="type" class="select2 form-select form-control @error('type') is-invalid @enderror" data-allow-clear="true" required>
                                      <option value="" {{ old('type') == '' ? 'selected' : '' }}>Select Geofence Type</option>
                                      <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Radius</option>
                                      <option value="2" {{ old('type') == '2' ? 'selected' : '' }}>Polygon</option>
                                  </select>
                                  <label for="type">Geofence Type</label>
                                  @error('type')<div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                            </div>

                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="radius" id="radius">
                            <input type="hidden" name="geojson" id="geojson">

                            <div id="radius-inputs" style="display: none;">
                                <div class="input-group input-group-merge mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" step="any" name="manual_latitude" id="manual_latitude" class="form-control" placeholder="Latitude">
                                        <label for="manual_latitude">Latitude</label>
                                    </div>
                                </div>

                                <div class="input-group input-group-merge mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" step="any" name="manual_longitude" id="manual_longitude" class="form-control" placeholder="Longitude">
                                        <label for="manual_longitude">Longitude</label>
                                    </div>
                                </div>

                                <div class="input-group input-group-merge mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" step="any" name="manual_radius" id="manual_radius" class="form-control" placeholder="Radius (meter)">
                                        <label for="manual_radius">Radius (meter)</label>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="input-group input-group-merge mb-4">
                              <div class="form-floating form-floating-outline">
                                <select name="customer_id" id="customer_id" class="select2 form-select form-control @error('customer_id') is-invalid @enderror" data-allow-clear="true">
                                  <option value="">Select Customer</option>
                                  @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }}
                                    </option>
                                  @endforeach
                                </select>
                                <label for="customer_id">Customer</label>
                                @error('customer_id')<div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                            </div>

                            <div class="input-group input-group-merge mb-4">
                              <div class="form-floating form-floating-outline">
                                  <select name="status" id="status" class="select2 form-select form-control @error('status') is-invalid @enderror" data-allow-clear="true">
                                      <option value="" {{ old('status') == '' ? 'selected' : '' }}>Select Status</option>
                                      <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                      <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Not Active</option>
                                  </select>
                                  <label for="status">Status</label>
                                  @error('status')<div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <div class="form-group">
                        <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i
                                class="mdi mdi-content-save-check me-sm-1"></i> Save Geofence</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

    <script>

        const typeSelect = document.getElementById('type');
        const radiusInputs = document.getElementById('radius-inputs');

        // Tampilkan input manual jika pilih radius
        typeSelect.addEventListener('change', function () {
            if (this.value == '1') {
                radiusInputs.style.display = 'block';
            } else {
                radiusInputs.style.display = 'none';
            }
        });

        // Saat submit, salin nilai dari input manual jika tersedia
        document.querySelector('form').addEventListener('submit', function (e) {
            const lat = document.getElementById('manual_latitude').value;
            const lng = document.getElementById('manual_longitude').value;
            const rad = document.getElementById('manual_radius').value;

            if (lat && lng && rad) {
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('radius').value = rad;
            }
        });

        var map = L.map('map').setView([-6.2088, 106.8456], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                polyline: false,
                marker: false,
                rectangle: false,
                circlemarker: false,
                polygon: {
                    allowIntersection: false
                },
                circle: {
                    shapeOptions: {
                        color: 'blue'
                    }
                }
            },
            edit: {
                featureGroup: drawnItems
            }
        });

        map.addControl(drawControl);

        map.on(L.Draw.Event.CREATED, function(event) {
            drawnItems.clearLayers(); // hapus sebelumnya
            var layer = event.layer;
            drawnItems.addLayer(layer);

            let type = event.layerType;

            if (type === 'circle') {
                let latlng = layer.getLatLng();
                document.getElementById('latitude').value = latlng.lat;
                document.getElementById('longitude').value = latlng.lng;
                document.getElementById('radius').value = layer.getRadius();
            } else if (type === 'polygon') {
                let geojson = layer.toGeoJSON();
                document.getElementById('geojson').value = JSON.stringify(geojson.geometry);
            }
        });

        // Ganti input yang aktif saat user ubah tipe
        document.getElementById('type').addEventListener('change', function() {
            drawnItems.clearLayers();
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
            document.getElementById('radius').value = '';
            document.getElementById('geojson').value = '';
        });
    </script>
@endpush
