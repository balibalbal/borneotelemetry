@push('style')
    <style>
        #customer_id + .select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }
    </style>
@endpush
@extends('layouts.admin')
@section('title', 'Tambah Geofence Radius')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Tambah Geofence Radius</strong> <a href="{{ route('geofence.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('geofence.simpan') }}" method="POST">
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
                        <div class="input-group input-group-merge mb-4">
                            <span id="name" class="input-group-text"
                              ><i class="mdi mdi-map-marker-radius"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="name"
                                placeholder="Ketik nama area disini"
                                aria-label="Ketik nama area disini"
                                aria-describedby="name" 
                                name="name"
                                value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required/>
                              <label for="name">Nama Geofence</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>  
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="customer_id" id="customer_id" class="form-select form-control @error('customer_id') is-invalid @enderror" data-allow-clear="true" required>
                                  <option value="">Pilih Customer</option>
                                  @foreach($customers as $customer)
                                  <option value="{{ $customer->id }}" data-no-pol="{{ $customer->id }}">
                                      {{ $customer->name }} 
                                  </option>
                                  @endforeach
                              </select>
                              <label for="customer_id">Nama Customer</label>
                              @error('customer_id')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-latitude"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="lat"
                                placeholder="Ketik latitude disini"
                                aria-label="Ketik latitude disini"
                                aria-describedby="lat" 
                                name="lat"
                                value="{{ old('lat') }}" class="form-control @error('lat') is-invalid @enderror" required/>
                              <label for="lat">Latitude</label>
                              @error('lat')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                                            
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-longitude"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="longi"
                                placeholder="Ketik longitude disini"
                                aria-label="Ketik longitude disini"
                                aria-describedby="longi" 
                                name="longi"
                                value="{{ old('longi') }}" class="form-control @error('longi') is-invalid @enderror" required/>
                              <label for="longi">Longitude</label>
                              @error('longi')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>                                                 
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-radius"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="radius"
                                placeholder="Ketik radius disini"
                                aria-label="Ketik radius disini"
                                aria-describedby="radius" 
                                name="radius"
                                value="{{ old('radius') }}" class="form-control @error('radius') is-invalid @enderror" required/>
                              <label for="radius">Radius</label>
                              @error('radius')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div> 
                        
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="status" id="status" class="select2 form-select form-control @error('status') is-invalid @enderror" data-allow-clear="true">
                                  <option value="" {{ old('status') == '' ? 'selected' : '' }}>Pilih Status</option>
                                  <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                  <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                              </select>
                              <label for="status">Status</label>
                              @error('status')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>    
                        <input type="hidden" name="type" value="1">                                             
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