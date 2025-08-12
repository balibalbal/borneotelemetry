@extends('layouts.admin')
@section('title', 'View Geofence')
@section('content')
    <div class="container-fluid">
        <h1>Data Kendaraan Parkir</h1>

        <!-- Form Filter -->
        <form action="{{ route('parking.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="vehicle_id">Vehicle ID</label>
                    <input type="text" name="vehicle_id" id="vehicle_id" class="form-control" value="{{ old('vehicle_id', $vehicle_id) }}">
                </div>
                <div class="col-md-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $start_date) }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $end_date) }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                </div>
            </div>
        </form>

        <!-- Data Tabel Parkir -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vehicle ID</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Durasi Parkir</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($parkirData as $data)
                    <tr>
                        <td>{{ $data->vehicle_id }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->start_time)->format('d-m-Y H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->end_time)->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $data->formatted_duration }}</td>
                        <td>{{ $data->location }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Analisa Parkir di Tempat yang Sama -->
        <h3>Analisa Parkir di Tempat yang Sama</h3> 
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vehicle ID</th>
                    <th>Lokasi</th>
                    <th>Jumlah Parkir di Tempat yang Sama</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($parkirAnalysis as $analysis)
                    <tr>
                        <td>{{ $analysis['vehicle_id'] }}</td>
                        <td>{{ $analysis['location'] }}</td>
                        <td>{{ $analysis['count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
