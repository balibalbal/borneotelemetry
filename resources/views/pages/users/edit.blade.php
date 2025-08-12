@push('style')
    <style>
        #customer_id + .select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }
    </style>
@endpush

@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Akun</strong> <a href="{{ route('users.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('users.update', $item->id) }}" method="POST">
          @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
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
                                value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror"/>
                              <label for="name">Nama</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                        
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
                            value="{{ old('email') ? old('email') : $item->email }}" class="form-control @error('email') is-invalid @enderror"/>
                          <label for="email">Email</label>
                          @error('email')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>                            
                      </div>
                      
                      <div class="input-group input-group-merge mb-4">
                        <span id="password" class="input-group-text"
                          ><i class="mdi mdi-lock-reset"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="password"
                            id="password"
                            placeholder="Ketik password disini"
                            aria-label="Ketik password disini"
                            aria-describedby="password" 
                            name="password"
                            value="{{ old('password') }}" class="form-control @error('password') is-invalid @enderror"/>
                          <label for="password">Password</label>
                          @error('password')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>                      
                    </div> 
                    <div class="col-md-6"> 
                        {{-- <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                            <select name="customer_id" id="customer_id" class="select2 form-select form-control @error('customer_id') is-invalid @enderror" data-allow-clear="true">
                              <option value="">Pilih Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id == $item->customer_id ? 'selected' : '' }}>
                                        {{ $customer->name }} 
                                    </option>
                                @endforeach 
                            </select>
                            <label for="customer_id">Customer</label>
                            @error('customer_id')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div> --}}
                        
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                            <select name="user_type" id="user_type" class="select2 form-select form-control @error('user_type') is-invalid @enderror" data-allow-clear="true">
                                <option value="">Pilih Tipe User</option>
                                <option value="0" {{ $item->user_type == '0' ? 'selected' : '' }}>Speedtrack</option>
                                <option value="1" {{ $item->user_type == '1' ? 'selected' : '' }}>Customer</option>
                            </select>
                            <label for="user_type">Tipe User</label>
                            @error('user_type')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                            <select name="status" id="status" class="select2 form-select form-control @error('status') is-invalid @enderror" data-allow-clear="true">
                                <option value="">Pilih Status</option>
                                <option value="1" {{ $item->status == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $item->status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <label for="status">Status</label>
                            @error('status')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>
                        {{-- <input type="hidden" name="akses" value=""> --}}
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                            <select name="akses" id="akses" class="select2 form-select form-control @error('akses') is-invalid @enderror" data-allow-clear="true">
                                <option value="">Pilih Akses</option>
                                <option value="0" {{ $item->akses == '0' ? 'selected' : '' }}>All</option>
                                <option value="1" {{ $item->akses == '1' ? 'selected' : '' }}>Web</option>
                                <option value="2" {{ $item->akses == '2' ? 'selected' : '' }}>Mobile</option>
                            </select>
                            <label for="akses">Akses Device</label>
                            @error('akses')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>
                    </div>                    
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('#customer_id').select2({
        allowClear: true,
        placeholder: 'Pilih Customer',
        dropdownAutoWidth: true,
        width: '100%',
    });
  });
</script>

@endpush