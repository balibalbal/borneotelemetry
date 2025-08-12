@push('style')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker/leaflet.rotatedMarker.js"></script>

<style>
    #dataList::-webkit-scrollbar {
        width: 0.0em; /* Atur lebar scrollbar sesuai keinginan Anda */
    }

    #dataList::-webkit-scrollbar-thumb {
        background-color: transparent; /* Warna thumb scrollbar */
    }

    #dataList {
        max-height: 430px; /* Atur tinggi maksimum sesuai keinginan Anda */
        overflow-y: auto; /* Aktifkan pengguliran vertikal */
    }
</style>
@endpush

@extends('layouts.admin')
@section('title', 'Monitoring HSO')
@section('content')

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6 mb-4">
            <a href="/traccar" class="text-white">
              <div class="card bg-secondary text-white shadow">
                  <div class="card-body">                  
                        <b>Total Kendaraan</b>                  
                        <div class="text-white small" id="total-vehicles-count">
                            <i class="mdi mdi-truck-remove"></i> Kendaraan
                        </div>
                  </div>
              </div>
            </a>
          </div>
        
          @if(auth()->user()->customer_id == 163)
                <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6 mb-4">
                    {{-- <a href="" class="text-white"> --}}
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body">
                                <b>Total Customer</b>
                                <div class="text-white small" id="total-customer">
                                    <i class="mdi mdi-truck-remove"></i> Customer
                                </div>
                            </div>
                        </div>
                    {{-- </a> --}}
                </div>
            @else
                <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6 mb-4">
                    {{-- <a href="" class="text-white"> --}}
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body">
                                <b>Masuk Geofence</b>
                                <div class="text-white small" id="kecepatan">
                                    <i class="mdi mdi-truck-remove"></i> Nopol
                                </div>
                            </div>
                        </div>
                    {{-- </a> --}}
                </div>
            @endif
          

          <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6mb-4">
            <a href="/traccar/offline" class="text-white">
              <div class="card bg-danger text-white shadow">
                  <div class="card-body">                  
                          <b>Mati</b>                  
                      <div class="text-white small" id="total-mati-count"><i class="mdi mdi-truck-remove"></i> </div>
                  </div>
              </div>
            </a>
          </div>

          <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6 mb-4">
            <a href="/traccar/online" class="text-white">
              <div class="card bg-success text-white shadow">
                  <div class="card-body">                  
                          <b>Bergerak</b>                  
                      <div class="text-white small" id="total-bergerak-count"><i class="mdi mdi-truck-remove"></i> Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>

          <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6 mb-4">
            <a href="/traccar/engine" class="text-white">
              <div class="card bg-warning text-white shadow">
                  <div class="card-body">                  
                          <b>Berhenti</b>                 
                      <div class="text-white small" id="total-berhenti-count"><i class="mdi mdi-truck-remove"></i> Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>
          
          <div class="col-xl-2 col-md-6 col-xs-6 col-sm-6 mb-4">
            <a href="/traccar/ack" class="text-white">
              <div class="card bg-dark text-white shadow">
                  <div class="card-body">                  
                          <b>Diam</b>                  
                      <div class="text-white small" id="total-diam-count"><i class="mdi mdi-truck-remove"></i> Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>
    </div>

    <div class="row gy-4">
        <div class="col-12 col-lg-9 mb-4 mb-xl-0">
            <div class="card">
                {{-- <div class="card-body"> --}}
                    <div id="map" style="height: 530px;"></div>
                {{-- </div> --}}
            </div>
        </div>        
        <div class="col-12 col-lg-3 mb-4 mb-xl-0">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="demo-inline-spacing mt-3"> --}}
                        <!-- Input pencarian -->
                        <input type="text" id="searchInput" class="form-control mb-3" placeholder="ketik nomor polisi">
                        <!-- List group -->
                        <div class="list-group" id="dataList"></div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>    
</div>

@endsection

@push('scripts')

