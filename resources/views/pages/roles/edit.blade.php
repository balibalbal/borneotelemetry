@extends('layouts.admin')
@section('title', 'Edit Role')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Form Edit Role</strong> <a href="{{ route('roles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('roles.update', $item->id) }}" method="POST">
            @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-merge mb-4">
                            <span id="name" class="input-group-text"
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
                                value="{{ old('name') ? old('name') : $item->name }}" class="form-control @error('name') is-invalid @enderror"/>
                              <label for="name">Nama Role</label>
                              @error('name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                    </div> 
                    {{-- <div class="col-md-6">
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
                                value="{{ old('description') ? old('description') : $item->description }}" class="form-control @error('description') is-invalid @enderror"/>
                              <label for="description">Deskripsi Group</label>
                              @error('description')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>                   
                    </div> --}}
                </div>   
            </div>
            <div class="card-footer d-flex justify-content-start">
                <div class="form-group">
                    <button class="btn rounded-pill btn-primary waves-effect waves-light" type="submit"><i class="mdi mdi-content-save-check me-sm-1"></i> Update Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection