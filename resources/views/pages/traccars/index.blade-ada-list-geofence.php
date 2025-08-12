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
@section('content')

<div class="container-fluid">
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
    <div class="row mt-3">
        <div class="col-xl-2 col-md-6 mb-4">
            <a href="http://127.0.0.1:8000/traccar/offline" class="text-white">
              <div class="card bg-secondary text-white shadow">
                  <div class="card-body">                  
                          Total Kendaraan                  
                          <div class="text-white small" id="total-vehicles-count"><i class="mdi mdi-truck-remove"></i></div>
                  </div>
              </div>
            </a>
          </div>
        
          <div class="col-xl-2 col-md-6 mb-4">
            <a href="http://127.0.0.1:8000/traccar/offline" class="text-white">
              <div class="card bg-primary text-white shadow">
                  <div class="card-body">                  
                          Mati                  
                      <div class="text-white small"><i class="mdi mdi-truck-remove"></i> 16 Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>

          <div class="col-xl-2 col-md-6 mb-4">
            <a href="http://127.0.0.1:8000/traccar/offline" class="text-white">
              <div class="card bg-danger text-white shadow">
                  <div class="card-body">                  
                          Mati                  
                      <div class="text-white small" id="total-mati-count"><i class="mdi mdi-truck-remove"></i> </div>
                  </div>
              </div>
            </a>
          </div>

          <div class="col-xl-2 col-md-6 mb-4">
            <a href="http://127.0.0.1:8000/traccar/offline" class="text-white">
              <div class="card bg-success text-white shadow">
                  <div class="card-body">                  
                          Bergerak                  
                      <div class="text-white small" id="total-bergerak-count"><i class="mdi mdi-truck-remove"></i> 16 Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>

          <div class="col-xl-2 col-md-6 mb-4">
            <a href="http://127.0.0.1:8000/traccar/offline" class="text-white">
              <div class="card bg-warning text-white shadow">
                  <div class="card-body">                  
                          Berhenti                 
                      <div class="text-white small" id="total-berhenti-count"><i class="mdi mdi-truck-remove"></i> 16 Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>
          
          <div class="col-xl-2 col-md-6 mb-0">
            <a href="http://127.0.0.1:8000/traccar/offline" class="text-white">
              <div class="card bg-dark text-white shadow">
                  <div class="card-body">                  
                          Diam (Mesin Off)                  
                      <div class="text-white small" id="total-diam-count"><i class="mdi mdi-truck-remove"></i> 16 Kendaraan</div>
                  </div>
              </div>
            </a>
          </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
    var mapData = [];
    var depotMapData = [];
    var customerMapData = [];

    $(document).ready(function () {
        var mymap = L.map('map', {
            center: [-6.889630106229436, 109.67020357636966],
            zoom: 7,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);

        $('#searchInput').on('input', function () {
            var searchValue = $(this).val().toLowerCase();
            var filteredData = mapData.filter(function (data) {
                return data.name.toLowerCase().includes(searchValue);
            });
            updateMap(filteredData);
        });

        function fetchDataAndRefreshMap() {
            $.ajax({
                url: '/get-traccar-data',
                method: 'GET',
                success: function (response) {
                    if (response.error) {
                        console.error('Gagal mengambil data:', response.error);
                    } else {
                        mapData = response.mapData;
                        depotMapData = response.depotMapData;
                        customerMapData = response.customerMapData;
                        updateMap(mapData, depotMapData, customerMapData);

                        // Setelah memperbarui peta, panggil fungsi checkGeofence dengan validMarkers sebagai parameter
                        checkGeofence(mapData.filter(function (data) {
                            return data.lat !== 0 && data.lon !== 0;
                        }));
                    }

                    updateVehicleCounts(response);
                    
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
        }

        // Simpan referensi ke marker sebelumnya untuk setiap kendaraan
        var vehicleMarkers = {};

        function updateMap(mapData, depotMapData, customerMapData) {
            // Variabel lokal untuk menyimpan posisi sebelumnya
            var previousLatLngs = {};

            // Hapus marker kendaraan sebelumnya
            for (var key in vehicleMarkers) {
                previousLatLngs[key] = vehicleMarkers[key].getLatLng(); // Simpan posisi sebelumnya
                //mymap.removeLayer(vehicleMarkers[key]);
            }

            var validMarkers = mapData.filter(function (data) {
                return data.lat !== 0 && data.lon !== 0;
            });

            $('#dataList').empty();

            validMarkers.forEach(function (data) {
                var carImage;
                switch (data.status) {
                    case 'offline':
                        carImage = 'backend/assets/img/illustrations/off.png';
                        break;
                    case 'online':
                        carImage = 'backend/assets/img/illustrations/on.png';
                        break;
                    case 'ack':
                        carImage = 'backend/assets/img/illustrations/ack.png';
                        break;
                    case 'engine':
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
                    var newLatLng = L.latLng(data.lat, data.lon); // Tetapkan posisi baru
                    marker.setRotationAngle(rotationAngle);
                    marker.setIcon(icon);
                    marker.setPopupContent(getPopupContent(data));
                    //marker.bindPopup(marker.getPopup().getContent()).openPopup();
                    moveMarkerSmoothly(marker, currentLatLng, newLatLng);
                } else {
                    // Jika belum ada, buat marker baru
                    marker = L.marker([data.lat, data.lon], { icon: icon, rotationAngle: rotationAngle }).addTo(mymap);
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
                                    .append(data.status)
                                )
                                .append($('<small class="text-muted ms-1">' + data.time + '</small>'))
                            )
                        )
                    )
                    .click(function () {
                        if (data.lat !== 0 && data.lon !== 0) {
                            mymap.flyTo([data.lat, data.lon], 18);
                            var popup = L.popup().setLatLng([data.lat, data.lon]).setContent(getPopupContent(data));
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
                return 'Menghadap ke utara.';
            } else if (course >= 22.5 && course < 67.5) {
                return 'Menghadap ke timur laut.';
            } else if (course >= 67.5 && course < 112.5) {
                return 'Menghadap ke timur.';
            } else if (course >= 112.5 && course < 157.5) {
                        return 'Menghadap ke tenggara.';
                    } else if (course >= 157.5 && course < 202.5) {
                        return 'Menghadap ke selatan.';
                    } else if (course >= 202.5 && course < 247.5) {
                        return 'Menghadap ke barat daya.';
            } else if (course >= 247.5 && course < 292.5) {
                return 'Menghadap ke barat.';
            } else if (course >= 292.5 && course < 337.5) {
                return 'Menghadap ke barat laut.';
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
                        <li>Status: ${data.status}</li>
                        <li>Speed: ${data.speed} kph</li>
                        <li>Direction: ${direction}</li>
                        <li>Total Distance: ${formattedDistance} Km</li>
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

            depotMapData.forEach(function (depot) {
                var center = [depot.lat, depot.lon];
                var radius = depot.radius;
                var address = depot.address;
            if (center[0] !== undefined && center[1] !== undefined && radius !== undefined && radius > 0) {
                    L.circle(center, {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(mymap).bindPopup(`<b>Depo/Pelabuhan: ${depot.name}</b><br><b>Radius:</b> ${radius} meters<br><b>Alamat:</b> ${address}`);
                }
            });

            customerMapData.forEach(function (customers) {
                var center = [customers.lat, customers.lon];
                var radius = customers.radius;
                var address = customers.address;
            if (center[0] !== undefined && center[1] !== undefined && radius !== undefined && radius > 0) {
                    L.circle(center, {
                        color: '#FFAF45',
                        fillColor: '#FFAF45',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(mymap).bindPopup(`<b>Customer/Pabrik: ${customers.name}</b><br><b>Radius:</b> ${radius} meters<br><b>Alamat:</b> ${address}`);
                }
            });
        }

        function checkGeofence(validMarkers) {
            validMarkers.forEach(function (data) {
                var markerLatLng = L.latLng(data.lat, data.lon);

                // Periksa geofence depo
                depotMapData.forEach(function (depot) {
                    var depotCenter = L.latLng(depot.lat, depot.lon);
                    var depotRadius = depot.radius;

                    var distanceToDepot = markerLatLng.distanceTo(depotCenter);
                    if (distanceToDepot <= depotRadius) {
                        console.log(`Kendaraan ${data.name} berada di dalam geofence depo ${depot.name}.`);
                    }
                });

                // Periksa geofence pelanggan
                customerMapData.forEach(function (customer) {
                    var customerCenter = L.latLng(customer.lat, customer.lon);
                    var customerRadius = customer.radius;

                    var distanceToCustomer = markerLatLng.distanceTo(customerCenter);
                    if (distanceToCustomer <= customerRadius) {
                        console.log(`Kendaraan ${data.name} berada di dalam geofence pelanggan ${customer.name}.`);
                    }
                });
            });
        }

        

        fetchDataAndRefreshMap();

        setInterval(fetchDataAndRefreshMap, 30000);
    });
</script>


                     


@endpush