<script>
    var mapData = [];

    var mymap = L.map('map', {
            center: [-6.889630106229436, 109.67020357636966],
            zoom: 7,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);


    var geofences = @json($geofences);

        geofences.forEach(function (geofence) {
            // Tampilkan circle jika punya center_point dan radius
        if (geofence.center_point && geofence.radius) {
                var center = JSON.parse(geofence.center_point);
                var lat = center.coordinates[1];
                var lng = center.coordinates[0];

                var circle = L.circle([lat, lng], {
                    color: 'blue',
                    radius: geofence.radius
                }).addTo(mymap);

                circle.bindPopup(`<b>Nama:</b> ${geofence.name}`);

       }

            // Tampilkan polygon jika punya polygon_area
        if (geofence.polygon_area) {
                var polygon = JSON.parse(geofence.polygon_area);
                var coordinates = polygon.coordinates[0].map(coord => [coord[1], coord[0]]); // lat, lng

                var shape = L.polygon(coordinates, { color: 'green' }).addTo(mymap);
                shape.bindPopup(`<b>Nama:</b> ${geofence.name}`);

        }
    });

    $(document).ready(function () {
        $('#searchInput').on('input', function () {
            var searchValue = $(this).val().toLowerCase();
            var filteredData = mapData.filter(function (data) {
                return data.name.toLowerCase().includes(searchValue);
            });
            updateMap(filteredData);
        });

        function fetchDataAndRefreshMap() {
            $.ajax({
                url: '/get-traccar-hso',
                method: 'GET',
                success: function (response) {
                    if (response.error) {
                        console.error('Gagal mengambil data:', response.error);
                    } else {
                        mapData = response.mapData;
                        updateMap(mapData);
                        console.log(response);
                    }

                    updateVehicleCounts(response);
                    console.log('data:', response.totalVehicles);
                },
                error: function (xhr, status, error) {
                    console.error('AJAX request gagal:', error);
                }
            });
        }

        function updateVehicleCounts(response) {
            $('#total-vehicles-count').text(response.totalVehicles + ' Kendaraan');
            $('#total-mati-count').text(response.totalOffline + ' Kendaraan');
            $('#total-bergerak-count').text(response.totalOnline + ' Kendaraan');
            $('#total-diam-count').text(response.totalAck + ' Kendaraan');
            $('#total-berhenti-count').text(response.totalEngine + ' Kendaraan');
            $('#total-customer').text(response.totalCustomer+ ' Customer');
            $('#kecepatan').text(response.enterGeofence+ ' Kendaraan');
        }

        // Simpan referensi ke marker sebelumnya untuk setiap kendaraan
        var vehicleMarkers = {};

        function updateMap(mapData) {
            // Variabel lokal untuk menyimpan posisi sebelumnya
            var previousLatLngs = {};

            // Hapus marker kendaraan sebelumnya
            for (var key in vehicleMarkers) {
                previousLatLngs[key] = vehicleMarkers[key].getLatLng(); // Simpan posisi sebelumnya
                //mymap.removeLayer(vehicleMarkers[key]);
            }

            var validMarkers = mapData.filter(function (data) {
                return data.latitude !== 0 && data.longitude !== 0;
            });

            $('#dataList').empty();

            validMarkers.forEach(function (data) {
                var carImage;
                switch (data.status) {
                    case 'mati':
                        carImage = 'backend/assets/img/illustrations/off.png';
                        break;
                    case 'bergerak':
                        carImage = 'backend/assets/img/illustrations/on.png';
                        break;
                    case 'diam':
                        carImage = 'backend/assets/img/illustrations/ack.png';
                        break;
                    case 'berhenti':
                        carImage = 'backend/assets/img/illustrations/engine.png';
                        break;
                    default:
                        carImage = 'backend/assets/img/illustrations/default.png';
                }

                var rotationAngle = data.course;

                var icon = L.icon({
                    iconUrl: carImage,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20],
                    popupAnchor: [-5, -15],
                });
                
                var marker;

                // Cek apakah marker kendaraan sebelumnya sudah ada
                if (vehicleMarkers[data.id]) {
                    // Jika sudah ada, update posisi marker dengan animasi
                    marker = vehicleMarkers[data.id];
                    var currentLatLng = previousLatLngs[data.id]; // Menggunakan posisi sebelumnya
                    var newLatLng = L.latLng(data.latitude, data.longitude); // Tetapkan posisi baru
                    marker.setRotationAngle(rotationAngle);
                    marker.setIcon(icon);
                    marker.setPopupContent(getPopupContent(data));
                    //marker.bindPopup(marker.getPopup().getContent()).openPopup();
                    moveMarkerSmoothly(marker, currentLatLng, newLatLng);
                } else {
                    // Jika belum ada, buat marker baru
                    marker = L.marker([data.latitude, data.longitude], { icon: icon, rotationAngle: rotationAngle }).addTo(mymap);
                    marker.bindPopup(getPopupContent(data));

                    // Simpan marker kendaraan ke dalam objek vehicleMarkers
                    vehicleMarkers[data.id] = marker;
                }

                

                // Buat daftar kendaraan di samping peta
                var listItem = $('<div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer"></div>')
                    .append($('<img src="' + carImage + '" alt="Car Image" style="width: 30%;">'))
                    .append($('<div class="w-100"></div>')
                        .append($('<div class="d-flex justify-content-between"></div>')
                            .append($('<div class="user-info"></div>')
                                .append($('<h6 class="mb-1">' + data.name + '</h6>'))
                                .append($('<div class="d-flex align-items-center"></div>')
                                .append(getStatusText(data.status))
                                )
                                .append($('<small class="text-muted ms-1">' + data.time + '</small>'))
                            )
                        )
                    )
                    .click(function () {
                        if (data.latitude !== 0 && data.longitude !== 0) {
                            mymap.flyTo([data.latitude, data.longitude], 18);
                            var popup = L.popup().setLatLng([data.latitude, data.longitude]).setContent(getPopupContent(data));
                            marker.bindPopup(popup).openPopup();
                        } else {
                            console.warn('Invalid coordinates for the clicked item.');
                        }
                    });

                $('#dataList').append(listItem);
            });            
        }

        

        function getDirection(course) {
            if (course >= 337.5 || course < 22.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke utara.</span>';
                } else if (course >= 22.5 && course < 67.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke timur laut.</span>';
                } else if (course >= 67.5 && course < 112.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke timur.</span>';
                } else if (course >= 112.5 && course < 157.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke tenggara.</span>';
                } else if (course >= 157.5 && course < 202.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke selatan.</span>';
                } else if (course >= 202.5 && course < 247.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke barat daya.</span>';
                } else if (course >= 247.5 && course < 292.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke barat.</span>';
                } else if (course >= 292.5 && course < 337.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke barat laut.</span>';
                }
        }

        function getStatusText(status) {
                    switch (status) {
                        case 'bergerak':
                            return '<span class="badge rounded-pill bg-success">Bergerak</span>';
                        case 'mati':
                            return '<span class="badge rounded-pill bg-danger">Mati</span>';
                        case 'berhenti':
                            return '<span class="badge rounded-pill bg-warning text-dark">Berhenti</span>';
                        case 'diam':
                            return '<span class="badge rounded-pill bg-secondary">Diam</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }

        function getIgnitionText(ignition) {
                    switch (ignition) {
                        case 'true':
                            return '<span class="badge rounded-pill bg-success">On</span>';
                        case 'false':
                            return '<span class="badge rounded-pill bg-danger">Off</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }

        function getChargingText(charging) {
                    switch (charging) {
                        case 'true':
                            return '<span class="badge rounded-pill bg-primary">Yes</span>';
                        case 'false':
                            return '<span class="badge rounded-pill bg-danger">No</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }
        function getGPSText(gpsTracking) {
                    switch (gpsTracking) {
                        case 'true':
                            return '<span class="badge rounded-pill bg-warning">Yes</span>';
                        case 'false':
                            return '<span class="badge rounded-pill bg-danger">No</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }

        // Fungsi untuk membuat konten popup marker
        function getPopupContent(data) {
            var direction = getDirection(data.course);
            var formattedDistance = data.distance ? data.distance.toLocaleString('id-ID') : "0";
            return `
                <div>
                    <h6>${data.name}</h6>
                    <ul>
                        <li>Time: ${data.time}</li>
                        <li>Status: ${getStatusText(data.status)}</li>
                        <li>Speed: ${data.speed} kph</li>
                        <li>Direction: ${direction}</li>
                        <li>Total Distance: ${formattedDistance} Km</li>
                        <li>Ignition: ${getIgnitionText(data.ignition)}</li>
                        <li>Alarm: ${data.alarm}</li>
                        <li>Voltage Level: ${data.voltageLevel}</li>
                        <li>Sinyal: ${data.sinyal}</li>
                        <li>Charging: ${getChargingText(data.charging)}</li>
                        <li>GPS Tracking: ${getGPSText(data.gpsTracking)}</li>
                        <li>Total Distance: ${formattedDistance} Km</li>
                        <li>Address: ${data.address}</li>
                    </ul>
                </div>
            `;
        }

        // Fungsi untuk melakukan animasi perpindahan marker
        function moveMarkerSmoothly(marker, fromLatLng, toLatLng) {
            var frames = 100; // Jumlah frame untuk animasi
            var intervalTime = 2000 / frames; // Waktu interval antara setiap frame

            var latStep = (toLatLng.lat - fromLatLng.lat) / frames;
            var lngStep = (toLatLng.lng - fromLatLng.lng) / frames;

            var currentLatLng = fromLatLng;
            var count = 0;

            var moveInterval = setInterval(function () {
                count++;
                if (count >= frames) {
                    clearInterval(moveInterval);
                } else {
                    currentLatLng = L.latLng(currentLatLng.lat + latStep, currentLatLng.lng + lngStep);
                    marker.setLatLng(currentLatLng);
                }
            }, intervalTime);

            
        }

        fetchDataAndRefreshMap();

        setInterval(fetchDataAndRefreshMap, 30000);
    });
    
</script>


                     


@endpush
