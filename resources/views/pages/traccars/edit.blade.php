@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Traccars</strong> <a href="{{ url('/list-traccars') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('traccars.update', $item->id) }}" method="POST">
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
                                class="form-control"
                                id="geofence"
                                aria-describedby="geofence" 
                                name="geofence"
                                value="{{ old('geofence') ? old('geofence') : $item->geofence }}" class="form-control @error('geofence') is-invalid @enderror"/>
                              <label for="geofence">Geofence</label>
                              @error('geofence')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-map-marker-radius"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="geofence_name"
                                aria-describedby="geofence_name" 
                                name="geofence_name"
                                value="{{ old('geofence_name') ? old('geofence_name') : $item->geofence_name }}" class="form-control @error('geofence_name') is-invalid @enderror"/>
                              <label for="geofence_name">Geofence Name</label>
                              @error('geofence_name')<div class="text-danger">{{ $message }}</div> @enderror 
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