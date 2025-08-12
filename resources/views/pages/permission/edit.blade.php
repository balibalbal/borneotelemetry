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
                <a href="{{ route('permissions.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left me-sm-1"></i> Kembali ke List Hak Akses Role</a>
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
                </div>
                
                <hr>
                @php
                    $groupedPermissions = $permissions->groupBy('permission_group');
                @endphp

                <div class="row">
                    @foreach($groupedPermissions as $name => $groupedPermission)
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card card-border-shadow-warning h-100">
                                <div class="card-body">
                                      <div class="content-right">
                                          <p class="mb-0 fw-medium">{{ strtoupper($name) }}</p>
                                          <div class="form-check">
                                            <input id="select-all-{{ $name }}" class="form-check-input select-all-checkbox" type="checkbox">
                                            <label class="form-check-label" for="select-all-{{ $name }}">Pilih Semua</label>
                                          </div>
                                          <p class="text-muted">{{ $groupedPermission->count() }} izin</p>
                                          
                                          @foreach($groupedPermission as $permission)
                                              <div class="form-check">
                                                  <input name="role_permissions[]" class="form-check-input permission-checkbox permission-checkbox-{{ $name }}" type="checkbox" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $item->hasPermissionTo($permission) ? 'checked' : '' }}>
                                                  <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                              </div>
                                          @endforeach
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
<script>
  // Handle Select All checkbox per group
  $(document).on('change', '.select-all-checkbox', function(){
      var groupName = $(this).attr('id').replace('select-all-', '');
      $('.permission-checkbox-' + groupName).prop('checked', $(this).prop('checked'));
  });

  // Handle individual permission checkbox change
  $(document).on('change', '.permission-checkbox', function(){
      var groupName = $(this).attr('class').split(' ')[2].replace('permission-checkbox-', '');
      var allChecked = true;
      $('.permission-checkbox-' + groupName).each(function(){
          if(!$(this).prop('checked')){
              allChecked = false;
          }
      });
      $('#select-all-' + groupName).prop('checked', allChecked);
  });
</script>

@endpush