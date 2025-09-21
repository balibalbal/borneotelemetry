@push('style')
<!-- Leaflet CSS -->
{{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

<!-- Leaflet JS -->
{{-- <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
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

    /* Tooltip untuk kendaraan bergerak */
    .tooltip-bergerak {
        background-color: green;
        color: white;
        font-weight: bold;
        border-radius: 4px;
        padding: 2px 6px;
    }

    /* Tooltip untuk kendaraan mati */
    .tooltip-mati {
        background-color: red;
        color: white;
        font-weight: bold;
        border-radius: 4px;
        padding: 2px 6px;
    }

    /* Tooltip untuk kendaraan diam */
    .tooltip-diam {
        background-color: black;
        color: white;
        font-weight: bold;
        border-radius: 4px;
        padding: 2px 6px;
    }

    /* Tooltip untuk kendaraan berhenti */
    .tooltip-berhenti {
        background-color: yellow;
        color: black;
        font-weight: bold;
        border-radius: 4px;
        padding: 2px 6px;
    }

    /* Tooltip default */
    .tooltip-default {
        background-color: blue;
        color: white;
        font-weight: bold;
        border-radius: 4px;
        padding: 2px 6px;
    }

    /* Styling untuk legend */
    .info.legend {
        /* background-color: rgba(255, 255, 255, 0.8);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); */
        font-size: 10px;
        /* line-height: 1.6; */
        max-width: 100%;
        min-width: 200px;
        box-sizing: border-box;
    }

    .nearby-vehicle {
        font-size: 12px;
        margin-bottom: 5px;
    }

    @media (max-width: 600px) {
        .info.legend {
            font-size: 10px; /* Mengurangi ukuran font untuk layar kecil */
            padding: 8px; /* Mengurangi padding */
        }
    }

</style>
@endpush

@extends('layouts.admin')
@section('title', 'Monitoring Kendaraan')
@section('content')

<div class="container-fluid">     
    <div class="row mt-3">        
        <div class="col-sm-6 col-lg-2 mb-4">
            <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-danger"
                            ><i class="mdi mdi-truck-remove mdi-20px"></i
                          ></span>
                        </div>
                        <span class="ms-1 mb-0" id="total-mati-count"></span>
                      </div>
                      <p class="mb-0 text-heading">Mati</p>
                    </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-2 mb-4">
                  <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-success"
                            ><i class="mdi mdi-truck-check mdi-20px"></i
                          ></span>
                        </div>
                        <span class="ms-1 mb-0" id="total-bergerak-count"></span>
                      </div>
                      <p class="mb-0 text-heading">Bergerak</p>
                    </div>
                  </div>
        </div>

        <div class="col-sm-6 col-lg-2 mb-4">
            <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-warning">
                            <i class="mdi mdi-truck mdi-20px"></i
                          ></span>
                        </div>
                        <span class="ms-1 mb-0" id="total-berhenti-count"></span>
                      </div>
                      <p class="mb-0 text-heading">Berhenti</p>
                    </div>
            </div>
        </div>
            
        <div class="col-sm-6 col-lg-2 mb-4">
            <div class="card card-border-shadow-dark h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-dark">
                            <i class="mdi mdi-truck-alert mdi-20px"></i>
                          </span>
                        </div>
                        <span class="ms-1 mb-0" id="total-diam-count"></span>
                    </div>
                    <p class="mb-0 text-heading">Diam</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <a href="/traccar" class="text-white">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-primary"
                            ><i class="mdi mdi-bus-school mdi-20px"></i
                          ></span>
                        </div>
                        <span class="ms-1 mb-0" id="total-vehicles-count"></span>
                      </div>
                      <p class="mb-0 text-heading">Total Kendaraan</p>
                    </div>
                </div>
            </a>
        </div>
                    
    </div> 
    <div class="row gy-4">
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
        <div class="col-12 col-lg-9 mb-4 mb-xl-0">
            <div class="card">
                {{-- <div class="card-body"> --}}
                    <div id="map" style="height: 530px;"></div>
                {{-- </div> --}}
            </div>
        </div> 
    </div>      
</div>

@endsection

@push('scripts')

<script>
    var mapData = [];
    var mymap = L.map('map', {
            center: [-3.854650, 116.160910],
            zoom: 5,
        });

    // Definisi berbagai jenis peta
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mymap);

    var googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google Satellite'
    });

    var googleTerrain = L.tileLayer('https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google Terrain'
    });

    var esriWorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; Esri World Imagery'
    });



        // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        // }).addTo(mymap);

    // Tambahkan kontrol untuk memilih layer
    var baseMaps = {
        "Google Terrain": googleTerrain,
        "OpenStreetMap": osm,
        "Google Satellite": googleSat,
        "Esri World Imagery": esriWorldImagery,
        // "OpenTopoMap": openTopoMap
    };

    // Tambahkan kontrol pilihan peta ke dalam map
    L.control.layers(baseMaps).addTo(mymap);
    
    $(document).ready(function () {
        var legendControlVisible = false;
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
                        updateMap(mapData);
                        // console.log(response);
                    }

                    updateVehicleCounts(response);
                    // console.log('data:', response.totalVehicles);
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
            // $('#total-customer').text(response.totalCustomer+ ' Customer');
            $('#kecepatan').text(response.noPol+ ' - ' +response.maxSpeed+ ' Km/h');
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

                if (data.vehicle_type === 0) {
                    // console.log('helo mobil')
                    // Vehicle type 0
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
                } else if (data.vehicle_type === 1) {
                    // console.log('helo motor')
                    // Vehicle type 1
                    switch (data.status) {
                        case 'mati':
                            carImage = 'backend/assets/img/illustrations/off-mtr.png';
                            break;
                        case 'bergerak':
                            carImage = 'backend/assets/img/illustrations/on-mtr.png';
                            break;
                        case 'diam':
                            carImage = 'backend/assets/img/illustrations/ack-mtr.png';
                            break;
                        case 'berhenti':
                            carImage = 'backend/assets/img/illustrations/engine-mtr.png';
                            break;
                        default:
                            carImage = 'backend/assets/img/illustrations/default-mtr.png';
                    }
                } else {
                    // console.log('gak ada status nih')
                    // Default case if vehicle_type is neither 0 nor 1
                    carImage = 'backend/assets/img/illustrations/default.png';
                }


                var tooltipClass;
                switch (data.status) {
                    case 'mati':
                        tooltipClass = 'tooltip-mati';
                        break;
                    case 'bergerak':
                        tooltipClass = 'tooltip-bergerak';
                        break;
                    case 'diam':
                        tooltipClass = 'tooltip-diam';
                        break;
                    case 'berhenti':
                        tooltipClass = 'tooltip-berhenti';
                        break;
                    default:
                        tooltipClass = 'tooltip-default';
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
                    marker.setTooltipContent(data.name);
                    //marker.bindPopup(marker.getPopup().getContent()).openPopup();
                    moveMarkerSmoothly(marker, currentLatLng, newLatLng);
                } else {
                    // Jika belum ada, buat marker baru
                    marker = L.marker([data.latitude, data.longitude], { icon: icon, rotationAngle: rotationAngle }).addTo(mymap);
                    marker.bindPopup(getPopupContent(data))
                    .bindTooltip(data.name, {
                        permanent: true,
                        direction: "top",
                        offset: [0, -10],
                        className: tooltipClass // Set class tooltip sesuai status
                    });

                    // Simpan marker kendaraan ke dalam objek vehicleMarkers
                    vehicleMarkers[data.id] = marker;
                }                

                // Buat daftar kendaraan di samping peta
                // Menangani klik pada item daftar kendaraan
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
                            // Move map to the clicked vehicle's location
                            mymap.flyTo([data.latitude, data.longitude], 18);
                            var popup = L.popup().setLatLng([data.latitude, data.longitude]).setContent(getPopupContent(data));
                            marker.bindPopup(popup).openPopup();

                            // Panggil AJAX untuk mendapatkan kendaraan terdekat berdasarkan id kendaraan
                            $.ajax({
                                url: '/get-nearby-vehicles',
                                method: 'GET',
                                data: { 
                                    vehicle_id: data.vehicle_id,
                                    geo_point: data.geo_point
                                 },  // Mengirimkan ID kendaraan yang diklik
                                success: function(response) {
                                    if (response.error) {
                                        console.error('Gagal mengambil kendaraan terdekat:', response.error);
                                    } else {
                                        // console.log(response);
                                        // Memperbarui legend dengan kendaraan terdekat
                                        updateLegend(response.vehicles);

                                        if (!legendControlVisible) {
                                            legendControl.addTo(mymap); // Menambahkan legendControl ke peta
                                            legendControlVisible = true; // Update status legendControl
                                        }
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX request gagal:', error);
                                }
                            });
                        } else {
                            console.warn('Invalid coordinates for the clicked item.');
                        }
                    });

                $('#dataList').append(listItem);
            });            
        }        

        function getDirection(course) {
            if (course >= 337.5 || course < 22.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke utara</span>';
                } else if (course >= 22.5 && course < 67.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke timur laut</span>';
                } else if (course >= 67.5 && course < 112.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke timur</span>';
                } else if (course >= 112.5 && course < 157.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke tenggara</span>';
                } else if (course >= 157.5 && course < 202.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke selatan</span>';
                } else if (course >= 202.5 && course < 247.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke barat daya</span>';
                } else if (course >= 247.5 && course < 292.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke barat</span>';
                } else if (course >= 292.5 && course < 337.5) {
                    return '<span class="badge rounded-pill bg-primary">Menghadap ke barat laut</span>';
                }
        }

        function getAccelerationRoll(roll) {
            if (roll > 0) {
                return `<span>Miring Ke Kanan ${roll}&deg;</span>`;
            } else if (roll < 0) {
                return `<span>Miring Ke Kiri ${roll}&deg;</span>`;
            } else {
                return `<span>Kemiringan ${roll}&deg;</span>`;
            }
        }

        function getAccelerationPitch(pitch) {
            if (pitch > 0) {
                return `<span>Miring Ke Belakang ${pitch}&deg;</span>`;
            } else if (pitch < 0) { 
                return `<span>Miring Ke Depan ${pitch}&deg;</span>`;
            } else {
                return `<span>Kemiringan ${pitch}&deg;</span>`;
            }
        }


        function getStatusText(status) {
                    switch (status) {
                        case 'bergerak':
                            return '<span class="badge rounded-pill bg-success">Bergerak</span>';
                        case 'mati':
                            return '<span class="badge rounded-pill bg-danger">Mati</span>';
                        case 'diam':
                            return '<span class="badge rounded-pill bg-dark">Diam</span>';
                        case 'berhenti':
                            return '<span class="badge rounded-pill bg-warning">Berhenti</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }

        function getIgnitionText(ignition) {
                    switch (ignition) {
                        case 'On':
                            return '<span class="badge rounded-pill bg-warning">On</span>';
                        case 'Off':
                            return '<span class="badge rounded-pill bg-danger">Off</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }

        function getChargingText(charging) {
                    switch (charging) {
                        case 'true':
                            return '<span class="badge rounded-pill bg-primary">Ya</span>';
                        case 'false':
                            return '<span class="badge rounded-pill bg-danger">Tidak</span>';
                        default:
                            return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
                    }
                }
        function getGPSText(gpsTracking) {
                    switch (gpsTracking) {
                        case 'true':
                            return '<span class="badge rounded-pill bg-warning">Ya</span>';
                        case 'false':
                            return '<span class="badge rounded-pill bg-danger">Tidak</span>';
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
                        <li>Update Terakhir: ${data.time}</li>
                        <li>Status: ${getStatusText(data.status)}</li>
                        <li>Kecepatan: ${data.speed} Km/h</li>
                        <li>Arah: ${direction}</li>
                        <li>Angle: ${data.course}&deg;</li>
                        <li>Altitude: ${data.altitude} m</li>
                        <li>X: ${data.axisx} mG, Y: ${data.axisy} mG, Z: ${data.axisz} mG</li>
                        <li>Roll: ${getAccelerationRoll(data.roll)}</li>
                        <li>Pitch: ${getAccelerationPitch(data.pitch)}</li>
                        <li>Ignition: ${getIgnitionText(data.ignition)}</li>
                        <li>LatLong: ${data.latitude}, ${data.longitude}</li>
                        <li>Alamat: ${data.address}</li>
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

        // Menambahkan kontrol legend di pojok kiri atas
        var legendControl = L.control({position: 'bottomleft'}); // Posisi di atas kanan

        legendControl.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend');
            div.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p style="margin: 0; font-weight: bold; color: #000;">Kendaraan Terdekat</p>
                    <button id="closeLegendBtn" style="border:none; background:none; font-size:16px; cursor:pointer;">&times;</button>
                </div>
                <div id="nearby-vehicles-legend" style="display: flex; overflow-x: auto; white-space: nowrap; gap: 10px; padding-top: 5px;"></div>
                `;

            return div;
        };

        legendControl.addTo(mymap);

        setTimeout(() => {
            document.getElementById('closeLegendBtn')?.addEventListener('click', () => {
                // Sembunyikan legendControl
                document.querySelector('.legend').style.display = 'none';
                legendControlVisible = false; // Update status menjadi tidak terlihat
            });
        }, 500);

        // Fungsi untuk memperbarui konten legend
        function updateLegend(vehicles) {
            var legendContent = '';

            if (vehicles && vehicles.length > 0) {
                // Looping melalui data kendaraan terdekat
                var nearbyVehicles = vehicles.map(function(vehicle) {
                    var noPol = vehicle.no_pol;
                    var speed = vehicle.speed;
                    var status = vehicle.status;
                    var distance = parseFloat(vehicle.distance).toFixed(2); // Format jarak ke dua angka desimal

                    return `
                        <div style="flex: 0 0 auto; background: #fff; border-radius: 5px; padding: 10px; min-width: 100px;">
                            <strong>${noPol}</strong><br>
                            Speed: ${speed} km/h<br>
                            Status: ${getStatusText(status)}<br>
                            Distance: ${distance} m
                        </div>
                    `;
                });

                // Gabungkan semua informasi kendaraan menjadi satu string
                legendContent = nearbyVehicles.join('');
            } else {
                legendContent = '<p>Tidak ada kendaraan berdekatan.</p>';
            }

            // Menampilkan hasil di elemen dengan ID 'nearby-vehicles-legend'
            document.getElementById('nearby-vehicles-legend').innerHTML = legendContent;
        }

        fetchDataAndRefreshMap();

        setInterval(fetchDataAndRefreshMap, 30000);
    });
    
</script>
@endpush
