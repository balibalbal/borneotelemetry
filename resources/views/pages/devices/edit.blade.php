@push('style')
<style>
  #customer_id + .select2-container .select2-selection--single {
      height: 45px;
      padding: 10px;
  }

  #vehicle_id + .select2-container .select2-selection--single {
      height: 45px;
      padding: 10px;
  }
</style>
@endpush

@extends('layouts.admin')
@section('title', 'Edit Device')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Device</strong> <a href="{{ route('devices.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('devices.update', $item->id) }}" method="POST">
          @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
                      <div class="input-group input-group-merge mb-4">
                        <span id="name" class="input-group-text"
                          ><i class="mdi mdi-tablet-cellphone"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="name"
                            placeholder="Ketik nama device disini"
                            aria-label="Ketik nama device disini"
                            aria-describedby="name" 
                            name="name"
                            value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror"/>
                          <label for="name">Nama Device</label>
                          @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>
                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-barcode"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="number"
                            id="imei"
                            placeholder="Ketik imei disini"
                            aria-label="Ketik imei disini"
                            aria-describedby="imei" 
                            name="imei"
                            value="{{ old('imei') ? old('imei') : $item->imei }}" class="form-control @error('imei') is-invalid @enderror"/>
                          <label for="imei">Imei</label>
                          @error('imei')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-phone"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="number"
                            id="sim_number"
                            placeholder="089 123 4567"
                            aria-label="089 123 4567"
                            aria-describedby="sim_number"
                            name="sim_number"
                            value="{{ old('sim_number') ? old('sim_number') : $item->sim_number }}" class="form-control phone-mask @error('sim_number') is-invalid @enderror" />
                          <label for="sim_number">Nomor Telepon</label>
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <select name="type_id" id="type_id" class="select2 form-select form-control @error('type_id') is-invalid @enderror" data-allow-clear="true">
                            <option value="">Pilih Tipe</option>
                            @foreach($modem_type as $type)
                              <option value="{{ $type->id }}" {{ $type->id == $item->type_id ? 'selected' : '' }}>
                                  {{ $type->type }}
                              </option>
                            @endforeach
                          </select>
                          <label for="type_id">Tipe Modem</label>
                          @error('type_id')<div class="text-danger">{{ $message }}</div> @enderror
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
                          <select name="vehicle_id" id="vehicle_id" class="select2 form-select form-control @error('vehicle_id') is-invalid @enderror" data-allow-clear="true">
                            <option value="">Pilih Kendaraan</option>
                            @foreach($vehicles as $vehicle)
                              <option value="{{ $vehicle->id }}" {{ $vehicle->id == $item->vehicle_id ? 'selected' : '' }}>
                                  {{ $vehicle->no_pol }}
                              </option>
                            @endforeach
                          </select>
                          <label for="vehicle_id">Nomor Polisi</label>
                          @error('vehicle_id')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>
                         
                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <select name="status" class="form-control @error('status') is-invalid @enderror">
                              <option value="">- Pilih Status -</option>
                              <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Aktif</option>
                              <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Not Aktif</option>
                          </select>
                            <label for="status">Status</label>
                            @error('status')<div class="text-danger">{{ $message }}</div> @enderror
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

  
  $('#vehicle_id').select2({
      allowClear: true,
      placeholder: 'Pilih Kendaraan',
      dropdownAutoWidth: true,
      width: '100%',
  });

  
});
</script>


@endpush
