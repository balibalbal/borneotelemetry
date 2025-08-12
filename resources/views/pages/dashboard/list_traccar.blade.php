@extends('layouts.admin')
@section('title', 'Traccar')
@section('content')
<div class="container-fluid">
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <a href="/traccars" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali ke Live Tracking</a>
            <a href="/" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali ke Dashboard</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>Nomor Polisi</th>
                            <th>Kecepatan(Km/h)</th>
                            <th>Total Jarak (KM)</th>
                            <th>Alamat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->time }}</td>
                                <td>{{ $item->no_pol }}</td>
                                <td>{{ $item->speed }}</td>   
                                <td>{{ $item->total_distance }}</td>
                                <td>{{ $item->address }}</td>                      
                                <td>
                                    @if($item->status == 'mati')
                                        <span class="badge rounded-pill bg-label-danger">Mati</span>
                                    @elseif($item->status == 'berhenti')
                                        <span class="badge rounded-pill bg-label-warning">Berhenti</span>
                                    @elseif($item->status == 'diam')
                                        <span class="badge rounded-pill bg-label-dark">Diam</span>
                                    @elseif($item->status == 'bergerak')
                                        <span class="badge rounded-pill bg-label-success">Bergerak</span>                                                
                                    @endif
                                </td>                      
                            </tr>
                        @empty
                            
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>   

</div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
        $('#dataTable').DataTable();
        });
    </script>
 @endpush




