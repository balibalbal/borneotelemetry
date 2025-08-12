@push('style')

@endpush

@extends('layouts.admin')
@section('title', 'Edit Customer')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Customer</strong> <a href="{{ route('customers.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('customers.update', $item->id) }}" method="POST">
            @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">
                      <div class="input-group input-group-merge mb-4">
                        <span id="name" class="input-group-text"
                          ><i class="mdi mdi-account-outline"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="name"
                            placeholder="Ketik nama lengkap disini"
                            aria-label="Ketik nama lengkap disini"
                            aria-describedby="name" 
                            name="name"
                            value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror"/>
                          <label for="name">Nama Customer</label>
                          @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>   
                      <div class="input-group input-group-merge mb-4">
                        <span id="address" class="input-group-text"><i class="mdi mdi-map-marker"></i></span>
                        <div class="form-floating form-floating-outline">
                            <textarea
                                id="address"
                                placeholder="Ketik alamat disini"
                                aria-label="Ketik alamat disini"
                                aria-describedby="address"
                                style="height: 100px"
                                name="address"
                                class="form-control @error('address') is-invalid @enderror"
                            >{{ old('address') ? old('address') : $item->address }}</textarea>
                            <label for="address">Alamat</label>
                            @error('address')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>    
                      <div class="input-group input-group-merge mb-4">
                        <span id="phone" class="input-group-text"
                          ><i class="mdi mdi-phone"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="number"
                            id="phone"
                            placeholder="089 123 4567"
                            aria-label="089 123 4567"
                            aria-describedby="phone"
                            name="phone" 
                            value="{{ old('phone') ? old('phone') : $item->phone }}"
                            class="form-control phone-mask @error('seluler') is-invalid @enderror" />
                          <label for="phone">Nomor Telepon</label>
                        </div>
                      </div>                     
                    </div> 
                    <div class="col-md-6">                          
                          <div class="input-group input-group-merge mb-4">
                              <span id="email" class="input-group-text"
                                ><i class="mdi mdi-email"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="email"
                                  placeholder="contoh@example.com"
                                  aria-label="contoh@example.com"
                                  aria-describedby="email"
                                  name="email" value="{{ old('email') ? old('email') : $item->email }}"
                                  class="form-control @error('email') is-invalid @enderror" />
                                <label for="email">Email</label>
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
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Update Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')

@endpush