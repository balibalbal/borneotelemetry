@push('style')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Leaflet.Draw CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
@endpush

@extends('layouts.admin')
@section('title', 'Tambah Geofence')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Buat Geofence Untuk Jalan</strong> <a href="{{ route('geofence.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
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
                        <span id="name" class="input-group-text"
                          ><i class="mdi mdi-latitude"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="name"
                            placeholder="Ketik nama geofence disini"
                            aria-label="Ketik nama geofence disini"
                            aria-describedby="name" 
                            name="name"
                            value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"/>
                          <label for="name">Nama Geofence</label>
                          @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>
                                   
                      <div class="input-group input-group-merge mb-4">
                        {{-- <span class="input-group-text"
                          ><i class="mdi mdi-latitude"></i
                        ></span> --}}
                        <div class="form-floating form-floating-outline">
                          <textarea
                            id="polygon"
                            style="height: 200px"
                            name="polygon" class="form-control @error('polygon') is-invalid @enderror" required readonly></textarea>
                          <label for="polygon">Geofence</label>
                          @error('polygon')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>
                      <input type="hidden" name="type" value="0">
                      <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                          <p><b>Catatan:</b></p><hr>
                          <p class="card-text">Ini hanya untuk geofence jalan. Jika ingin membuat geofence depo atau customer, silahkan gunakan di menu master!</p>
                        </div>
                      </div>
                    </div>
                </div>   
            </div>
            <div class="card-footer d-flex justify-content-end">
                <div class="form-group">
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Simpan Geofence</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

<script>
  var map = L.map('map').setView([-6.2088, 106.8456], 9); // Atur koordinat dan level zoom sesuai kebutuhan

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  var drawnItems = L.featureGroup().addTo(map);
  var drawControl = new L.Control.Draw({
      draw: {
          polygon: true,
          polyline: false,
          rectangle: false,
          circle: false,
          marker: false
      },
      edit: {
          featureGroup: drawnItems,
          remove: true
      }
  });
  map.addControl(drawControl);

  map.on(L.Draw.Event.CREATED, function (e) {
    var type = e.layerType,
        layer = e.layer;

    if (type === 'polygon') {
        var polygonCoordinates = layer.getLatLngs()[0]; // Ambil koordinat dari polygon

        // Ubah koordinat menjadi format WKT
        var wktCoordinates = [];
        for (var i = 0; i < polygonCoordinates.length; i++) {
            var lat = polygonCoordinates[i].lat;
            var lng = polygonCoordinates[i].lng;
            wktCoordinates.push(lng + ' ' + lat);
        }

        // Gabungkan koordinat menjadi satu string
        var wktString = wktCoordinates.join(', ');

        console.log(wktString); // Tampilkan string WKT di console
        document.getElementById('polygon').value = wktString; // Set nilai textarea dengan string WKT
    }

    drawnItems.addLayer(layer);
});

// Tambahkan penanganan peristiwa untuk peristiwa "draw:deleted"
map.on(L.Draw.Event.DELETED, function (e) {
    document.getElementById('polygon').value = ''; // Bersihkan nilai textarea saat fitur dihapus
});

</script>
@endpush