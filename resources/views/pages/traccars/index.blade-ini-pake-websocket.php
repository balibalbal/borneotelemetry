@push('style')

{{-- <link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/typeahead-js/typeahead.css') }}" /> --}}
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/leaflet/leaflet.css') }}" />

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
@section('title', 'Dashboard Mtrack')
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
                                <b>Top Speed</b>
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
                <div class="leaflet-map" id="map" style="height: 530px;"></div>
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
@vite('resources/js/app.js')
@endsection

@push('scripts')
<script src="{{ url('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ url('backend/assets/vendor/libs/leaflet/leaflet.js') }}"></script>
<script src="{{ url('backend/assets/vendor/libs/leaflet/leaflet.rotatedMarker.js') }}"></script>
<script src="{{ url('backend/assets/js/maps-leaflet.js') }}"></script>

<script>
    $(document).ready(function() {
    // Inisialisasi peta
    var map = L.map('map').setView([-7.178288992633926, 107.27250526723148], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var vehicleMarkers = {};

    // Fungsi untuk menambahkan marker
    function addMarker(data) {
        var latLng = [parseFloat(data.latitude), parseFloat(data.longitude)];
        var icon = L.icon({
            iconUrl: getCarImage(data.status),
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [-5, -15]
        });

        if (vehicleMarkers[data.id]) {
            // Update marker jika sudah ada
            var marker = vehicleMarkers[data.id];
            marker.setLatLng(latLng);
            marker.setIcon(icon);
            marker.setRotationAngle(parseFloat(data.course));
            marker.setPopupContent(getPopupContent(data));
            moveMarkerSmoothly(marker, marker.getLatLng(), latLng);
        } else {
            // Tambah marker baru jika belum ada
            var marker = L.marker(latLng, { icon: icon, rotationAngle: parseFloat(data.course) }).addTo(map);
            marker.bindPopup(getPopupContent(data));
            vehicleMarkers[data.id] = marker;
        }
        updateListGroup(data);
    }

    // Memanggil data awal dengan AJAX
    function fetchInitialData() {
        $.ajax({
            url: '/get-traccar-data', // Ganti dengan URL endpoint API Anda
            method: 'GET',
            success: function (response) {
                if (response.mapData && Array.isArray(response.mapData)) {
                    console.log(Array.isArray(response.mapData))
                response.mapData.forEach(vehicle => {
                    console.log('Vehicle Data Api:', vehicle);
                    addMarker(vehicle);
                });
            } else {
                console.error('Data mapData tidak ditemukan atau bukan array:', response);
            }
            },
            error: function (err) {
                console.error('Failed to fetch initial data:', err);
            }
        });
    }

    // Memanggil data awal
    fetchInitialData();

    function createListItem(data) {
        var carImage = getCarImage(data.status);
        return $('<div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer" data-id="' + data.id + '"></div>')
            .append($('<img src="' + carImage + '" alt="Car Image" style="width: 30%;">'))
            .append($('<div class="w-100"></div>')
                .append($('<div class="d-flex justify-content-between"></div>')
                    .append($('<div class="user-info"></div>')
                        .append($('<h6 class="mb-1">' + data.no_pol + '</h6>'))
                        .append($('<div class="d-flex align-items-center"></div>')
                            .append(getStatusText(data.status))
                        )
                        .append($('<small class="text-muted ms-1">' + data.time + '</small>'))
                    )
                )
            )
            .click(function() {
                if (data.latitude && data.longitude) {
                    map.flyTo([parseFloat(data.latitude), parseFloat(data.longitude)], 18);
                    var popup = L.popup().setLatLng([parseFloat(data.latitude), parseFloat(data.longitude)])
                        .setContent(getPopupContent(data));
                    if (vehicleMarkers[data.id]) {
                        vehicleMarkers[data.id].bindPopup(popup).openPopup();
                    }
                } else {
                    console.warn('Invalid coordinates for the clicked item.');
                }
            });
    }
    
    function updateListGroup(data) {
        var listItem = createListItem(data);
        var existingItem = $('#dataList').find(`[data-id="${data.id}"]`);
        //console.log(existingItem);
        if (existingItem.length) {
            existingItem.replaceWith(listItem);
        } else {
            $('#dataList').append(listItem);
        }
    }

    function updateMapMarker(data) {
        var latLng = [parseFloat(data.latitude), parseFloat(data.longitude)];
        if (vehicleMarkers[data.id]) {
            var marker = vehicleMarkers[data.id];
            var currentLatLng = marker.getLatLng();
            marker.setRotationAngle(parseFloat(data.course));
            marker.setIcon(L.icon({
                iconUrl: getCarImage(data.status),
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                popupAnchor: [-5, -15],
            }));
            marker.setPopupContent(getPopupContent(data));
            moveMarkerSmoothly(marker, currentLatLng, L.latLng(latLng));
        } else {
            var marker = L.marker(latLng, { icon: L.icon({
                iconUrl: getCarImage(data.status),
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                popupAnchor: [-5, -15],
            }), rotationAngle: parseFloat(data.course) }).addTo(map);
            marker.bindPopup(getPopupContent(data));
            vehicleMarkers[data.id] = marker;
        }
        updateListGroup(data);
    }

    function moveMarkerSmoothly(marker, fromLatLng, toLatLng) {
        var frames = 100;
        var intervalTime = 2000 / frames;

        var latStep = (toLatLng.lat - fromLatLng.lat) / frames;
        var lngStep = (toLatLng.lng - fromLatLng.lng) / frames;

        var currentLatLng = fromLatLng;
        var count = 0;

        var moveInterval = setInterval(function() {
            count++;
            if (count >= frames) {
                clearInterval(moveInterval);
            } else {
                currentLatLng = L.latLng(currentLatLng.lat + latStep, currentLatLng.lng + lngStep);
                marker.setLatLng(currentLatLng);
            }
        }, intervalTime);
    }

    function getCarImage(status) {
        switch (status) {
            case 'offline': return 'backend/assets/img/illustrations/off.png';
            case 'online': return 'backend/assets/img/illustrations/on.png';
            case 'ack': return 'backend/assets/img/illustrations/ack.png';
            case 'engine': return 'backend/assets/img/illustrations/engine.png';
            default: return 'backend/assets/img/illustrations/default.png';
        }
    }

    function getStatusText(status) {
        switch (status) {
            case 'online': return '<span class="badge rounded-pill bg-success">Bergerak</span>';
            case 'offline': return '<span class="badge rounded-pill bg-danger">Mati</span>';
            case 'engine': return '<span class="badge rounded-pill bg-warning text-dark">Berhenti</span>';
            case 'ack': return '<span class="badge rounded-pill bg-secondary">Diam</span>';
            default: return '<span class="badge rounded-pill bg-secondary">Tidak Dikenal</span>';
        }
    }

    function getPopupContent(data) {
        return `
            <div>
                <h6>${data.no_pol}</h6>
                <ul>
                    <li>Time: ${data.time}</li>
                    <li>Status: ${getStatusText(data.status)}</li>
                    <li>Speed: ${data.speed} km/h</li>
                    <li>Direction: ${getDirection(data.course)}</li>
                    <li>Total Distance: ${data.total_distance} Km</li>
                    <li>Ignition: ${data.ignition_status}</li>
                    <li>Vendor GPS: ${data.vendor_gps}</li>
                </ul>
            </div>
        `;
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

    // Inisialisasi Laravel Echo
    window.Echo.channel('traccar-channel')
        .listen('.update-traccar', (event) => {
            console.log('Vehicle update websocket:', event.traccar);
            updateMapMarker(event.traccar);
        });

    // Implementasikan filter pencarian di list group
    $('#searchInput').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    console.log('Searching for:', value); // Log pencarian
    console.log($.fn.typeahead);

    
    $('#dataList .list-group-item').each(function() {
        var itemText = $(this).text().toLowerCase();
        console.log('Item text:', itemText); // Log item text
        
        if (itemText.indexOf(value) > -1) {
            $(this).show(); // Tampilkan item yang cocok
        } else {
            $(this).hide(); // Sembunyikan item yang tidak cocok
        }
    });
});



});

</script>

@endpush
