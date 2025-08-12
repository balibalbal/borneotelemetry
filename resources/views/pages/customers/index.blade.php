@push('style')
    <!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

@endpush

@extends('layouts.admin')
@section('title', 'Customer')
@section('content')
<div class="container-fluid">    
    {{-- <div class="card shadow mb-3"> 
        <div id="map" style="height: 400px;"></div>               
    </div> --}}
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('customers.export') }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-file-document-outline"></i> Export Ke Excel</a> <a href="{{ route('customers.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Tambah Customer</a>
        </div>        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Cutomer</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->address }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>
                                    @if(is_null($item->status))
                                        <span class="badge bg-warning">Tidak Ada Status</span>
                                    @elseif($item->status == 0)
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @elseif($item->status == 1)
                                        <span class="badge bg-primary">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Status Tidak Dikenal</span>
                                    @endif
                                </td>
                                <td style="width: 15%">
                                    <a href="{{ route('customers.show', $item->id) }}" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Data">
                                        <span class="tf-icons mdi mdi-eye-outline"></span>
                                    </a>
                                    <a href="{{ route('customers.edit', $item->id) }}" class="btn btn-icon btn-label-secondary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Edit Data">
                                        <span class="tf-icons mdi mdi-pencil"></span>
                                    </a>
                                    
                                    <form action="" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="button" 
                                        class="btn btn-icon btn-label-danger waves-effect" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        data-url="{{ route('customers.destroy', $item->id) }}"
                                        data-bs-placement="top"
                                        title="Hapus Data">
                                        <span class="tf-icons mdi mdi-delete"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus customer ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    
    <!-- Leaflet JS -->
    {{-- <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
    <script>
        $(document).ready(function () {
        $('#dataTable').DataTable();
        });
    </script>

    {{-- <script>
        var map = L.map('map').setView([-6.2088, 106.8456], 9); // Atur koordinat dan level zoom sesuai kebutuhan

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var geofences = @json($items);

        geofences.forEach(function(geofence) {
            // Konversi WKT menjadi koordinat yang bisa digunakan oleh Leaflet
            var coordinates = geofence.polygon.replace('(', '').replace(')', '');
            var latlngs = coordinates.split(', ').map(function(point) {
                var [lng, lat] = point.split(' ');
                return [parseFloat(lat), parseFloat(lng)];
            });

            // Tambahkan poligon ke peta
            var polygon = L.polygon(latlngs, { color: 'red' }).addTo(map);

            // Tambahkan event click pada polygon
            polygon.on('click', function(e) {
                // Tampilkan informasi
                var info = "<b>Nama:</b> " + geofence.name + "<br>" +
                        "<b>Alamat:</b> " + geofence.address + "<br>" +
                        "<b>Telepon:</b> " + geofence.phone;
                L.popup()
                    .setLatLng(e.latlng)
                    .setContent(info)
                    .openOn(map);
            });
        });
    </script> --}}
 @endpush