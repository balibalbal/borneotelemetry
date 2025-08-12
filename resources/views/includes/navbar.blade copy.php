<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="container-xxl">
            <div class="navbar-brand app-brand d-none d-xl-flex py-0 me-4">
              <a href="/" class="app-brand-link gap-2">
                <span class="app-brand-logo">
                  <img src="{{ url('backend/img/borneo-logo.png') }}" class="img-fluid" style="width: 60px;">
                </span>
                <span class="app-brand-text menu-text">Borneotelemetry</span>
              </a>

              <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="mdi mdi-close align-middle"></i>
              </a>
            </div>

            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="mdi mdi-menu mdi-24px"></i>
              </a>
            </div>

            <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
              <div class="container-xxl d-flex h-100">
                <ul class="menu-inner">
                  <!-- Dashboards -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons mdi mdi-database-cog"></i>
                      <div data-i18n="Master Data">Master Data</div>
                    </a>
                    <ul class="menu-sub">
                      @if (auth()->user()->hasPermissionTo('master-kendaraan')) 
                      <li class="menu-item">
                        <a href="{{ route('vehicles.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-dump-truck"></i>
                          <div data-i18n="Kendaraan">Kendaraan</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('master-device'))
                      <li class="menu-item">
                        <a href="{{ route('devices.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-cellphone-marker"></i>
                          <div data-i18n="Device">Device</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('master-supir')) 
                      <li class="menu-item">
                        <a href="{{ route('drivers.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-account-multiple"></i>
                          <div data-i18n="Driver">Driver</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('master-group'))
                      <li class="menu-item">
                        <a href="{{ route('groups.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-group"></i>
                          <div data-i18n="Group">Group</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('master-geofence')) 
                      <li class="menu-item">
                        <a href="{{ route('geofence.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-map-marker-circle"></i>
                          <div data-i18n="Geofence">Geofence</div>
                        </a>
                      </li>
                      @endif
                    </ul>
                  </li>

                  <!-- Layouts -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons mdi mdi-map-search"></i>
                      <div data-i18n="Monitoring">Monitoring</div>
                    </a>

                    <ul class="menu-sub">
                      @if (auth()->user()->hasPermissionTo('monitoring-live-tracking'))
                      <li class="menu-item">
                        <a href="{{ route('traccars.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-map-marker-radius"></i>
                          <div data-i18n="Monitoring Kendaraan">Monitoring Kendaraan</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('tracking-perjalanan'))
                      <li class="menu-item">
                        <a href="{{ route('histories.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-map-marker-path"></i>
                          <div data-i18n="Historical Kendaraan">Historical</div>
                        </a>
                      </li>
                      @endif
                    </ul>
                  </li>

                  <!-- Report -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons mdi mdi-file-sign"></i>
                      <div data-i18n="Laporan">Laporan</div>
                    </a>
                    <ul class="menu-sub">
                      @if (auth()->user()->hasPermissionTo('laporan-last-position'))
                      <li class="menu-item">
                        <a href="{{ route('reports.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-map-marker"></i>
                          <div data-i18n="Posisi Akhir">Posisi Akhir</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('laporan-historical'))
                      <li class="menu-item">
                        <a href="{{ route('report.historicalReport') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-map-clock"></i>
                          <div data-i18n="Historical">Historical</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('laporan-parkir'))
                      <li class="menu-item">
                        <a href="{{ route('reports.parkir') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-bus-stop"></i>
                          <div data-i18n="Parking">Parking</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('laporan-kecepatan'))
                      <li class="menu-item">
                        <a href="{{ route('reports.kecepatan') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-speedometer"></i>
                          <div data-i18n="Speed">Speed</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('laporan-jarak'))
                      <li class="menu-item">
                        <a href="{{ route('reports.jarak') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-signal-distance-variant"></i>
                          <div data-i18n="Jarak Tempuh">Jarak Tempuh</div>
                        </a>
                      </li>
                      @endif                      
                    </ul>
                  </li>

                  <!-- Grafik -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons mdi mdi-chart-bar-stacked"></i>
                      <div data-i18n="Grafik">Grafik</div>
                    </a>
                    <ul class="menu-sub">
                      @if (auth()->user()->hasPermissionTo('grafik-heatmap'))
                      <li class="menu-item">
                        <a href="/grafik/heatmap" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-chart-scatter-plot-hexbin"></i>
                          <div data-i18n="Grafik Heatmap">Grafik Heatmap</div>
                        </a>
                      </li>
                      @endif 

                      @if (auth()->user()->hasPermissionTo('speed-distribution'))
                      <li class="menu-item">
                        <a href="/grafik/distribusi" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-chart-timeline-variant-shimmer"></i>
                          <div data-i18n="Speed Distribution">Speed Distribution</div>
                        </a>
                      </li>
                      @endif 
                      
                      @if (auth()->user()->hasPermissionTo('grafik-kecepatan'))
                      <li class="menu-item">
                        <a href="/grafik/speed" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-finance"></i>
                          <div data-i18n="Speed">Speed</div>
                        </a>
                      </li>
                      @endif

                      @if (auth()->user()->hasPermissionTo('grafik-jarak'))
                      <li class="menu-item">
                        <a href="/grafik/distance" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-chart-timeline"></i>
                          <div data-i18n="Distance">Distance</div>
                        </a>
                      </li>
                      @endif                      
                    </ul>
                  </li>

                  <!-- User -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons mdi mdi-account-group"></i>
                      <div data-i18n="User">User</div>
                    </a>
                    <ul class="menu-sub">
                      @if (auth()->user()->hasPermissionTo('tambah-user'))
                      <li class="menu-item">
                        <a href="{{ route('users.create') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-account-edit"></i>
                          <div data-i18n="Registrasi User">Registrasi User</div>
                        </a>
                      </li>
                      @endif 

                      @if (auth()->user()->hasPermissionTo('user'))
                      <li class="menu-item">
                        <a href="{{ route('users.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-account-box-multiple"></i>
                          <div data-i18n="List User">List User</div>
                        </a>
                      </li>
                      @endif                                           
                    </ul>
                    
                  </li>

                  <!-- Role -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons mdi mdi-security"></i>
                      <div data-i18n="Role & Akses">Role & Akses</div>
                    </a>
                    <ul class="menu-sub">
                      @if (auth()->user()->hasPermissionTo('role'))
                      <li class="menu-item">
                        <a href="{{ route('roles.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-account-hard-hat"></i>
                          <div data-i18n="Roles">Roles</div>
                        </a>
                      </li>
                      @endif 

                      @if (auth()->user()->hasPermissionTo('hak-akses'))
                      <li class="menu-item">
                        <a href="{{ route('permissions.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons mdi mdi-account-lock"></i>
                          <div data-i18n="Hak Akses">Hak Akses</div>
                        </a>
                      </li>
                      @endif                                           
                    </ul>
                    
                  </li>
                </ul>
              </div>
            </aside>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <ul class="navbar-nav flex-row align-items-center ms-auto">
             
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="{{ url('backend/assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="pages-account-settings-account.html">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="{{ url('backend/assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-medium d-block">{{ auth()->user()->name }}</span>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('users.show', auth()->user()->id) }}">
                        <i class="mdi mdi-account-outline me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item"><i class="mdi mdi-logout me-2"></i> Log Out</button>
                      </form>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </div>
        </nav>