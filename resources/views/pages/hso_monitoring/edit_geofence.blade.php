@push('style')

@endpush

@extends('layouts.admin')
@section('title', 'Edit Parking')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Status Kirim Data Geofence</strong> <a href="{{ route('histories_geofence.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('histories_geofence.update', $item->id) }}" method="POST">
            @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">
                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-map-marker-circle"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') ? old('name') : $item->geofence_name }}" class="form-control @error('name') is-invalid @enderror" readonly/>
                          <label for="name">Nama Geofence</label>
                          @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>   
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
                        <span id="email" class="input-group-text"
                          ><i class="mdi mdi-clock-in"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="off"
                            name="off" value="{{ old('off') ? old('off') : $item->enter_time }}"
                            class="form-control @error('email') is-invalid @enderror" readonly/>
                          <label for="off">Enter Geofence</label>
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
                              name="on" value="{{ old('on') ? old('on') : $item->exit_time }}"
                              class="form-control @error('email') is-invalid @enderror" readonly/>
                            <label for="on">Exit Geofence</label>
                          </div>
                      </div>                   
                    </div> 
                    <div class="col-md-6">  
                          <div class="input-group input-group-merge mb-4">
                              <span id="email" class="input-group-text"
                                ><i class="mdi mdi-information"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="note"
                                  name="note" value="{{ old('note') ? old('note') : $item->note }}"
                                  class="form-control @error('email') is-invalid @enderror" readonly/>
                                <label for="note">Informasi</label>
                              </div>
                          </div>  
                          <div class="input-group input-group-merge mb-4">
                              <span id="email" class="input-group-text"
                                ><i class="mdi mdi-clock"></i
                              ></span>
                              <div class="form-floating form-floating-outline">
                                <input
                                  type="text"
                                  id="updated_at"
                                  name="updated_at" value="{{ old('updated_at') ? old('updated_at') : $item->updated_at }}"
                                  class="form-control @error('updated_at') is-invalid @enderror" readonly/>
                                <label for="updated_at">Update</label>
                              </div>
                          </div>  
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="status_geofence" class="form-control @error('status_geofence') is-invalid @enderror">
                                  <option value="">- Pilih Status Geofence -</option>
                                  <option value="1" {{ $item->status_geofence == 1 ? 'selected' : '' }}>Masuk Geofence</option>
                                  <option value="2" {{ $item->status_geofence == 2 ? 'selected' : '' }}>Keluar Geofence</option>
                              </select>
                                <label for="status_geofence">Status Geofence</label>
                                @error('status_geofence')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>                    
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="status_kirim" class="form-control @error('status_kirim') is-invalid @enderror">
                                  <option value="">- Pilih Status Kirim -</option>
                                  <option value="1" {{ $item->status_kirim == 1 ? 'selected' : '' }}>Fence Enter Terkirim</option>
                                  <option value="2" {{ $item->status_kirim == 2 ? 'selected' : '' }}>Fence Exit Terkirim</option>
                                  <option value="3" {{ $item->status_kirim == 3 ? 'selected' : '' }}>Gagal Terkirim</option>
                                  <option value="0" {{ $item->status_kirim == 0 ? 'selected' : '' }}>Belum Dikirim</option>
                              </select>
                                <label for="status_kirim">Status Kirim</label>
                                @error('status_kirim')<div class="text-danger">{{ $message }}</div> @enderror
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