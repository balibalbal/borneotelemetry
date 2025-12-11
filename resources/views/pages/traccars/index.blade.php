@push('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <style>
        /* RESET YANG BENAR */
        /* html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        } */

        /* ===== LAYOUT UTAMA ===== */
        .container-fluid.p-0 {
            height: calc(100vh - 56px);
            display: flex;
            flex-direction: column;
        }

        /* ===== WRAPPER UTAMA ===== */
        .wrapper {
            flex: 1;
            display: flex;
            gap: 10px;
            background: #f8f9fa;
            overflow: hidden;
            position: relative;
        }

        /* ===== CARD STYLE ===== */
        .panel-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
        }

        .panel-card .card {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .panel-card .card-header {
            background: linear-gradient(135deg, #00FF9C 0%, #72BF78 100%);
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 12px 20px;
            font-weight: 600;
        }

        .panel-card .card-body {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }

        /* ===== SIDEBAR KIRI (DAFTAR KENDARAAN) ===== */
        #sidebar {
            width: 320px;
            min-width: 320px;
            transition: all 0.3s ease;
        }

        #sidebar.hidden {
            min-width: 0;
            width: 0 !important;
            margin-right: 0;
            overflow: hidden;
        }

        /* ===== MAP AREA ===== */
        #map-container {
            flex: 1;
            min-width: 0;
            position: relative;
        }

        #map-container .card {
            border: none;
        }

        #map-container .card-body {
            padding: 0;
        }

        #map {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border-radius: 0 0 10px 10px;
        }

        /* ===== PANEL DETAIL KANAN ===== */
        #detailPanel {
            width: 320px;
            min-width: 320px;
            transition: all 0.3s ease;
        }

        #detailPanel.hidden {
            min-width: 0;
            width: 0 !important;
            margin-left: 0;
            overflow: hidden;
        }

        /* ===== SEARCH BOX ===== */
        #searchInput {
            margin: 15px;
            padding: 10px 15px;
            width: calc(100% - 30px);
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            transition: all 0.3s;
        }

        #searchInput:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        /* ===== LIST KENDARAAN ===== */
        #list {
            padding: 0 10px 10px 10px;
        }

        .vehicle-item {
            padding: 12px 15px;
            margin: 0 5px 8px 5px;
            cursor: pointer;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            background: white;
            transition: all 0.2s;
        }

        .vehicle-item:hover {
            background: #f8f9fa;
            border-color: #667eea;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .vehicle-item.active {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-color: #f5576c;
        }

        .vehicle-item .vehicle-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .vehicle-item .vehicle-status {
            font-size: 12px;
            opacity: 0.9;
        }

        .vehicle-item .vehicle-location {
            font-size: 12px;
            color: #6c757d;
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .vehicle-item.active .vehicle-location {
            color: rgba(255,255,255,0.9);
        }

        /* ===== DETAIL KENDARAAN ===== */
        .detail-section {
            padding: 15px;
        }

        .detail-section h6 {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .detail-item {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #eee;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .detail-value {
            color: #333;
            font-size: 14px;
        }

        /* ===== TOGGLE BUTTONS ===== */
        .toggle-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            width: 25px;
            height: 70px;
            cursor: pointer;
            border-radius: 0 8px 8px 0;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .toggle-btn:hover {
            background: #667eea;
            color: white;
            width: 28px;
        }

        .toggle-btn.left {
            left: 310px;
            border-radius: 15px;
        }

        .toggle-btn.right {
            right: 310px;
            border-radius: 15px;
        }

        /* Ketika sidebar tersembunyi */
        #sidebar.hidden ~ .toggle-btn.left {
            left: 0;
            border-radius: 0 8px 8px 0;
        }

        #detailPanel.hidden ~ .toggle-btn.right {
            right: 0;
            border-radius: 8px 0 0 8px;
        }

        /* ===== SCROLLBAR CUSTOM ===== */
        .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Responsif untuk mobile */
        @media (max-width: 768px) {
            .wrapper {
                gap: 10px;
                padding: 10px;
            }
            
            #sidebar, #detailPanel {
                width: 280px;
                min-width: 280px;
            }
            
            .toggle-btn.left {
                left: 280px;
            }
            
            .toggle-btn.right {
                right: 280px;
            }
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Marker Status Indicator */
        .marker-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-moving { background-color: #28a745; }
        .status-stopped { background-color: #ffc107; }
        .status-offline { background-color: #dc3545; }
        .status-unknown { background-color: #6c757d; }
    </style>
@endpush

@extends('layouts.admin')
@section('title', 'Monitoring Kendaraan')
@section('content')

<div class="container-fluid p-0">
    <div class="wrapper">
        <!-- Sidebar Kiri - Daftar Kendaraan -->
        <div id="sidebar" class="panel-card">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Daftar Kendaraan</span>
                    <span class="badge bg-light text-dark" id="vehicleCount">0</span>
                </div>
                <div class="card-body">
                    <input type="text" 
                           id="searchInput" 
                           class="form-control" 
                           placeholder="🔍 Cari kendaraan..." 
                           onkeyup="filterList()">
                    <div id="list" class="mt-3">
                        <div class="loading">
                            <div class="loading-spinner"></div>
                            <p>Memuat data kendaraan...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol toggle sidebar kiri -->
        <button class="toggle-btn left" id="toggleSidebarLeft" onclick="toggleSidebarLeft()">
            <span id="leftArrow">◀</span>
        </button>

        <!-- Peta -->
        <div id="map-container" class="panel-card">
            <div class="card">
                {{-- <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Peta Monitoring</span>
                    <div>
                        <small class="me-3">
                            Total: <span id="mapCount" class="badge bg-info">0</span>
                        </small>
                        <small>
                            Aktif: <span id="activeCount" class="badge bg-success">0</span>
                        </small>
                    </div>
                </div> --}}
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <!-- Panel Detail Kanan -->
        <div id="detailPanel" class="panel-card">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detail Kendaraan</span>
                    <button class="btn btn-sm btn-light" onclick="clearDetail()">
                        <small>Clear</small>
                    </button>
                </div>
                <div class="card-body">
                    <div id="detailContent">
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-car fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">Pilih kendaraan</h6>
                            <p class="text-muted small">Klik pada daftar kendaraan atau marker di peta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol toggle sidebar kanan -->
        <button class="toggle-btn right" id="toggleSidebarRight" onclick="toggleSidebarRight()">
            <span id="rightArrow">▶</span>
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_map.api_key') }}"></script>
<script src="https://unpkg.com/leaflet.gridlayer.googlemutant/dist/Leaflet.GoogleMutant.js"></script>

<script src="https://rawcdn.githack.com/bbecquet/Leaflet.RotatedMarker/master/leaflet.rotatedMarker.js"></script>
<script>
const URL_API = "https://borneotelemetry.speedtrack.id/get-traccar-data";
const REFRESH_INTERVAL = 5000;

// const GOOGLE_KEY = "{{ config('services.google_map.api_key') }}";

let markers = {};
let deviceList = [];
let selectedImei = null;

let map;

document.addEventListener("DOMContentLoaded", function () {

    /* INIT MAP GLOBAL */
    map = L.map('map').setView([-6.4, 106.63], 12);

    let osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
        .addTo(map);

    /* GOOGLE LAYER */
    setTimeout(() => {
        let g_roadmap  = L.gridLayer.googleMutant({ type: 'roadmap' });
        let g_satellite = L.gridLayer.googleMutant({ type: 'satellite' });
        let g_hybrid = L.gridLayer.googleMutant({ type: 'hybrid' });
        let g_terrain = L.gridLayer.googleMutant({ type: 'terrain' });

        L.control.layers({
            "OSM": osm,
            "Google Roadmap": g_roadmap,
            "Google Satellite": g_satellite,
            "Google Hybrid": g_hybrid,
            "Google Terrain": g_terrain
        }).addTo(map);
    }, 150);

    /* LOAD DATA */
    loadData();
    setInterval(loadData, REFRESH_INTERVAL);

    /* RESIZE MAP */
    window.addEventListener('resize', () => map.invalidateSize());
});


// Cache untuk marker icons untuk performa
let iconCache = {};

/* ============================
   LOAD DATA
=============================== */
function loadData() {
    fetch(URL_API)
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                deviceList = data.result;
                updateCounters(deviceList);
                renderList(deviceList);
                updateMarkers(deviceList);
            }
        })
        .catch(err => console.error("API Error:", err));
}

function updateCounters(devices) {
    document.getElementById('vehicleCount').textContent = devices.length;
    // document.getElementById('mapCount').textContent = devices.length;
    
    const activeCount = devices.filter(d => d.st === 'moving' || d.st === 'running').length;
    // document.getElementById('activeCount').textContent = activeCount;
}

/* ============================
   LIST KENDARAAN + SEARCH
=============================== */
function renderList(devices) {
    const listContainer = document.getElementById("list");
    if (!listContainer) return;
    
    if (devices.length === 0) {
        listContainer.innerHTML = '<p class="text-muted p-3">Tidak ada data kendaraan</p>';
        return;
    }
    
    let html = "";
    devices.forEach(d => {
        const isActive = selectedImei === d.imei;
        html += `
            <div class="vehicle-item ${isActive ? 'active' : ''}" onclick="selectDevice('${d.imei}')">
                <div class="vehicle-name">${d.name || d.plate_number || d.imei}</div>
                <div class="vehicle-status">
                    <span class="badge ${getStatusBadge(d.st)}">${d.ststr}</span>
                    ${d.speed ? `<span class="ms-2">${d.speed} km/h</span>` : ''}
                </div>
                <div class="vehicle-location">${d.address || 'Lokasi tidak diketahui'}</div>
            </div>`;
    });
    listContainer.innerHTML = html;
}

function getStatusBadge(status) {
    switch(status) {
        case 'Moving':
            return 'bg-success';
        case 'running':
            return 'bg-primary';
        case 'Stopped':
            return 'bg-warning';
        case 'Offline':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

function filterList() {
    let q = document.getElementById("searchInput").value.toLowerCase();
    let filtered = deviceList.filter(d =>
        (d.name || "").toLowerCase().includes(q) ||
        (d.plate_number || "").toLowerCase().includes(q) ||
        (d.imei || "").includes(q)
    );
    renderList(filtered);
}

/* ============================
   UPDATE MARKER DENGAN URL DARI API
=============================== */
function updateMarkers(devices) {
    devices.forEach(d => {
        let lat = parseFloat(d.lat);
        let lng = parseFloat(d.lng);
        
        if (isNaN(lat) || isNaN(lng)) return;
        
        let latlng = [lat, lng];
        let angle = parseFloat(d.angle || d.course || d.direction || 0);

        // Gunakan marker dari API jika ada, jika tidak gunakan default berdasarkan status
        let markerUrl = d.marker || getDefaultMarkerByStatus(d.st);
        
        // Cache key untuk icon
        let cacheKey = `${markerUrl}_${angle}_${selectedImei === d.imei ? 'selected' : 'normal'}`;
        
        // Buat icon dengan URL dari API
        let icon = createIcon(markerUrl, angle, d.imei);

        if (!markers[d.imei]) {
            markers[d.imei] = L.marker(latlng, {
                icon: icon,
                rotationAngle: angle,
                rotationOrigin: "center center",
                title: d.name || d.imei
            })
            .addTo(map)
            .on("click", () => selectDevice(d.imei));
            
            // Tambahkan popup untuk marker
            markers[d.imei].bindPopup(createMarkerPopup(d));
            
        } else {
            markers[d.imei].setLatLng(latlng);
            markers[d.imei].setRotationAngle(angle);
            markers[d.imei].setIcon(icon);
            
            // Update popup
            markers[d.imei].setPopupContent(createMarkerPopup(d));
        }
    });
}

function createIcon(markerUrl, angle, imei) {
    // Jika kendaraan dipilih, buat icon lebih besar
    const isSelected = selectedImei === imei;
    const sizeMultiplier = isSelected ? 1.2 : 1.0;
    
    return L.icon({
        iconUrl: markerUrl,
        iconSize: [30 * sizeMultiplier, 40 * sizeMultiplier],
        iconAnchor: [15 * sizeMultiplier, 40 * sizeMultiplier],
        popupAnchor: [0, -40 * sizeMultiplier]
    });
}

function getDefaultMarkerByStatus(status) {
    // Fallback marker berdasarkan status jika tidak ada di API
    const baseUrl = 'https://speedotrack.pro//img/markers/';
    
    switch(status) {
        case 'moving':
        case 'running':
            return baseUrl + 'arrow-green.svg';
        case 'stopped':
            return baseUrl + 'arrow-yellow.svg';
        case 'offline':
            return baseUrl + 'arrow-grey.svg';
        default:
            return baseUrl + 'arrow-red.svg'; // Default dari contoh Anda
    }
}

function createMarkerPopup(d) {
    return `
        <div style="min-width: 200px;">
            <strong>${d.plate_number || 'Tanpa Nama'}</strong><br>
            <small>${d.imei || 'No. Imei: -'}</small><br>
            <hr style="margin: 5px 0;">
            <table style="font-size: 12px;">
                <tr>
                    <td>Status:</td>
                    <td><b>${d.ststr}</b></td>
                </tr>
                <tr>
                    <td>Kecepatan:</td>
                    <td><b>${d.speed || 0} km/h</b></td>
                </tr>
                <tr>
                    <td>Terakhir:</td>
                    <td>${formatTime(d.dt_tracker)}</td>
                </tr>
            </table>
            <button onclick="selectDeviceFromMap('${d.imei}')" 
                    style="margin-top: 8px; padding: 4px 12px; font-size: 12px;"
                    class="btn btn-sm btn-primary w-100">
                Lihat Detail
            </button>
        </div>
    `;
}

function selectDeviceFromMap(imei) {
    // Tutup popup
    markers[imei].closePopup();
    // Pilih device
    selectDevice(imei);
}

function formatTime(dt) {
    if (!dt) return '-';
    const date = new Date(dt);
    return date.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

/* ============================
   KLIK LIST → MAP + DETAIL
=============================== */
function selectDevice(imei) {
    selectedImei = imei;
    let d = deviceList.find(v => v.imei == imei);
    if (!d) return;

    // Update UI
    renderList(deviceList);
    
    // Update semua marker untuk reflect selection
    updateMarkers(deviceList);
    
    // Buka panel detail jika tertutup
    const detailPanel = document.getElementById("detailPanel");
    if (detailPanel.classList.contains("hidden")) {
        toggleSidebarRight();
    }

    // Zoom ke marker
    let marker = markers[imei];
    if (marker) {
        map.setView(marker.getLatLng(), 16);
        marker.openPopup();
    }

    showDetail(d);
}

/* ============================
   TAMPILKAN DETAIL PANEL
=============================== */
function showDetail(d) {
    const detailContent = document.getElementById("detailContent");
    if (!detailContent) return;
    
    // Dapatkan URL marker untuk ditampilkan di detail
    const markerUrl = d.marker || getDefaultMarkerByStatus(d.st);
    
    detailContent.innerHTML = `
        <div class="detail-section">
            <div class="text-center mb-3">
                <div style="display: inline-block; transform: rotate(${d.angle || 0}deg);">
                    <img src="${markerUrl}" alt="Marker" style="width: 40px; height: 40px;">
                </div>
                <h6 class="mt-2">${d.name || 'Tidak ada nama'}</h6>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">IMEI</div>
                <div class="detail-value">${d.imei}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Nomor Polisi</div>
                <div class="detail-value">${d.plate_number || '-'}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="badge ${getStatusBadge(d.st)}">${d.ststr}</span>
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Kecepatan</div>
                <div class="detail-value">${d.speed || 0} km/h</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Arah</div>
                <div class="detail-value">
                    <div style="display: flex; align-items: center;">
                        <div style="transform: rotate(${d.angle || 0}deg); margin-right: 8px;">
                            ➤
                        </div>
                        <span>${d.angle || 0}°</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Update Terakhir</div>
                <div class="detail-value">${formatDateTime(d.dt_tracker)}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Alamat</div>
                <div class="detail-value">${d.address || '-'}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Koordinat</div>
                <div class="detail-value">${d.lat}, ${d.lng}</div>
            </div>
        </div>
    `;
}

function formatDateTime(dt) {
    if (!dt) return '-';
    return new Date(dt).toLocaleString('id-ID', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function clearDetail() {
    selectedImei = null;
    renderList(deviceList);
    updateMarkers(deviceList); // Reset marker ke normal
    
    // Tutup semua popup
    Object.values(markers).forEach(marker => {
        if (marker.closePopup) marker.closePopup();
    });
    
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-car fa-3x text-muted"></i>
            </div>
            <h6 class="text-muted">Pilih kendaraan</h6>
            <p class="text-muted small">Klik pada daftar kendaraan atau marker di peta</p>
        </div>
    `;
}

/* ============================
   COLLAPSIBLE SIDEBAR
=============================== */
function toggleSidebarLeft() {
    const sidebar = document.getElementById("sidebar");
    const arrow = document.getElementById("leftArrow");
    
    sidebar.classList.toggle("hidden");
    
    if (sidebar.classList.contains("hidden")) {
        arrow.innerHTML = "▶";
    } else {
        arrow.innerHTML = "◀";
    }
    
    setTimeout(() => map.invalidateSize(), 300);
}

function toggleSidebarRight() {
    const detailPanel = document.getElementById("detailPanel");
    const arrow = document.getElementById("rightArrow");
    
    detailPanel.classList.toggle("hidden");
    
    if (detailPanel.classList.contains("hidden")) {
        arrow.innerHTML = "◀";
    } else {
        arrow.innerHTML = "▶";
    }
    
    setTimeout(() => map.invalidateSize(), 300);
}
</script>
@endpush