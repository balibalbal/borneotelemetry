<!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top align-items-center">
        <div class="container-fluid">
            <!-- Logo dan Brand -->
            <a class="navbar-brand d-flex align-items-center gap-1 pt-0 pb-0" href="/">
                <img src="{{ url('backend/img/borneo-logo.png') }}" alt="borneotelemetry Logo">
                <span class="navbar-brand-text">Borneotelemetry</span>
            </a>

            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="mdi mdi-menu"></span>
            </button>
            
            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="devicesDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mdi mdi-database-cog"></span>
                            Master Data
                        </a>
                        <ul class="dropdown-menu">
                          @if (auth()->user()->hasPermissionTo('master-kendaraan')) 
                            <li><a class="dropdown-item" href="{{ route('vehicles.index') }}"><span class="mdi mdi-dump-truck"></span> Kendaraan</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('master-device'))
                            <li><a class="dropdown-item" href="{{ route('devices.index') }}"><span class="mdi mdi-cellphone-marker"></span> Device</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('master-supir')) 
                            <li><a class="dropdown-item" href="{{ route('drivers.index') }}"><span class="mdi mdi-account-multiple"></span> Driver</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('master-group')) 
                            <li><a class="dropdown-item" href="{{ route('groups.index') }}"><span class="mdi mdi-group"></span> Group</a></li>
                          @endif
                            <li><hr class="dropdown-divider"></li>
                            @if (auth()->user()->hasPermissionTo('master-geofence'))
                              <li><a class="dropdown-item" href="{{ route('geofence.index') }}"><span class="mdi mdi-map-marker-circle"></span> Geofence</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="monitoringDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mdi mdi-map-search"></span>
                            Monitoring
                        </a>
                        <ul class="dropdown-menu">
                          @if (auth()->user()->hasPermissionTo('monitoring-live-tracking'))
                            <li><a class="dropdown-item" href="{{ route('traccars.index') }}"><span class="mdi mdi-map-marker-radius"></span> Monitoring Kendaraan</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('tracking-perjalanan'))
                            <li><a class="dropdown-item" href="{{ route('histories.index') }}"><span class="mdi mdi-map-marker-path"></span> Historical</a></li>
                          @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mdi mdi-chart-bar"></span>
                            Laporan
                        </a>
                        <ul class="dropdown-menu">
                          @if (auth()->user()->hasPermissionTo('laporan-last-position'))
                            <li><a class="dropdown-item" href="{{ route('reports.index') }}"><span class="mdi mdi-map-marker"></span> Posisi Akhir</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('laporan-historical'))
                            <li><a class="dropdown-item" href="{{ route('report.historicalReport') }}"><span class="mdi mdi-map-clock"></span> Historical</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('laporan-parkir'))
                            <li><a class="dropdown-item" href="{{ route('reports.parkir') }}"><span class="mdi mdi-bus-stop"></span> Parkir</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('laporan-kecepatan'))
                            <li><a class="dropdown-item" href="{{ route('reports.kecepatan') }}"><span class="mdi mdi-speedometer"></span> Kecepatan</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('laporan-jarak'))
                            <li><a class="dropdown-item" href="{{ route('reports.jarak') }}"><span class="mdi mdi-signal-distance-variant"></span> Jarak Tempuh</a></li>
                          @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="grafikDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mdi mdi-chart-bar"></span>
                            Grafik
                        </a>
                        <ul class="dropdown-menu">
                          @if (auth()->user()->hasPermissionTo('grafik-heatmap'))
                            <li><a class="dropdown-item" href="/grafik/heatmap"><span class="mdi mdi-chart-scatter-plot-hexbin"></span> Grafik Heatmap</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('speed-distribution'))
                            <li><a class="dropdown-item" href="/grafik/distribusi"><span class="mdi mdi-chart-timeline-variant-shimmer"></span> Speed Distribution</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('grafik-kecepatan'))
                            <li><a class="dropdown-item" href="/grafik/speed"><span class="mdi mdi-finance"></span> Speed</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('grafik-jarak'))
                            <li><a class="dropdown-item" href="/grafik/distance"><span class="mdi mdi-chart-timeline"></span> Jarak Tempuh</a></li>
                          @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mdi mdi-account-group"></span>
                            User
                        </a>
                        <ul class="dropdown-menu">
                          @if (auth()->user()->hasPermissionTo('tambah-user'))
                            <li><a class="dropdown-item" href="{{ route('users.create') }}"><span class="mdi mdi-account-edit"></span> Registrasi User</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('user'))
                            <li><a class="dropdown-item" href="{{ route('users.index') }}"><span class="mdi mdi-account-box-multiple"></span> List User</a></li>
                          @endif
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="mdi mdi-account-group"></span>
                            Role & Akses
                        </a>
                        <ul class="dropdown-menu">
                          @if (auth()->user()->hasPermissionTo('role'))
                            <li><a class="dropdown-item" href="{{ route('roles.index') }}"><span class="mdi mdi-account-hard-hat"></span> Roles</a></li>
                          @endif
                          @if (auth()->user()->hasPermissionTo('hak-akses'))
                            <li><a class="dropdown-item" href="{{ route('permissions.index') }}"><span class="mdi mdi-account-lock"></span> Hak Akses</a></li>
                          @endif
                        </ul>
                    </li>
                </ul>
                
                <!-- User Dropdown -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="{{ url('backend/assets/img/avatars/1.png') }}" class="user-avatar" alt="User Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="fw-semibold">{{ auth()->user()->name }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('users.show', auth()->user()->id) }}"><span class="mdi mdi-account me-2"></span> Profile</a></li>
                            {{-- <li><a class="dropdown-item" href="{{ route('settings.index') }}"><span class="mdi mdi-cog me-2"></span> Settings</a></li> --}}
                            <li><a class="dropdown-item" href="{{ route('settings.index') }}"><span class="mdi mdi-bell me-2"></span> Notifications</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                              <form action="/logout" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="mdi mdi-logout me-2"></i> Log Out</button>
                              </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>