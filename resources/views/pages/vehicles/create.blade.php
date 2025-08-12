@push('style')
<style>
  #customer_id + .select2-container .select2-selection--single {
      height: 45px;
      padding: 10px;
  }
  #group_id + .select2-container .select2-selection--single {
      height: 45px;
      padding: 10px;
  }
</style>
@endpush

@extends('layouts.admin')
@section('title', 'Tambah Kendaraan')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Tambah Kendaraan</strong> <a href="{{ route('vehicles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('vehicles.store') }}" method="POST">
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">
                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-dump-truck"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="no_pol"
                            placeholder="Ketik nomor polisi disini"
                            aria-label="Ketik nomor polisi disini"
                            aria-describedby="no_pol" 
                            name="no_pol"
                            value="{{ old('no_pol') }}" class="form-control @error('no_pol') is-invalid @enderror"/>
                          <label for="no_pol">Nomor Polisi</label>
                          @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>
                      {{-- <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <select name="customer_id" id="customer_id" class="select2 form-select form-control @error('customer_id') is-invalid @enderror" data-allow-clear="true">
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                              <option value="{{ $customer->id }}">
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
                          <select name="group_id" id="group_id" class="select2 form-select form-control @error('group_id') is-invalid @enderror" data-allow-clear="true">
                            {{-- <option value="" disabled selected>Pilih Customer Dulu</option> --}}
                            <option value="1">Non Group</option>
                          </select>                        
                          <label for="group_id">Group</label>
                          @error('group_id')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>                                                            
                    </div> 
                    <div class="col-md-6">
                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                            <select name="vehicle_type" id="vehicle_type" class="select2 form-select form-control @error('vehicle_type') is-invalid @enderror" data-allow-clear="true">
                                <option value="" {{ old('vehicle_type') == '' ? 'selected' : '' }}>Pilih Jenis Kendaraan</option>
                                <option value="0" {{ old('vehicle_type') == '0' ? 'selected' : '' }}>Mobil</option>
                                <option value="1" {{ old('vehicle_type') == '1' ? 'selected' : '' }}>Sepeda Motor</option>
                            </select>
                            <label for="vehicle_type">Jenis Kendaraan</label>
                            @error('vehicle_type')<div class="text-danger">{{ $message }}</div> @enderror
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

    $('#group_id').select2({
        allowClear: true,
        // placeholder: 'Pilih Group',
        dropdownAutoWidth: true,
        width: '100%',
    });

    $('#customer_id').on('change', function() {
      var customerId = $(this).val();

      // Periksa jika ada customer yang dipilih
      // if (customerId) {
          // Hapus opsi "Pilih Customer Dulu" dan aktifkan dropdown group_id
         // $('#group_id').prop('disabled', false);
          
          // Lakukan request AJAX untuk mendapatkan grup berdasarkan customer_id
          $.ajax({
              url: '/group-vehicle/' + customerId,
              method: 'GET',
              success: function(data) {
                  // Kosongkan opsi grup sebelumnya (tetapi jangan hapus "Non Group")
                  $('#group_id').empty().append('<option value="" disabled selected>Pilih Group</option><option value="1">Non Group</option>');

                  // Tambahkan opsi grup baru berdasarkan data yang diterima
                  $.each(data, function(index, group) {
                      $('#group_id').append('<option value="' + group.id + '">' + group.name + '</option>');
                  });

                  // Refresh select2 agar update opsi baru
                  $('#group_id').trigger('change');
              },
              error: function() {
                  alert('Terjadi kesalahan saat mengambil data grup.');
              }
          });
     // } else {
          // Jika tidak ada customer yang dipilih, kosongkan opsi grup
          // $('#group_id').empty().append('<option value="1">Non Group</option><option value="" disabled selected>Pilih Customer Dulu</option>');
          // $('#group_id').prop('disabled', true);  // Disable select2 untuk group
      //}
    });  
  });
</script>


@endpush
