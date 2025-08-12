@extends('layouts.admin')
@section('title', 'Dump Truck')
@section('content')
<div class="container-fluid">
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <a href="/traccars" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali ke Live Tracking</a>
            <a href="/" class="btn rounded-pill btn-primary waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali ke Dashboard</a>
            {{-- <a href="/" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-microsoft-excel me-sm-1"></i> Unduh Ke Excel</a> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Nomor Polisi</th>
                            <th>Dump</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Map</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($items as $item )
                            <tr>
                                <td>{{ $item->time }}</td>
                                <td>{{ $item->no_pol }}</td>  
                                <td>
                                    @if($item->door == 0)
                                        <span class="badge rounded bg-primary">On</span>
                                    @elseif($item->door == 1)
                                        <span class="badge rounded bg-danger">Off</span>                                               
                                    @endif
                                </td>
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
                                <td>
                                    @if($item->latitude && $item->longitude)
                                        <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank">
                                            <i class="mdi mdi-google-maps me-sm-1"></i>
                                        </a>
                                    @else
                                        -
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




