@push('style')
<style>
  .toast-custom {
      animation: bounceInUp 1s;
  }
  @keyframes bounceInUp {
      0% {
          transform: translateY(1000px);
          opacity: 0;
      }
      60% {
          transform: translateY(-30px);
          opacity: 1;
      }
      80% {
          transform: translateY(10px);
          opacity: 1;
      }
      100% {
          transform: translateY(0);
          opacity: 1;
      }
  }
        .status-red {
            background-color: #fd051a !important;
            color: #faf7f7 !important;
        }

        .status-yellow {
            background-color: #ffc70d !important;
            color: #fffefc !important;
        }

        .status-default {
            background-color: #ffffff;
        }
  </style>
@endpush

@extends('layouts.admin')

@section('content')
    <!-- Begin Page Content -->
    <div class="row gy-4">
      <!-- Hour chart  -->
      <div class="card bg-transparent shadow-none border-0 my-4">
        <div class="card-body row p-0 pb-3">
          <div class="col-12 col-md-9 card-separator">
            <h3 class="display-6">Selamat datang kembali, <span class="fw-semibold">{{ auth()->user()->name }}</span> 👋🏻</h3>
            <div class="col-12 col-lg-7">
              <p>Hari ini adalah hari {{ now()->locale('id')->isoFormat('dddd') }}, tanggal {{ now()->formatLocalized('%d %B %Y') }}, Jam {{ now()->format('H:i') }} WIB. Tetap semangat ya!</p>
            </div>
          
            <div class="d-flex justify-content-between flex-wrap gap-3 me-5">
              <div class="d-flex align-items-center gap-3 me-4 me-sm-0">
                <div class="avatar avatar-md">
                  <div class="avatar-initial bg-label-danger rounded">
                    <i class="mdi mdi-car-off mdi-36px"></i>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Mati</p>
                  <span class="text-danger mb-0 display-6">{{ $offlineCount }}</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-4">
                <div class="avatar avatar-md">
                  <div class="avatar-initial bg-label-warning rounded">
                    <i class="mdi mdi-car-multiple mdi-36px"></i>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Berhenti</p>
                  <span class="text-warning mb-0 display-6">{{ $diamCount }}</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-4">
                <div class="avatar avatar-md">
                  <div class="avatar-initial bg-label-dark rounded">
                    <i class="mdi mdi-car-key mdi-36px"></i>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Diam</p>
                  <span class="text-dark mb-0 display-6">{{ $berhentiCount }}</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-4">
                <div class="avatar avatar-md">
                  <div class="avatar-initial bg-label-success rounded">
                    <i class="mdi mdi-car-arrow-right mdi-36px"></i>
                  </div>
                </div>
                <div class="content-right">
                  <p class="mb-0 fw-medium">Bergerak</p>
                  <span class="text-success mb-0 display-6">{{ $onlineCount }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 ps-md-3 ps-lg-5 pt-3 pt-md-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div>
                  <h5 class="mb-2">Total Kendaraan</h5>
                  @php
                      $currentDate = \Carbon\Carbon::now();
                  @endphp
                  <p class="mb-4">Bulan {{ $currentDate->isoFormat('MMMM YYYY') }}</p>
                </div>
                <div class="time-spending-chart">
                  <h5 class="mb-2 d-flex justify-content-center align-items-center"><i class="mdi mdi-truck mdi-36px"></i>&nbsp;&nbsp;&nbsp;<i class="mdi mdi-motorbike mdi-36px"></i></h5>
                  {{-- <h6 class="mb-2"><i class="mdi mdi-motorbike mdi-24px"></i> Sepeda Motor : {{ $totalVehicles }}</h6> --}}
                  <h6 class="mb-2">{{ $totalVehicles }} Kendaraan <span class="badge bg-label-success rounded-pill">Aktif</span></h6>
                </div>
              </div>
              <div id="leadsReportChart"></div>
            </div>
          </div>
        </div>
      </div>

      @if(auth()->user()->customer_id == 1)
        <div class="col-lg-6 col-sm-6">
          <div class="card h-100">
            <div class="row">
              <div class="col-6">
                <div class="card-body">
                  <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                    <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap"><i class="mdi mdi-truck mdi-36px"></i> Mobil</h5>
                    <div class="badge bg-label-primary rounded-pill lh-xs">{{ $totalMobilAktif }} Aktif</div> 
                    <div class="badge bg-label-danger rounded-pill lh-xs">{{ $totalMobilTidakAktif }} Tidak Aktif</div>
                  </div>
                  <div class="d-flex align-items-end flex-wrap gap-1">
                    <h4 class="mb-0 me-2">{{ $totalMobil }}</h4>
                    <small class="text-success">Total Mobil</small>
                  </div>
                </div>
              </div>
              <div class="col-6 text-end d-flex align-items-end justify-content-center">
                <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                  <img
                    src="{{ url('backend/assets/img/illustrations/card-mobil.png') }}"
                    alt="Total Mobil"
                    width="230" />
                </div>
              </div>
            </div>
          </div>
        </div>      
        <!--/ Total Mobil -->

        <!-- Total Sepeda Motor -->
        <div class="col-lg-6 col-sm-6">
          <div class="card h-100">
            <div class="row">
              <div class="col-6">
                <div class="card-body">
                  <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                    <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap"><i class="mdi mdi-motorbike mdi-36px"></i> Sepeda Motor</h5>
                    <div class="badge bg-label-success rounded-pill lh-xs">{{ $totalMotorAktif }} Aktif</div>
                    <div class="badge bg-label-danger rounded-pill lh-xs">{{ $totalMotorTidakAktif }} Tidak Aktif</div>
                  </div>
                  <div class="d-flex align-items-end flex-wrap gap-1">
                    <h5 class="mb-0 me-2">{{ $totalMotor }}</h5>
                    <small class="text-primary">Total Sepeda Motor</small>
                  </div>
                </div>
              </div>
              <div class="col-6 text-end d-flex align-items-end justify-content-center">
                <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                  <img
                    src="{{ url('backend/assets/img/illustrations/card-motorbike.png') }}"
                    alt="Ratings"
                    width="220" />
                </div> 
              </div>
            </div>
          </div>
        </div>
      @else
        @if ($punyaMobil)
          <div class="col-lg-6 col-sm-6">
            <div class="card h-100">
              <div class="row">
                <div class="col-6">
                  <div class="card-body">
                    <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                      <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap"><i class="mdi mdi-truck mdi-36px"></i> Mobil</h5>
                      <div class="badge bg-label-primary rounded-pill lh-xs">{{ $totalMobilAktif }} Aktif</div> 
                      <div class="badge bg-label-danger rounded-pill lh-xs">{{ $totalMobilTidakAktif }} Tidak Aktif</div>
                    </div>
                    <div class="d-flex align-items-end flex-wrap gap-1">
                      <h4 class="mb-0 me-2">{{ $totalMobil }}</h4>
                      <small class="text-success">Total Mobil</small>
                    </div>
                  </div>
                </div>
                <div class="col-6 text-end d-flex align-items-end justify-content-center">
                  <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                    <img
                      src="{{ url('backend/assets/img/illustrations/card-mobil.png') }}"
                      alt="Total Mobil"
                      width="230" />
                  </div>
                </div>
              </div>
            </div>
          </div>          
        @endif
        <!--/ Total Mobil -->

        <!-- Total Sepeda Motor -->
        @if ($punyaMotor)
          <div class="col-lg-6 col-sm-6">
            <div class="card h-100">
              <div class="row">
                <div class="col-6">
                  <div class="card-body">
                    <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                      <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap"><i class="mdi mdi-motorbike mdi-36px"></i> Sepeda Motor</h5>
                      <div class="badge bg-label-success rounded-pill lh-xs">{{ $totalMotorAktif }} Aktif</div>
                      <div class="badge bg-label-danger rounded-pill lh-xs">{{ $totalMotorTidakAktif }} Tidak Aktif</div>
                    </div>
                    <div class="d-flex align-items-end flex-wrap gap-1">
                      <h5 class="mb-0 me-2">{{ $totalMotor }}</h5>
                      <small class="text-primary">Total Sepeda Motor</small>
                    </div>
                  </div>
                </div>
                <div class="col-6 text-end d-flex align-items-end justify-content-center">
                  <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                    <img
                      src="{{ url('backend/assets/img/illustrations/card-motorbike.png') }}"
                      alt="Ratings"
                      width="220" />
                  </div> 
                </div>
              </div>
            </div>
          </div>
        @endif        
      @endif

      {{-- dump truck --}}
        @if(auth()->user()->customer_id == 1 || auth()->user()->customer_id == 4)
          <div class="col-lg-6 col-sm-6">
            <div class="card h-100">
              <div class="row">
                <div class="col-6">
                  <div class="card-body">
                    <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
                      <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap"><i class="mdi mdi-dump-truck mdi-36px"></i> Dump Muatan</h5>
                      <a href="/dump/0"><div class="badge bg-label-primary rounded-pill lh-xs">{{ $total_dump }} Mobil Sedang Dump</div> </a>
                      {{-- <div class="badge bg-label-danger rounded-pill lh-xs">{{ $totalMobilTidakAktif }} Tidak Aktif</div> --}}
                    </div>
                    <div class="d-flex align-items-end flex-wrap gap-1">
                      <h4 class="mb-0 me-2">{{ $total_dump }}</h4>
                      <small class="text-success">Total Mobil Dump</small>
                    </div>
                  </div>
                </div>
                <div class="col-6 text-end d-flex align-items-end justify-content-center">
                  <div class="card-body pb-0 pt-3 position-absolute bottom-0">
                    <img
                      src="{{ url('backend/assets/img/illustrations/dump-truck.png') }}"
                      alt="Total Mobil"
                      width="230" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
      <!--/ Sessions -->

      <div class="col-12">
        <div class="card mb-0">
          <div class="card-widget-separator-wrapper">
            {{-- <div class="card-header">
              <p><marquee>Hari ini : {{ now()->formatLocalized('%d %B %Y') }}, Jam {{ now()->format('H:i') }} WIB.</marquee></p>
              <hr>
            </div> --}}
            <div class="card-body card-widget-separator">
              <div class="row gy-2 gy-sm-1">
                <div class="col-sm-6 col-lg-3">
                  <div
                    class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                    <div>
                      <p class="mb-2">Kecepatan Tertinggi</p>
                      <h5 class="mb-2">{{ $nopol }}</h5>
                      <p class="mb-0">
                        <span class="badge rounded-pill bg-label-success">{{ $topSpeed }}</span><span class="me-2"> km/h</span>
                      </p>
                    </div>
                    <div class="avatar me-sm-4">
                      <span class="avatar-initial rounded bg-label-warning">
                        <i class="mdi mdi-car-traction-control mdi-24px"></i>
                      </span>
                    </div>
                  </div>
                  <hr class="d-none d-sm-block d-lg-none me-4" />
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div
                    class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                    <div>
                      <p class="mb-2">Jarak Terjauh</p>
                      <h5 class="mb-2">{{ $nopolDistance }}</h5>
                      <p class="mb-0">
                        <span class="badge rounded-pill bg-label-primary">{{ number_format((float)$topDistance, 2, ',', '.') }}</span><span class="me-2"> km</span>
                      </p>
                    </div>
                    <div class="avatar me-lg-4">
                      <span class="avatar-initial rounded bg-label-primary">
                        <i class="mdi mdi-car-speed-limiter mdi-24px"></i>
                      </span>
                    </div>
                  </div>
                  <hr class="d-none d-sm-block d-lg-none" />
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div
                    class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                    <div>
                      <p class="mb-2">Mati Terlama</p>
                      <h5 class="mb-2">{{ $vehicleWithLongestOffline }}</h5>
                      <p class="mb-0">
                        <span class="badge rounded-pill bg-label-secondary">{{ $longestOfflineDuration }}</span>
                      </p>                     
                    </div>
                    <div class="avatar me-sm-4">
                      <span class="avatar-initial rounded bg-label-secondary">
                        <i class="mdi mdi-car-clock mdi-24px"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <p class="mb-2">Alarm</p>
                      <h5 class="mb-2"></h5>
                      <p class="mb-0">
                        <span class="badge rounded-pill bg-label-danger"></span> <span class="me-2"></span>
                      </p>
                    </div>
                    <div class="avatar">
                      <span class="avatar-initial rounded bg-label-danger">
                        <i class="mdi mdi-car-emergency mdi-24px"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5><i class="mdi mdi-car-off mdi-36px" style="color: red;"></i> Daftar Kendaraan Mati ({{ $offlineCount }} unit)</h5><a href="{{ route('reports.index') }}" class="btn rounded-pill btn-success waves-effect waves-light"><i class="mdi mdi-file-document-outline"></i> Selengkapnya</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
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