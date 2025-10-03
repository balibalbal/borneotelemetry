@push('style')
  {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
  <style>
    #no_pol + .select2-container .select2-selection--single {
        height: 45px;
        padding: 10px;
    }
</style>
  <style>
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
  </style>
@endpush

@extends('layouts.admin')
@section('title', 'History Perjalanan')
@section('content')
<div class="container-fluid">
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
    @endif

    <div class="col-lg-12 col-12">
        <div class="card mb-3">
            <form action="" method="GET">
                <div class="card-header header-elements d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            Tracking Perjalanan Kendaraan
                        </h5>
                        <small class="text-muted">Tracking yang dapat di generate ke maksimal 1 bulan</small>
                    </div>
                    {{-- <a href="{{ url('/history') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-file-document-outline"></i> Unduh Tracking</a> --}}
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline mb-3">
                                <select name="no_pol" id="no_pol" class="form-select form-control @error('no_pol') is-invalid @enderror" data-allow-clear="true">
                                    <option value="">Pilih Kendaraan</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->no_pol }}" data-no-pol="{{ $vehicle->no_pol }}">
                                        {{ $vehicle->no_pol }} 
                                    </option>
                                    @endforeach
                                </select>
                                <label for="no_pol">Nomor Polisi</label>
                                @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="datetime-local" id="start_date" class="form-control datetimepicker @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}" required/>
                                <label for="start_date">Tanggal Awal</label>
                                @error('start_date')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="datetime-local" id="end_date" class="form-control datetimepicker @error('end_date') is-invalid @enderror" aria-describedby="basic-icon-default-phone2" name="end_date" value="{{ old('end_date') }}" />
                                <label for="end_date">Tanggal Akhir</label>
                                @error('end_date')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn rounded-pill btn-dark waves-effect waves-light" type="submit" id="trackButton"><i class="mdi mdi-car-search-outline me-sm-1"></i> Lacak Kendaraan</button>
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="button" id="playButton" style="display: none;"><i class="mdi mdi-play"></i> Putar Animasi</button>
                    {{-- <button class="btn rounded-pill btn-warning waves-effect waves-light" type="button" id="pauseButton" style="display: none;"><i class="mdi mdi-pause"></i> Pause Animasi</button> --}}
                    <button class="btn rounded-pill btn-danger waves-effect waves-light" type="button" id="stopButton" style="display: none;"><i class="mdi mdi-stop"></i> Stop Animasi</button>
                </div>
            </form>
        </div>
    </div>

    <div id="loading" style="display: none; width: 6rem;">
        <img class="card-img-top" src="/backend/assets/img/icons/loading.gif" alt="loading...">
    </div>

    {{-- peta --}}
    <div class="card shadow mb-3">
        <div id="map" style="height: 500px;"></div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Informasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                    <!-- Isi detail order akan ditampilkan di sini -->
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
{{-- <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker/leaflet.rotatedMarker.js"></script>

<script>
    $(document).ready(function() {
        $('#no_pol').select2({
            allowClear: true,
            placeholder: 'Pilih Kendaraan',
            dropdownAutoWidth: true,
            width: '100%',
        });

        $('#stopButton').prop('disabled', true); 
        $('#playButton').prop('disabled', true);
        $('#pauseButton').prop('disabled', true); 

        var map = L.map('map').setView([-6.2297209, 106.664705], 10); // Default location and zoom level
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        var animationInterval; // Variabel untuk menyimpan interval animasi
        var index;
        var courseValues;

        // Remove existing markers and polyline
        function removeExistingLayers() {
            clearInterval(animationInterval);
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker || layer instanceof L.Polyline) {
                    map.removeLayer(layer);
                }
            });
        }

        $('#trackButton').click(function(event) {
            event.preventDefault();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var noPol = $('#no_pol').val();

            // Perform AJAX request to get data
            $.ajax({
                url: '/get-data-map',
                method: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    no_pol: noPol
                },
                beforeSend: function() {
                    $('#loading').show(); // Show loading indicator
                },
                complete: function() {
                    $('#loading').hide(); // Hide loading indicator after completion
                },
                success: function(response) {
                    //console.log(response.data); // Log the response to check its structure

                    $('#playButton').prop('disabled', false); 
                    // Access the arrays from the response
                    var data = response.data;

                    if (!Array.isArray(data)) {
                        console.error("Expected arrays in the response");
                        $('#errorModalBody').html("Data tidak valid");
                        $('#errorModal').modal('show');
                        return;
                    }

                    // Remove existing markers and polylines
                    map.eachLayer(function(layer) {
                        if (layer instanceof L.Marker || layer instanceof L.Polyline || layer instanceof L.Polygon) {
                            map.removeLayer(layer);
                        }
                    });

                     // Add markers with popups for each data point
                     // dimatikan dulu karena berat saat load data 24-03-2025
                    //  data.forEach(function(item, idx) {
                    //     var latLng = [parseFloat(item.latitude), parseFloat(item.longitude)];
                    //     var popupContent = `<strong>Time:</strong> ${item.time}<br><strong>Status :</strong> ${item.status}`;

                    //     var triangleIcon = L.divIcon({
                    //         className: 'custom-triangle-icon',
                    //         html: '<div style="width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-bottom: 12px solid black;"></div>',
                    //         iconSize: [12, 12], // Size of the icon
                    //         iconAnchor: [6, 12] // Center of the icon
                    //     });

                    //     var marker = L.marker(latLng, { icon: triangleIcon }).addTo(map)
                    //         .bindPopup(popupContent)
                    //         .on('click', function() {
                    //             this.openPopup();
                    //         });

                    //     // Set marker rotation if needed
                    //     if (item.course !== undefined) {
                    //         marker.setRotationAngle(item.course);
                    //     }
                    // });                                                                                           

                    

                    // Display data as polylines and markers
                    var coordinates = data.map(function(item) {
                        if (item.latitude !== undefined && item.longitude !== undefined) {
                            return [parseFloat(item.latitude), parseFloat(item.longitude)];
                        } else {
                            console.warn("Invalid data item:", item);
                            return null;
                        }
                    }).filter(function(item) { return item !== null; });

                    if (coordinates.length > 0) {
                        var polyline = L.polyline(coordinates, {color: 'blue'}).addTo(map);

                        var startIcon = L.icon({
                            iconUrl: 'backend/assets/img/illustrations/on.png',
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                        });
                        var endIcon = L.icon({
                            iconUrl: 'backend/assets/img/illustrations/off.png',
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                        });

                        var startMarker = L.marker(coordinates[0], { icon: startIcon }).addTo(map);
                        var endMarker = L.marker(coordinates[coordinates.length - 1], { icon: endIcon }).addTo(map);

                        var rotationAngleStart = data[0].course;
                        var rotationAngleEnd = data[data.length - 1].course;
                        startMarker.setRotationAngle(rotationAngleStart);
                        endMarker.setRotationAngle(rotationAngleEnd);

                        var bounds = L.latLngBounds([coordinates[0], coordinates[coordinates.length - 1]]);
                        map.fitBounds(bounds);

                        var courseValues = data.map(function(item) {
                            return item.course;
                        });

                        $('#playButton').click(function() {  
                            clearInterval(animationInterval);                      
                            animateMarker(startMarker, endMarker, coordinates, courseValues, index);
                            $('#stopButton').prop('disabled', false); 
                            $('#playButton').prop('disabled', true); 
                            $('#pauseButton').prop('disabled', false); 
                        });

                        $('#pauseButton').click(function() {
                            clearInterval(animationInterval);
                            $('#playButton').prop('disabled', false); 
                            $('#pauseButton').prop('disabled', true); 
                            $('#stopButton').prop('disabled', true); 
                            index = index; 
                        });

                        $('#stopButton').click(function() {
                            clearInterval(animationInterval); // Stop animation
                            index = 0;
                            $('#stopButton').prop('disabled', true); 
                            $('#playButton').prop('disabled', false);
                        });
                    }

                    // Display depotMapData as polygons
                    // depotMapData.forEach(function(item) {
                    //     var coordinates = item.polygon.split(',').map(function(coord) {
                    //         var [long, lat] = coord.trim().split(' ');
                    //         return [parseFloat(lat), parseFloat(long)];
                    //     });
                    //     L.polygon(coordinates, {color: 'red'}).addTo(map).bindPopup(item.name);
                    // });
                },

                error: function(error) {
                    console.error("Error fetching data:", error);

                    var errorMessage = "Error fetching data.";
                    if (error.responseJSON && error.responseJSON.errors) {
                        errorMessage = "<ul>";
                        $.each(error.responseJSON.errors, function(field, messages) {
                            errorMessage += "<li><strong>" + field.replace('_', ' ') + ":</strong> " + messages.join(", ") + "</li>";
                        });
                        errorMessage += "</ul>";
                    }

                    $('#errorModalBody').html(errorMessage);
                    $('#errorModal').modal('show');
                }
            });
        });


        function animateMarker(startMarker, endMarker, coordinates, courseValues, index) {
            index = 0;
            var totalSteps = coordinates.length; // Jumlah total langkah adalah jumlah titik dalam polyline
            var polyline = L.polyline([], {color: 'blue'}).addTo(map); // Membuat polyline kosong dengan warna awal

            animationInterval = setInterval(function() {
                if (index < totalSteps) {
                    var currentLatLng = coordinates[index];
                    var rotationAngle = courseValues[index]; // Mengambil nilai course yang sesuai dengan indeks saat ini
                    
                    startMarker.setLatLng(currentLatLng); // Set posisi marker saat ini ke posisi titik pada polyline
                    startMarker.setRotationAngle(rotationAngle); // Set rotasi marker
                    
                    // Perbarui polyline dengan bagian yang sudah dilewati
                    var passedCoords = coordinates.slice(0, index + 1);
                    if (polyline) {
                        map.removeLayer(polyline); // Hapus polyline yang ada
                    }
                    polyline = L.polyline(passedCoords, {color: 'red'}).addTo(map); // Buat polyline baru dengan bagian yang sudah dilewati diberi warna biru
                    
                    // Perbarui tampilan peta untuk memfokuskan marker yang bergerak
                    map.setView(currentLatLng, map.getZoom()); // 'map' adalah nama variabel peta Anda
                    
                    index++;
                } else {
                    clearInterval(animationInterval);
                    $('#stopButton').prop('disabled', true); 
                    $('#pauseButton').prop('disabled', true);                      
                    $('#playButton').prop('disabled', false); 
                }
            }, 500); // Interval waktu yang lebih kecil untuk membuat pergerakan lebih mulus
        }
    });
</script>
@endpush
