@push('style')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Leaflet.Draw CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
@endpush


@extends('layouts.admin')
@section('title', 'Edit Geofence')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Geofence</strong> <a href="{{ route('geofence.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('geofence.update', $item->id) }}" method="POST">
            @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-8">
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
                            value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror"/>
                          <label for="name">Nama Geofence</label>
                          @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                                                                          
                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <textarea
                            id="polygon"
                            style="height: 200px"
                            name="polygon" class="form-control @error('polygon') is-invalid @enderror" required readonly>{{ old('polygon') ? old('polygon') : $item->polygon }}</textarea>
                          <label for="polygon">Geofence</label>
                          @error('polygon')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>
                      <input type="hidden" name="type" value="0">
                          
                        
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <div class="form-group">
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Update Data Geofence</button>
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
  var map = L.map('map').setView([-6.2088, 106.8456], 13); // Atur koordinat dan level zoom sesuai kebutuhan

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  var drawnItems = L.featureGroup().addTo(map);
  var drawControl = new L.Control.Draw({
      draw: {
          polygon: false,
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

  // Menampilkan geofence yang sudah ada pada peta
  var polygonInput = document.getElementById('polygon');
  var polygonCoordinates = [];

  // Memisahkan koordinat dari string
  var coordinatesArray = polygonInput.value.split(', ');
  coordinatesArray.forEach(function(coordinate) {
      var pair = coordinate.split(' ');
      var lat = parseFloat(pair[1]);
      var lng = parseFloat(pair[0]);
      polygonCoordinates.push([lat, lng]);
  });

  // Periksa apakah polygonCoordinates memiliki data yang valid
  if (polygonCoordinates.length > 0) {
      var polygon = L.polygon(polygonCoordinates, {
          editable: true // Aktifkan fitur edit pada polygon
      }).addTo(drawnItems);

      map.fitBounds(polygon.getBounds());

      // Menangani perubahan pada polygon
      polygon.on('edit', function (e) {
          var polygonCoordinates = e.target.getLatLngs()[0]; // Ambil koordinat dari polygon yang sudah diedit

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
          polygonInput.value = wktString; // Set nilai textarea dengan string WKT
      });
  } else {
      console.error('Polygon coordinates are invalid: ' + polygonInput.value);
  }
</script>
@endpush