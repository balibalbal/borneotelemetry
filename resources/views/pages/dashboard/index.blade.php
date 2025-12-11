@extends('layouts.admin')

@section('content')
<!-- Content -->

            <div class="container-fluid">
              <!-- Card Border Shadow -->
              <div class="row">
                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-primary"
                            ><i class="mdi mdi-bus-school mdi-20px"></i
                          ></span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $totalVehicles }}</h4>
                      </div>
                      <p class="mb-0 text-heading">Total Kendaraan</p>
                      {{-- <p class="mb-0">
                        <span class="me-1">+18.2%</span>
                        <small class="text-muted">than last week</small>
                      </p> --}}
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-2 mb-4">
                  <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-success"
                            ><i class="mdi mdi-truck-check mdi-20px"></i
                          ></span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $onlineCount }}</h4>
                      </div>
                      <p class="mb-0 text-heading">Bergerak</p>
                      {{-- <p class="mb-0">
                        <span class="me-1">+18.2%</span>
                        <small class="text-muted">than last week</small>
                      </p> --}}
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-2 mb-4">
                  <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-warning">
                            <i class="mdi mdi-truck mdi-20px"></i
                          ></span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $berhentiCount }}</h4>
                      </div>
                      <p class="mb-0 text-heading">Berhenti</p>
                      {{-- <p class="mb-0">
                        <span class="me-1">-8.7%</span>
                        <small class="text-muted">than last week</small>
                      </p> --}}
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-2 mb-4">
                  <div class="card card-border-shadow-dark h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-dark">
                            <i class="mdi mdi-truck-alert mdi-20px"></i>
                          </span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $diamCount }}</h4>
                      </div>
                      <p class="mb-0 text-heading">Diam</p>
                      {{-- <p class="mb-0">
                        <span class="me-1">+4.3%</span>
                        <small class="text-muted">than last week</small>
                      </p> --}}
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-2 mb-4">
                  <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                          <span class="avatar-initial rounded bg-label-danger"
                            ><i class="mdi mdi-truck-remove mdi-20px"></i
                          ></span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $offlineCount }}</h4>
                      </div>
                      <p class="mb-0 text-heading">Mati</p>
                      {{-- <p class="mb-0">
                        <span class="me-1">-2.5%</span>
                        <small class="text-muted">than last week</small>
                      </p> --}}
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Card Border Shadow -->
              <div class="row">
                <div class="col-12 order-5">
                  <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <div class="card-title mb-0">
                        <h5><i class="mdi mdi-excavator mdi-36px" style="color: red;"></i> Excavator Kemiringan > 3° ({{ $offlineCount }} unit)</h5><a href="{{ route('reports.index') }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-file-document-outline"></i> Selengkapnya</a>
                      </div>
                      
                    </div>
                    <div class="card-datatable table-responsive">
                      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tanggal Update</th>
                                <th>Update Terakhir</th>
                                <th>Nomor Polisi</th>
                                {{-- <th>Latitude</th>
                                <th>Longitude</th> --}}
                                {{-- <th>Kecepatan (Km/h)</th> --}}
                                <th>Total Jarak Tempuh</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Google Maps</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($items as $item)
                            @php
                                $now = \Carbon\Carbon::now();
                                $time = \Carbon\Carbon::parse($item->time);
                                $diffInHours = $time->diffInHours($now);
                                $statusClass = 'status-default'; // Kelas default
                    
                                if ($diffInHours > 24) {
                                    $statusClass = 'status-red'; // Lebih dari 24 jam
                                } elseif ($diffInHours > 12) {
                                    $statusClass = 'status-yellow'; // Lebih dari 12 jam
                                }
                            @endphp
                            <tr>
                                <td>{{ $item->time }}</td>
                                <td class="{{ $statusClass }}">{{ $item->time_diff }}</td>
                                <td>{{ $item->no_pol }}</td>
                                {{-- <td>{{ $item->latitude }}</td>
                                <td>{{ $item->longitude }}</td> --}}
                                {{-- <td>{{ $item->speed }} </td> --}}
                                <td> {{ number_format((float)$item->total_distance, 2, ',', '.') }} KM</td>
                                <td>{{ $item->address }}</td>
                                <td>
                                    @if($item->status == 'bergerak')
                                        <span class="badge bg-success">Bergerak</span>
                                    @elseif($item->status == 'mati')
                                        <span class="badge bg-danger">Mati</span>
                                    @elseif($item->status == 'diam')
                                        <span class="badge bg-dark">Diam</span>
                                    @elseif($item->status == 'berhenti')
                                        <span class="badge bg-warning">Berhenti</span>
                                    @endif
                                </td>                            
                                <td>
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn btn-icon btn-label-success waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Di Google Map">
                                        <span class="tf-icons mdi mdi-google-maps"></span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ On route vehicles Table -->
            </div>
            <!--/ Content -->
@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
    $('#dataTable').DataTable();
    });
  </script>
@endpush