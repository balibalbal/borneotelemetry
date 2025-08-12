<head>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
  <div id="map" style="height: 600px;"></div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>

  <script>
    const map = L.map('map').setView([-6.437, 106.634], 12); // posisi awal

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    const socket = io('http://202.51.198.4:2000', {
      transports: ['websocket']
    });

    const vehicleMarkers = {};

    socket.on('connect', () => {
      console.log('Connected to WebSocket');
    });

    socket.on('gps-location', (data) => {
      let decoded = typeof data === 'string' ? JSON.parse(data) : data;

      const id = decoded.vehicle_id;
      const lat = decoded.lat;
      const lon = decoded.lon;
      const no_pol = decoded.no_pol || 'Tanpa Nopol';
      const speed = decoded.speed || 0;
      const ignition = decoded.ignition;

      // Update atau buat marker baru
      if (vehicleMarkers[id]) {
        vehicleMarkers[id].setLatLng([lat, lon]);
        vehicleMarkers[id].bindPopup(`${no_pol}<br>Speed: ${speed} km/h`);
      } else {
        const marker = L.marker([lat, lon], {
          icon: L.icon({
            iconUrl: '/marker-icon.png', // optional custom icon
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
          })
        }).addTo(map)
          .bindPopup(`${no_pol}<br>Speed: ${speed} km/h`);

        vehicleMarkers[id] = marker;
      }
    });
  </script>
</body>
