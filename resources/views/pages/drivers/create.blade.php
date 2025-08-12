@extends('layouts.admin')
@section('title', 'Tambah Supir')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Form Tambah Supir</strong> <a href="{{ route('drivers.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('drivers.store') }}" method="POST" enctype="multipart/form-data">
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6">                        
                        {{-- <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="divisi" id="divisi" class="select2 form-select form-control @error('divisi') is-invalid @enderror" data-allow-clear="true" onchange="isiDriverCode()">
                                  <option value="">Pilih Divisi</option>
                                  <option value="J" {{ old('divisi') == 'J' ? 'selected' : '' }}>DIVISI KOTA - KOTA JAKARTA</option>
                                  <option value="S" {{ old('divisi') == 'S' ? 'selected' : '' }}>DIVISI BPPI</option>
                                  <option value="A" {{ old('divisi') == 'A' ? 'selected' : '' }}>DIVISI KOTA - KOTA SURABAYA</option>
                                  <option value="M" {{ old('divisi') == 'M' ? 'selected' : '' }}>DIVISI INDORAMA</option>
                                  <option value="G" {{ old('divisi') == 'G' ? 'selected' : '' }}>DIVISI GANDENGAN</option>
                                  <option value="T" {{ old('divisi') == 'T' ? 'selected' : '' }}>DIVISI TANGKI</option>
                                  <option value="W" {{ old('divisi') == 'W' ? 'selected' : '' }}>DIVISI WINGBOX</option>
                              </select>
                              <label for="divisi">Divisi</label>
                              @error('divisi')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div> --}}
                      <input type="hidden" name="customer_id" value="{{ auth()->user()->customer_id }}" />
                      <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text">
                              <i class="mdi mdi-identifier"></i>
                          </span>
                          <div class="form-floating form-floating-outline">
                              <input
                                  type="text"
                                  id="driver_code"
                                  placeholder="Input kode supir disini"
                                  aria-label="Input kode supir disini"
                                  aria-describedby="driver_code" 
                                  name="driver_code"
                                  value="{{ old('driver_code') }}"
                                  class="form-control @error('driver_code') is-invalid @enderror"
                              />
                              <label for="driver_code">Kode Supir</label>
                              @error('driver_code')<div class="text-danger">{{ $message }}</div> @enderror 
                          </div>
                      </div>
                        
                        <div class="input-group input-group-merge mb-4">
                          <span id="name" class="input-group-text">
                              <i class="mdi mdi-account-outline"></i>
                          </span>
                          <div class="form-floating form-floating-outline">
                              <input
                                  type="text"
                                  id="name"
                                  placeholder="Ketik nama lengkap disini"
                                  aria-label="Ketik nama lengkap disini"
                                  aria-describedby="name" 
                                  name="name"
                                  value="{{ old('name') }}"
                                  class="form-control @error('name') is-invalid @enderror"
                              />
                              <label for="name">Nama Supir</label>
                              @error('name')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror 
                          </div>
                        </div>
                        
                      
                        <div class="input-group input-group-merge mb-4">
                            <span id="start_date" class="input-group-text"
                              ><i class="mdi mdi-calendar-range"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="date"
                                id="start_date"
                                class="form-control phone-mask datepicker @error('start_date') is-invalid @enderror"
                                aria-describedby="start_date"
                                name="start_date" value="{{ old('start_date') }}" />
                              <label for="start_date">Tanggal Masuk</label>
                              @error('start_date')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                            </div>
                        </div>
                        
                        <div class="input-group input-group-merge mb-4">
                            <span id="contract_end_date" class="input-group-text"
                              ><i class="mdi mdi-calendar-range"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="date"
                                id="contract_end_date"
                                class="form-control phone-mask datepicker @error('contract_end_date') is-invalid @enderror"
                                aria-describedby="basic-icon-default-phone2"
                                name="contract_end_date" value="{{ old('contract_end_date') }}" />
                              <label for="contract_end_date">Tanggal Habis Kontrak</label>
                            </div>
                        </div>
                        
                        <div class="input-group input-group-merge mb-4">
                            <span id="sim_number" class="input-group-text"
                              ><i class="mdi mdi-card-account-details"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="number"
                                id="sim_number"
                                placeholder="111 123 4567"
                                aria-label="111 123 4567"
                                aria-describedby="sim_number"
                                name="sim_number" value="{{ old('sim_number') }}" class="form-control @error('sim_number') is-invalid @enderror" />
                              <label for="sim_number">ID SIM</label>  
                              @error('sim_number')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror                            
                            </div>                            
                        </div>
                        
                        <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                              <select name="sim_type" id="sim_type" class="select2 form-select form-control @error('sim_type') is-invalid @enderror" data-allow-clear="true">
                                  <option value="" {{ old('sim_type') == '' ? 'selected' : '' }}>Pilih Jenis SIM</option>
                                  <option value="A" {{ old('sim_type') == 'A' ? 'selected' : '' }}>SIM A</option>
                                  <option value="B" {{ old('sim_type') == 'B' ? 'selected' : '' }}>SIM B</option>
                                  <option value="C" {{ old('sim_type') == 'C' ? 'selected' : '' }}>SIM C</option> 
                                  <option value="BII UMUM" {{ old('sim_type') == 'BII UMUM' ? 'selected' : '' }}>SIM BII UMUM</option>
                              </select>
                              <label for="sim_type">Jenis SIM</label>
                              @error('sim_type')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div>                      
                        
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-calendar-range"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="date"
                                id="expired_sim"
                                class="form-control datepicker @error('expired_sim') is-invalid @enderror"
                                aria-describedby="expired_sim"
                                name="expired_sim" value="{{ old('expired_sim') }}" />
                              <label for="expired_sim">Masa Berlaku SIM</label>
                            </div>
                        </div>
                        
                        <div class="input-group input-group-merge mb-4">
                            <span id="rekening_number" class="input-group-text"
                              ><i class="mdi mdi-numeric-9-plus-box"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="number"
                                id="rekening_number"
                                placeholder="111 123 4567"
                                aria-label="111 123 4567"
                                aria-describedby="rekening_number"
                                name="rekening_number" value="{{ old('rekening_number') }}" class="form-control phone-mask @error('rekening_number') is-invalid @enderror" />
                              <label for="rekening_number">No Rekening</label>
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                            <span id="rekening_name" class="input-group-text"
                              ><i class="mdi mdi-bank"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="rekening_name"
                                placeholder="Ketik nama lengkap disini"
                                aria-label="Ketik nama lengkap disini"
                                aria-describedby="rekening_name" 
                                name="rekening_name"
                                value="{{ old('rekening_name') }}" class="form-control @error('rekening_name') is-invalid @enderror"/>
                              <label for="rekening_name">Nama Rekening</label>
                              @error('rekening_name')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>
                        
                        <div class="input-group input-group-merge mb-4">
                            <span id="address" class="input-group-text"
                              ><i class="mdi mdi-home-city"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <textarea
                                id="address"
                                placeholder="Ketik alamat disini"
                                aria-label="Ketik alamat disini"
                                aria-describedby="address"
                                style="height: 100px"
                                name="address" class="form-control @error('address') is-invalid @enderror"></textarea>
                              <label for="address">Alamat</label>
                              @error('address')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text"
                            ><i class="mdi mdi-phone"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="number"
                              id="phone"
                              placeholder="089 123 4567"
                              aria-label="089 123 4567"
                              aria-describedby="phone"
                              name="phone" value="{{ old('phone') }}" class="form-control phone-mask @error('phone') is-invalid @enderror" />
                            <label for="phone">Nomor Telepon</label>
                          </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                            <select name="driver_position" id="driver_position" class="select2 form-select form-control @error('driver_position') is-invalid @enderror" data-allow-clear="true">
                                <option value="" {{ old('driver_position') == '' ? 'selected' : '' }}>Pilih Posisi Supir</option>
                                <option value="Supir Utama" {{ old('driver_position') == 'Supir Utama' ? 'selected' : '' }}>Supir Utama</option>
                                <option value="Supir Luar" {{ old('driver_position') == 'Supir Luar' ? 'selected' : '' }}>Supir Luar</option>
                                <option value="Helper" {{ old('driver_position') == 'Helper' ? 'selected' : '' }}>Helper</option>
                            </select>
                            <label for="driver_position">Posisi Supir</label>
                            @error('driver_position')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>
                      
                      {{-- <div class="input-group input-group-merge mb-4">
                          <span id="debt" class="input-group-text"
                            ><i class="mdi mdi-currency-usd"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="number"
                              id="debt"
                              placeholder="Rp 535533"
                              aria-label="Rp 535533"
                              aria-describedby="debt"
                              name="debt" value="{{ old('debt') }}" class="form-control @error('debt') is-invalid @enderror" />
                            <label for="debt">Hutang</label>
                            @error('debt')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                      </div> --}}
                      
                      <div class="input-group input-group-merge mb-4">
                          <span id="note" class="input-group-text"
                            ><i class="mdi mdi-message-outline"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <textarea
                              id="note"
                              class="form-control"
                              placeholder="Ketik disini jika ingin memberikan keterangan hutang"
                              aria-label="Ketik disini jika ingin memberikan keterangan hutang"
                              aria-describedby="note"
                              style="height: 60px"
                              name="note" class="form-control @error('note') is-invalid @enderror"></textarea>
                            <label for="note">Keterangan / Catatan</label>
                            @error('note')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                      </div>
                    </div> 
                    <div class="col-md-6">                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card" style="width: 100%;">
                                    <div class="card-header">Photo Supir</div>
                                    <img id="previewImageDriver" src="{{ url('backend/img/driver.png') }}" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <input class="form-control" type="file" id="photo_driver" name="photo_driver" onchange="previewFileDriver()">
                                        @error('photo_driver')<div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card" style="width: 100%;">
                                    <div class="card-header">Photo Sertifikat</div>
                                    <img id="previewImageCertificate" src="{{ url('backend/img/certificate.png') }}" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <input class="form-control" type="file" id="photo_certificate_driver" name="photo_certificate_driver" onchange="previewFileSertifikat()">
                                        @error('photo_certificate_driver')<div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6">
                              <div class="card mt-3" style="width: 100%;">
                                  <div class="card-header">Photo KTP</div>
                                  <img id="previewImage" src="{{ url('backend/img/ktp.png') }}" class="card-img-top" alt="...">
                                  <div class="card-body">
                                      <input class="form-control" type="file" id="photo_ktp" name="photo_ktp" onchange="previewFileKtp()">
                                      @error('photo_ktp')<div class="text-danger">{{ $message }}</div> @enderror
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="card mt-3" style="width: 100%;">
                                  <div class="card-header">Photo SIM</div>
                                  <img id="previewImageSim" src="{{ url('backend/img/sim.png') }}" class="card-img-top" alt="...">
                                  <div class="card-body">
                                      <input class="form-control" type="file" id="photo_sim" name="photo_sim" onchange="previewFileSim()">
                                      @error('photo_sim')<div class="text-danger">{{ $message }}</div> @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                        <div><hr></div>
                        
                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-certificate"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="no_certificate"
                                placeholder="089 123 4567"
                                aria-label="089 123 4567"
                                aria-describedby="no_certificate"
                                name="no_certificate" value="{{ old('no_certificate') }}" class="form-control phone-mask @error('no_certificate') is-invalid @enderror" />
                              <label for="no_certificate">Nomor Sertifikat</label>
                            </div>
                        </div>
                        
                        <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text"
                            ><i class="mdi mdi-calendar-range"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="date"
                              id="masa_berlaku_certificate"
                              class="form-control datepicker @error('masa_berlaku_certificate') is-invalid @enderror"
                              aria-describedby="masa_berlaku_certificate"
                              name="masa_berlaku_certificate" value="{{ old('masa_berlaku_certificate') }}" />
                            <label for="masa_berlaku_certificate">Masa Berlaku Sertifikat</label>
                          </div>
                        </div>

                        {{-- <div class="input-group input-group-merge mb-4"> 
                          <div class="form-floating form-floating-outline">
                            <select name="nama_pt" id="nama_pt" class="select2 form-select form-control @error('nama_pt') is-invalid @enderror" data-allow-clear="true">
                              <option value="">Pilih PT</option>
                              <option value="PT. STAL">PT. Sinar Transindomitra Abadi Logistik</option>
                              <option value="PT. MMS">PT. Multisukses Mitra Sejati</option>
                            </select>
                            <label for="nama_pt">PT</label>
                            @error('nama_pt')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div> --}}

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
<script>
    function previewFileKtp() {
        var preview = document.getElementById('previewImage');
        var fileInput = document.getElementById('photo_ktp');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ url('backend/img/ktp.png') }}";
        }
    }
</script>

<script>
    function previewFileSim() {
        var preview = document.getElementById('previewImageSim');
        var fileInput = document.getElementById('photo_sim');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ url('backend/img/sim-default.png') }}";
        }
    }

    function previewFileDriver() {
        var preview = document.getElementById('previewImageDriver');
        var fileInput = document.getElementById('photo_driver');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ url('backend/img/driver.png') }}";
        }
    }

    function previewFileSertifikat() {
        var preview = document.getElementById('previewImageCertificate');
        var fileInput = document.getElementById('photo_certificate_driver');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ url('backend/img/certificate.png') }}";
        }
    }

  function isiDriverCode() {
      var divisi = document.getElementById('divisi');
      var driverCodeInput = document.getElementById('driver_code');

      // Setelah memilih divisi, isi driver_code dengan value divisi
      driverCodeInput.value = divisi.value !== '' ? divisi.value + '-' : '';
  }
</script>
@endpush