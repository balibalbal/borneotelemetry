@push('style')
    <!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

@endpush

@extends('layouts.admin')
@section('title', 'Geofence')
@section('content')
<div class="container-fluid">    
    <div class="card shadow mb-3"> 
        <div id="map" style="height: 400px;"></div>               
    </div>
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-start align-items-center">
            <a href="{{ route('geofence.create') }}" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-plus me-sm-1"></i> Create New Geofence</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Geofence</th>
                            <th>Customer Name</th>
                            <th>Geofence Type</th>
                            <th>Status</th>
                            <th style="width: 15%">Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->name }}</td>                                
                                <td>{{ $item->customer_name }}</td>                                
                                <td>
                                    @if ($item->type == 1)
                                        <span class="badge bg-warning">Radius</span>
                                    @elseif ($item->type == 2)
                                        <span class="badge bg-primary">Polygon</span>
                                    @else
                                        Tidak diketahui
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($item->status == 2)
                                        <span class="badge bg-danger">Not Active</span>
                                    @else
                                        Tidak diketahui
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('geofence.show', $item->id) }}" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Data">
                                        <span class="tf-icons mdi mdi-eye-outline"></span>
                                    </a>

                                    {{-- Hanya tampilkan tombol Edit dan Hapus jika type = 0 --}}
                                    @if ($item->type == 0)
                                        <a href="{{ route('geofence.edit', $item->id) }}" class="btn btn-icon btn-label-secondary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Edit Data">
                                            <span class="tf-icons mdi mdi-pencil"></span>
                                        </a>
                                    @endif
                                        <form action="" method="post" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="btn btn-icon btn-label-danger waves-effect" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmDeleteModal" 
                                            data-url="{{ route('geofence.destroy', $item->id) }}"
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus geofence ini?
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
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    
        var map = L.map('map').setView([-6.2088, 106.8456], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var geofences = @json($items);

        var allBounds = []; // Kumpulan koordinat untuk fitBounds

        geofences.forEach(function (geofence) {
            // Tampilkan circle jika punya center_point dan radius
            if (geofence.center_point && geofence.radius) {
                var center = JSON.parse(geofence.center_point);
                var lat = center.coordinates[1];
                var lng = center.coordinates[0];

                var circle = L.circle([lat, lng], {
                    color: 'blue',
                    radius: geofence.radius
                }).addTo(map);

                circle.bindPopup(`<b>Nama:</b> ${geofence.name}`);

                // Tambahkan bounding box dari circle ke allBounds
                allBounds.push([lat, lng]);
            }

            // Tampilkan polygon jika punya polygon_area
            if (geofence.polygon_area) {
                var polygon = JSON.parse(geofence.polygon_area);
                var coordinates = polygon.coordinates[0].map(coord => [coord[1], coord[0]]); // lat, lng

                var shape = L.polygon(coordinates, { color: 'green' }).addTo(map);
                shape.bindPopup(`<b>Nama:</b> ${geofence.name}`);

                // Tambahkan semua titik polygon ke allBounds
                allBounds.push(...coordinates);
            }
        });

        // Jika ada geofence, sesuaikan tampilan peta agar mencakup semuanya
        if (allBounds.length > 0) {
            map.fitBounds(allBounds);
        }
    </script>

 @endpush