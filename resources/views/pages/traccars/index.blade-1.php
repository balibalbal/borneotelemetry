@push('style')
    <!-- Leaflet CSS -->
    {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

    <!-- Leaflet JS -->
    {{-- <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker/leaflet.rotatedMarker.js"></script>
    
<style>
    .bg-custom {
        background-color: #86B6F6;
        color: #ffffff;
        transition: background-color 0.3s ease;
    }

    .bg-custom:hover {
        background-color: #6a9bd2; 
        color: #ffffff;
    }

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
@section('title', 'Monitoring Order')
@section('content')
<div class="container-fluid">  
    <div class="row gy-4 mb-3">
        {{-- <div class="col-lg-12 col-12">
            <div class="card mb-2">
              <div class="card-body d-flex justify-content-between">    
                <h5 class="card-title mb-0">
                    Unduh Monitoring Order
                </h5>
                <a href="{{ route('monitoring.export') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-download-circle"></i> Unduh Monitoring Order</a>
              </div>
            </div>
        </div> --}}

        <div class="col-12 col-lg-3 mb-4 mb-xl-0">            
            <div class="card">
                <div class="card-body">
                    <!-- Input pencarian -->
                    <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-car-search"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="searchInput"
                            placeholder="ketik nomor polisi"
                            class="form-control"/>
                          <label for="searchInput">Cari Nomor Polisi</label> 
                        </div>
                    </div>                  
                      
                    <div class="list-group" id="dataList"></div>  
                                        
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9 mb-4 mb-xl-0">
            <div class="card mb-2">
                {{-- <div class="card-body d-flex justify-content-between">
                  <a href="{{ route('monitoring.list-order', ['status' => 2]) }}" class="btn rounded-pill btn-warning waves-effect waves-light"><i class="mdi mdi-warehouse"></i> &nbsp; {{ $inDepoCount }} Depo</a>
                  <a href="{{ route('monitoring.list-order', ['status' => 3]) }}" class="btn rounded-pill bg-custom waves-effect waves-light"><i class="mdi mdi-truck-fast"></i> &nbsp; {{ $otwCount }} OTW</a>
                  <a href="{{ route('monitoring.list-order', ['status' => 4]) }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-home-city"></i> &nbsp; {{ $inCustomerCount }} Cust</a>
                  <a href="{{ route('monitoring.list-order', ['status' => 5]) }}" class="btn rounded-pill btn-info waves-effect waves-light"><i class="mdi mdi-truck-delivery"></i> &nbsp; {{ $inBackCount }} Back</a>
                  <a href="{{ route('monitoring.list-order', ['status' => 6]) }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-check-circle"></i> &nbsp; {{ $finishCount }} Finish</a>
                </div> --}}
            </div>
            <div class="card">
                {{-- <div class="card-body"> --}}
                    <div id="map" style="height: 530px;"></div>
                {{-- </div> --}}
            </div>
        </div>        
    </div> 

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">

                <!-- MAP -->
                <div id="modalMap" style="height:450px; width:100%; border-radius:8px 8px 0 0;"></div>

                <!-- INFO -->
                <div class="p-4">
                <h6>Informasi Kendaraan</h6>
                <hr>
                <ul id="vehicleInfo" class="list-unstyled"></ul>
                </div>

            </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    

</div>



@endsection

@push('scripts')

<script>
    var mapData = [];

    $(document).ready(function () {
        fetchDataAndRefreshMap();

        var mymap = L.map('map', {
            center: [-6.889630106229436, 109.67020357636966],
            zoom: 7,
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
            "OpenStreetMap": osm,
            "Google Terrain": googleTerrain,
            "Google Satellite": googleSat,
            "Esri World Imagery": esriWorldImagery,
            // "OpenTopoMap": openTopoMap
        };

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
                    console.log(response);
                    if (response.error) {
                        //console.error('Gagal mengambil data:', response.error);
                        console.error('Gagal mengambil data');
                    } else {
                        mapData = response.mapData;
                        updateMap(mapData);
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

            validMarkers.forEach(function (data, index) {
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

                

                // Membuat elemen list group
                var listItem = `
                    <div class="list-group-item list-group-item-action" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                        <div class="d-flex align-items-center">
                            <img src="${carImage}" alt="Car Image" style="width: 30%; margin-right: 10px;">
                            <div>
                                <h6 class="mb-1">${data.no_pol}</h6>
                                ${getStatusText(data.status)}<br>
                                <span style="font-size: 11px;" class="badge rounded-pill  bg-label-primary">${data.time}</span>
                            </div>
                        </div>
                        <div class="collapse" id="collapse${index}">
                            <hr>
                            <div class="accordion-body">
                                <ul class="timeline ps-3 mt-4">                                    
                                    <li class="timeline-item ps-4 border-transparent">
                                        <span class="timeline-indicator-advanced text-secondary border-0 shadow-none">
                                            <i class="mdi mdi-map-marker-outline"></i>
                                        </span>
                                        <div class="timeline-event ps-1 pb-2">
                                            <div class="timeline-header">
                                                <small class="text-secondary text-uppercase">${data.address}</small>
                                            </div>
                                        </div>
                                    </li>                                           
                                </ul>
                                <hr>
                                <div class="text-center">
                                    <span class="badge rounded-pill btn-primary waves-effect waves-light" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#detailModal" data-order-details='${JSON.stringify(data)}'>
                                        <i class="mdi mdi-clipboard-list me-sm-1"></i> Detail Kendaraan
                                    </span>
                                </div>
                            </div>                                
                        </div>
                    </div>                    
                `;

                // Menambahkan elemen list group ke dalam elemen dengan id dataList
                $('#dataList').append(listItem);

                // Menambahkan event listener untuk setiap tombol dalam list group
                $(`#collapse${index}`).on('show.bs.collapse', function () {
                    if (data.latitude !== 0 && data.longitude !== 0) {
                        mymap.flyTo([data.latitude, data.longitude], 18);
                        var popup = L.popup().setLatLng([data.latitude, data.longitude]).setContent(getPopupContent(data));
                        marker.bindPopup(popup).openPopup();
                    } else {
                        console.warn('Invalid coordinates for the clicked item.');
                    }
                });

                // Menambahkan event listener untuk setiap collapse
                $(`.collapse`).on('show.bs.collapse', function () {
                    // Menutup collapse lain yang sedang terbuka
                    $(`.collapse.show`).each(function () {
                        if (this !== event.target) {
                            $(this).collapse('hide');
                        }
                    });
                });
            });
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

        function getStatusOrderText(status_order) {
            switch (status_order) {
                case 0:
                    return '<span class="badge rounded-pill bg-danger">Menunggu Assign</span>';
                case 1:
                    return '<span class="badge rounded-pill bg-primary">Menuju Depo</span>';
                case 2:
                    return '<span class="badge rounded-pill bg-warning">Tiba di Depo</span>';
                case 3:
                    return '<span class="badge rounded-pill bg-custom">OTW</span>';
                case 4:
                    return '<span class="badge rounded-pill bg-dark">Di Customer</span>';
                case 5:
                    return '<span class="badge rounded-pill bg-info">Back</span>';
                case 6:
                    return '<span class="badge rounded-pill bg-success">Selesai</span>';
                case 7:
                    return '<span class="badge rounded-pill bg-danger">Menunggu Antrian</span>';
                        
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
                        <li>Ignition: ${getIgnitionText(data.ignition)}</li>
                        <li>LatLong: ${data.latitude}, ${data.longitude}</li>
                        <li>Alamat: ${data.address}</li>
                    </ul>
                </div>
            `;
        }

        
        // Fungsi untuk melakukan animasi perpindahan marker
        function moveMarkerSmoothly(marker, fromLatLng, toLatLng) {
            //console.log('fromLatLng:', fromLatLng);
            //console.log('toLatLng:', toLatLng);

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

        let modalLeafletMap;

        $('#detailModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const orderDetails = button.data('order-details');

            // Isi informasi kendaraan
            const infoList = `
                <li><b>No. Polisi:</b> <span class="badge bg-danger">${orderDetails.no_pol}</span></li>
                <li><b>Timestamp:</b> ${orderDetails.time}</li>
                <li><b>LatLong:</b> ${orderDetails.latitude}, ${orderDetails.longitude}</li>
                <li><b>Status Kendaraan:</b> ${getStatusText(orderDetails.status)}</li>
                <li><b>Kecepatan:</b> ${orderDetails.speed} kph</li>
                <li><b>Arah Kendaraan:</b> ${getDirection(orderDetails.course)}</li>
                <li><b>Angle:</b> <i>${orderDetails.course}</i></li>
            `;
            $('#vehicleInfo').html(infoList);

            // Tunggu modal tampil penuh, baru render peta
            $('#detailModal').on('shown.bs.modal', function () {
                if (modalLeafletMap) {
                modalLeafletMap.remove(); // hapus map lama
                }

                modalLeafletMap = L.map('modalMap').setView(
                [orderDetails.latitude, orderDetails.longitude], 
                18 // lebih dekat
                );

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
                }).addTo(modalLeafletMap);

                L.marker([orderDetails.latitude, orderDetails.longitude])
                .addTo(modalLeafletMap)
                .bindPopup(`<b>${orderDetails.no_pol}</b><br>${orderDetails.address}`)
                .openPopup();
            });
        });

    });
</script>
@endpush

