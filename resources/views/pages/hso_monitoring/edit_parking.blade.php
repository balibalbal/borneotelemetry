@push('style')

@endpush

@extends('layouts.admin')
@section('title', 'Edit Parking')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Status Kirim Data Parkir</strong> <a href="{{ route('hso_parking.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('hso_parking.update', $item->id) }}" method="POST">
            @method('PUT')
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
                            name="no_pol"
                            value="{{ old('no_pol') ? old('no_pol') : $item->no_pol }}" class="form-control @error('no_pol') is-invalid @enderror" readonly/>
                          <label for="no_pol">Nopol</label>
                          @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror 
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
                        <span class="input-group-text"
                          ><i class="mdi mdi-engine"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="acc"
                            name="acc" value="{{ old('acc') ? old('acc') : $item->acc }}"
                            class="form-control @error('email') is-invalid @enderror" />
                          <label for="acc">Ignition</label>
                        </div>
                      </div>                  
                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-latitude"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="latitude"
                            name="latitude" value="{{ old('latitude') ? old('acc') : $item->latitude }}"
                            class="form-control @error('latitude') is-invalid @enderror" readonly/>
                          <label for="latitude">Latitude</label>
                        </div>
                      </div>                   
                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-longitude"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="longitude"
                            name="longitude" value="{{ old('longitude') ? old('acc') : $item->longitude }}"
                            class="form-control @error('longitude') is-invalid @enderror" readonly/>
                          <label for="longitude">Longitude</label>
                        </div>
                      </div>                   
                    </div> 
                    <div class="col-md-6">                          
                          <div class="input-group input-group-merge mb-4">
                              <span class="input-group-text"
                                ><i class="mdi mdi-timer"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="duration"
                                  name="duration" value="{{ old('duration') ? old('duration') : $item->duration }}"
                                  class="form-control @error('duration') is-invalid @enderror" readonly/>
                                <label for="duration">Durasi</label>
                              </div>
                          </div>  
                          <div class="input-group input-group-merge mb-4">
                              <span id="email" class="input-group-text"
                                ><i class="mdi mdi-clock-in"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="off"
                                  name="off" value="{{ old('off') ? old('off') : $item->off }}"
                                  class="form-control @error('email') is-invalid @enderror" readonly/>
                                <label for="off">Enter Parking</label>
                              </div>
                          </div>  
                          <div class="input-group input-group-merge mb-4">
                              <span id="email" class="input-group-text"
                                ><i class="mdi mdi-clock-out"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="on"
                                  name="on" value="{{ old('on') ? old('on') : $item->on }}"
                                  class="form-control @error('email') is-invalid @enderror" readonly/>
                                <label for="on">Exit Parking</label>
                              </div>
                          </div>  
                          <div class="input-group input-group-merge mb-4">
                              <span id="email" class="input-group-text"
                                ><i class="mdi mdi-information"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="info"
                                  name="info" value="{{ old('info') ? old('info') : $item->info }}"
                                  class="form-control @error('email') is-invalid @enderror" readonly/>
                                <label for="info">Informasi</label>
                              </div>
                          </div>  
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="status" class="form-control @error('status') is-invalid @enderror">
                                  <option value="">- Pilih Status -</option>
                                  <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Sukses Kirim</option>
                                  <option value="2" {{ $item->status == 2 ? 'selected' : '' }}>Gagal Kirim</option>
                                  <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Belum Dikirim</option>
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