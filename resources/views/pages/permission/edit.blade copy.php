@extends('layouts.admin')
@section('content')
<div class="container-fluid">
  <div class="col-lg-12 col-sm-6">
    <div class="card h-100">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
              <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Tambah & Ubah Hak Akses Role {{ $item->name }}</h5>
              <div><small>Pengaturan tambah dan edit hak akses untuk role <span class="badge rounded-pill bg-label-success">{{ $item->name }}</span></small> </div>
            </div>
            <div>                        
                <a href="{{ route('permissions.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left me-sm-1"></i> Kembali ke List User Role</a>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end justify-content-center">
          <div class="card-body pb-0 pt-3 position-absolute bottom-0">
            <img src="{{ url('backend/assets/img/illustrations/account-settings-security-illustration.png') }}" alt="Ratings" width="120">
          </div>
        </div>
      </div>
    </div>
  </div> 
    <div class="card shadow mb-4 mt-3">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                     
        </div>
        <form action="{{ route('permissions.update', $item->id) }}" method="POST">
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
                                placeholder="Ketik nama disini"
                                aria-label="Ketik nama disini"
                                aria-describedby="name" 
                                name="name"
                                value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror" readonly/>
                              <label for="name">Nama Role</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                      <div class="input-group input-group-merge mb-4">
                        <span id="email" class="input-group-text"
                          ><i class="mdi mdi-email-lock"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="email"
                            id="email"
                            placeholder="Ketik email disini"
                            aria-label="Ketik email disini"
                            aria-describedby="email" 
                            name="email"
                            value="{{ old('email') ? old('email') : $item->email }}" class="form-control @error('email') is-invalid @enderror" readonly/>
                          <label for="email">Email</label>
                          @error('email')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>                            
                      </div>                        
                    </div>                    
                </div>
                
                <hr>
                @php
    $groupedPermissions = $permissions->groupBy('group');
@endphp

<div class="row">
    @foreach($groupedPermissions as $name => $groupedPermission)
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 me-4 me-sm-0">
                        <!-- Avatar dan konten disini -->
                        
                      <div class="content-right">
                          <p class="mb-0 fw-medium">{{ $name }}</p>
                          <p class="text-muted">{{ $groupedPermission->count() }} izin</p>
                          @foreach($groupedPermission as $permission)
                              <div class="form-check">
                                  <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                              </div>
                          @endforeach
                      </div>
                      
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

                <div class="row">
                  @foreach($permissions as $permission)
                  <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-warning h-100">
                      <div class="card-body">
                        <div class="d-flex align-items-center gap-3 me-4 me-sm-0">
                          @if (Str::contains($permission->name, 'user'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-danger rounded">
                                <i class="mdi mdi-account-multiple-check mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                          @elseif (Str::contains($permission->name, 'supir'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-warning rounded">
                                <i class="mdi mdi-clipboard-account mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'customer'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-dark rounded">
                                <i class="mdi mdi-account-multiple-outline mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'area'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-info rounded">
                                <i class="mdi mdi-city-variant-outline mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'depo'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-grey rounded">
                                <i class="mdi mdi-warehouse mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'kendaraan'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-success rounded">
                                <i class="mdi mdi-truck-cargo-container mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'rute'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-danger rounded">
                                <i class="mdi mdi-map-marker-radius mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'uang'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-warning rounded">
                                <i class="mdi mdi-cash-100 mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'monitoring'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-dark rounded">
                                <i class="mdi mdi-map-search-outline mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                            @elseif (Str::contains($permission->name, 'laporan'))
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-info rounded">
                                <i class="mdi mdi-chart-bar mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                          @else
                            <div class="avatar avatar-md">
                              <div class="avatar-initial bg-label-primary rounded">
                                <i class="mdi mdi-laptop mdi-36px"></i>
                              </div>
                            </div>
                            <div class="content-right">
                              <p class="mb-0 fw-medium">{{ $permission->name }}</p>
                              <div class="form-check">
                                <input name="role_permissions[]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label> --}}
                              </div>
                            </div>
                          @endif
                        </div>                                                
                      </div>
                    </div>
                  </div>
                @endforeach
                </div>
                
            </div>
            <div class="card-footer">
                <div class="form-group  d-flex justify-content-between">
                  <a href="{{ route('permissions.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left me-sm-1"></i> Kembali</a>
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Simpan</button>                    
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')

@endpush