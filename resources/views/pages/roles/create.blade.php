@extends('layouts.admin')
@section('title', 'Tambah Role')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Tambah Role</strong> <a href="{{ route('roles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-login"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                class="form-control"
                                id="name"
                                placeholder="Ketik nama role disini"
                                aria-label="Ketik nama role disini"
                                aria-describedby="name" 
                                name="name"
                                value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"/>
                              <label for="name">Nama Role</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>                       
                    </div> 
                    <input type = "hidden" name= "guard_name" value ='web'/>
                </div>   
            </div>
            <div class="card-footer d-flex justify-content-start">
                <div class="form-group">
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection