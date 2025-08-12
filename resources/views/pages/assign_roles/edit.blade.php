@extends('layouts.admin')
@section('content')
<div class="container-fluid">
  <div class="col-lg-12 col-sm-6">
    <div class="card h-100">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
              <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Tambah & Ubah Role User <span class="badge rounded-pill bg-success">{{ $item->name }}</span></h5>
              <div><small>Pengaturan tambah dan edit hak akses user</small> </div>
            </div>
            <div>                        
                <a href="{{ route('assign_roles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left me-sm-1"></i> Kembali ke List User Role</a>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end justify-content-center">
          <div class="card-body pb-0 pt-3 position-absolute bottom-0">
            <img src="{{ url('backend/assets/img/illustrations/card-ratings-illustration.png') }}" alt="Ratings" width="140">
          </div>
        </div>
      </div>
    </div>
  </div> 
    <div class="card shadow mb-4 mt-3">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                     
        </div>
        <form action="{{ route('assign_roles.update', $item->id) }}" method="POST">
          @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
                        <input type="hidden" name="user_id">
                        <div class="input-group input-group-merge mb-4">
                            <span id="name" class="input-group-text"
                              ><i class="mdi mdi-account"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror" readonly/>
                              <label for="name">Nama User</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                      <div class="input-group input-group-merge mb-4">
                        <span id="email" class="input-group-text"
                          ><i class="mdi mdi-account-hard-hat"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') ? old('email') : $item->jabatan }}" class="form-control @error('email') is-invalid @enderror" readonly/>
                          <label for="email">Jabatan</label>
                          @error('email')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>                            
                      </div>                        
                    </div>                    
                </div>
                
                <hr>

                @php
                  function randomColor() {
                      // Generate random RGB values
                      $red = mt_rand(0, 255);
                      $green = mt_rand(0, 255);
                      $blue = mt_rand(0, 255);

                      // Convert RGB to hexadecimal
                      $color = sprintf("#%02x%02x%02x", $red, $green, $blue);

                      return $color;
                  }

                  $icons = ['mdi-account-tie-hat', 'mdi-account-tie', 'mdi-account-card', 'mdi-account-key', 'mdi-comment-account-outline', 'mdi-clipboard-account-outline', 'mdi-account-switch', 'mdi-account-star-outline', 'mdi-account-supervisor-circle', 'mdi-account-clock', 'mdi-shield-account'];
              @endphp

              <div class="row">
                  @foreach($roles as $role)
                      @php
                          // Ambil ikon secara acak dari daftar ikon
                          $randomIcon = $icons[array_rand($icons)];
                          // Generate random color
                          $randomBackgroundColor = randomColor();
                      @endphp
                      <div class="col-lg-3 col-sm-6 mt-3">
                          <div class="card">
                              <div class="card-body">
                                  <ul class="list-unstyled mb-0">
                                      <li class="d-flex pb-1">
                                          <div class="avatar flex-shrink-0 me-3">
                                              <span class="avatar-initial rounded" style="background-color: {{ $randomBackgroundColor }};">
                                                  <i class="mdi {{ $randomIcon }} mdi-24px"></i>
                                              </span>
                                          </div>
                                          <div class="row w-100 align-items-center">
                                              <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                                                  <h6 class="mb-0 lh-sm">{{ $role->name }}</h6>
                                              </div>
                                              <div class="col-sm-4 col-lg-12 col-xxl-4 text-sm-end text-lg-start text-xxl-end">
                                                  <div class="badge bg-label-secondary rounded-pill fw-normal">
                                                      <input name="role_id" class="form-check-input" type="radio" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ $item->hasRole($role->name) ? 'checked' : '' }} onclick="logSelectedRole({{ $role->id }})">
                                                  </div>
                                              </div>
                                          </div>
                                      </li>
                                  </ul>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <div class="form-group">
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
  function randomColor() {
      // Generate random RGB values
      var red = Math.floor(Math.random() * 256);
      var green = Math.floor(Math.random() * 256);
      var blue = Math.floor(Math.random() * 256);

      // Convert RGB to hexadecimal
      var color = '#' + red.toString(16).padStart(2, '0') +
                  green.toString(16).padStart(2, '0') +
                  blue.toString(16).padStart(2, '0');

      return color;
  }

  // Gunakan fungsi randomColor() di sini
</script>

@endpush