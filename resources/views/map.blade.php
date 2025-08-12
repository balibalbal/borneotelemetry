<!DOCTYPE html>
<html>
<head>
  <title>Map Kendaraan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    #map { height: 100vh; }
  </style>
</head>
<body>
<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-6.2, 106.8], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Fungsi fetch dan tampilkan GeoJSON
    function loadData(url, styleOptions, getPopup) {
        fetch(url)
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    pointToLayer: (feature, latlng) => L.circleMarker(latlng, styleOptions),
                    onEachFeature: (feature, layer) => {
                        layer.bindPopup(getPopup(feature.properties));
                    }
                }).addTo(map);
            });
    }

    // 🚨 Ngebut (merah)
    // loadData('/api/speeding', 
    //     { radius: 6, fillColor: '#f03', color: '#800', weight: 1, fillOpacity: 0.8 },
    //     props => `<b>Ngebut!</b><br>Vehicle: ${props.vehicle_id}<br>Speed: ${props.speed_kmh} km/h<br>Time: ${props.time}`
    // );

    // 🅿️ Berhenti (biru)
    loadData('/api/stops', 
        { radius: 6, fillColor: '#00f', color: '#005', weight: 1, fillOpacity: 0.8 },
        props => `<b>Berhenti</b><br>Vehicle: ${props.vehicle_id}<br>Durasi: ${props.durasi_detik}s<br>Time: ${props.time}`
    );

    // 🚦 Kemacetan (oranye)
    // loadData('/api/jams', 
    //     { radius: 6, fillColor: '#ffa500', color: '#cc8400', weight: 1, fillOpacity: 0.8 },
    //     props => `<b>Macet</b><br>Vehicle: ${props.vehicle_id}<br>Speed: ${props.speed_kmh} km/h<br>Time: ${props.time}`
    // );
</script>

</body>
</html>
