@push('style')
<style>
  #customer_id + .select2-container .select2-selection--single {
      height: 45px;
      padding: 10px;
  }
</style>
@endpush

@extends('layouts.admin')
@section('title', 'Tambah Group')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Tambah Group</strong> <a href="{{ route('groups.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('groups.store') }}" method="POST">
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
                        <div class="input-group input-group-merge mb-4">
                            <span id="name" class="input-group-text"
                              ><i class="mdi mdi-alpha-g-circle"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="name"
                                placeholder="Ketik nama group disini"
                                aria-label="Ketik nama group disini"
                                aria-describedby="name" 
                                name="name"
                                value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"/>
                              <label for="name">Nama Group</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div> 
                        @if(auth()->user()->customer_id == 1)
                        <div class="input-group input-group-merge mb-4">
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
                        </div>  
                        @else
                        <input type="hidden" name="customer_id" value="{{auth()->user()->customer_id}}" />
                        @endif
                    </div> 
                    <div class="col-md-6">
                        <div class="input-group input-group-merge mb-4">
                            <span id="description" class="input-group-text"
                              ><i class="mdi mdi-note-edit-outline"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="description"
                                placeholder="Ketik deskripsi disini"
                                aria-label="Ketik deskripsi disini"
                                aria-describedby="description" 
                                name="description"
                                value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror"/>
                              <label for="description">Deskripsi Group</label>
                              @error('description')<div class="text-danger">{{ $message }}</div> @enderror 
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