@push('style')
    <style>
        #driver_id + .select2-container .select2-selection--single {
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
@section('title', 'Edit Kendaraan')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <strong>Edit Kendaraan</strong> <a href="{{ route('vehicles.index') }}" class="btn rounded-pill btn-dark waves-effect waves-light"><i class="mdi mdi-arrow-left-circle me-sm-1"></i> Kembali</a>
        </div>
        <form action="{{ route('vehicles.update', $item->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            <div class="card-body card-block">            
                @csrf
                <div class="row">
                    <div class="col-md-6"> 
                        {{-- <input type="hidden" name="customer_id" value="{{ auth()->user()->customer_id }}" /> --}}
                        {{-- <input type="hidden" name="customer_id" value="{{$item->customer_id }}" /> --}}
                        <div class="input-group input-group-merge mb-4">
                            <span id="no_pol" class="input-group-text"
                              ><i class="mdi mdi-car-3-plus"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="no_pol"
                                placeholder="Ketik nomor polisi disini"
                                aria-label="Ketik nomor polisi disini"
                                aria-describedby="no_pol" 
                                name="no_pol"
                                value="{{ old('no_pol') ? old('no_pol') : $item->no_pol }}" 
                                class="form-control @error('no_pol') is-invalid @enderror"/>
                              <label for="no_pol">Nomor Polisi</label>
                              @error('no_pol')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>

                        {{-- <div class="input-group input-group-merge mb-4"> 
                          <div class="form-floating form-floating-outline">
                            <select name="nama_pt" id="nama_pt" class="select2 form-select form-control @error('nama_pt') is-invalid @enderror" data-allow-clear="true">
                              <option value="">Pilih PT</option>
                              <option value="PT. STAL" {{ $item->nama_pt == 'PT. STAL' ? 'selected' : '' }}>PT. Sinar Transindomitra Abadi Logistik</option>
                              <option value="PT. MMS" {{ $item->nama_pt == 'PT. MMS' ? 'selected' : '' }}>PT. Multisukses Mitra Sejati</option>
                            </select>
                            <label for="nama_pt">PT</label>
                            @error('nama_pt')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div> --}}

                        {{-- <div class="input-group input-group-merge mb-4">
                          <span id="address_icon" class="input-group-text"><i class="mdi mdi-home-city"></i></span>
                          <div class="form-floating form-floating-outline">
                              <textarea
                                  id="address"
                                  placeholder="Ketik alamat disini"
                                  aria-label="Ketik alamat disini"
                                  aria-describedby="address"
                                  style="height: 70px"
                                  name="address"
                                  class="form-control @error('address') is-invalid @enderror"
                              >{{ old('address') ? old('address') : $item->address }}</textarea>
                              <label for="address">Alamat</label>
                              @error('address')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div> --}}

                        <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text"
                            ><i class="mdi mdi-truck-cargo-container"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <select name="vehicle_type" id="vehicle_type" class="select2 form-select form-control @error('vehicle_type') is-invalid @enderror" data-allow-clear="true">
                              <option value="">Pilih Jenis Kendaraan</option>
                              <option value="0" {{ $item->vehicle_type == '0' ? 'selected' : '' }}>Mobil</option>
                              <option value="1" {{ $item->vehicle_type == '1' ? 'selected' : '' }}>Sepeda Motor</option>
                            </select>
                            {{-- <input
                              type="text"
                              id="vehicle_type"
                              placeholder="Ketik tipe kendaraan disini"
                              aria-label="Ketik tipe kendaraan disini"
                              aria-describedby="vehicle_type" 
                              name="vehicle_type"
                              value="{{ old('vehicle_type') ? old('vehicle_type') : $item->vehicle_type }}" class="form-control @error('no_rangka') is-invalid @enderror"/> --}}
                            <label for="vehicle_type">Jenis Kendaraan</label>
                            @error('vehicle_type')<div class="text-danger">{{ $message }}</div> @enderror 
                          </div>
                      </div>
                       
                        {{-- <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                            <select name="divisi" id="divisi" class="select2 form-select form-control @error('divisi') is-invalid @enderror" data-allow-clear="true" onchange="isiDriverCode()">
                              <option value="">Pilih Divisi</option>
                              <option value="DIVISI KOTA-KOTA JAKARTA" {{ $item->divisi == 'DIVISI KOTA-KOTA JAKARTA' ? 'selected' : '' }}>DIVISI KOTA-KOTA JAKARTA</option>
                              <option value="DIVISI BPPI" {{ $item->divisi == 'DIVISI BPPI' ? 'selected' : '' }}>DIVISI BPPI</option>
                              <option value="DIVISI KOTA-KOTA SURABAYA" {{ $item->divisi == 'DIVISI KOTA-KOTA SURABAYA' ? 'selected' : '' }}>DIVISI KOTA-KOTA SURABAYA</option>
                              <option value="DIVISI INDORAMA" {{ $item->divisi == 'DIVISI INDORAMA' ? 'selected' : '' }}>DIVISI INDORAMA</option>
                              <option value="DIVISI GANDENGAN" {{ $item->divisi == 'DIVISI GANDENGAN' ? 'selected' : '' }}>DIVISI GANDENGAN</option>
                              <option value="DIVISI TANGKI" {{ $item->divisi == 'DIVISI TANGKI' ? 'selected' : '' }}>DIVISI TANGKI</option>
                            </select>
                            <label for="divisi">Divisi</label>
                            @error('divisi')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                        </div> --}}

                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-numeric"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="tahun_kendaraan"
                                placeholder="Ketik tahun disini"
                                aria-label="Ketik tahun disini"
                                aria-describedby="tahun_kendaraan" 
                                name="tahun_kendaraan"
                                value="{{ old('tahun_kendaraan') ? old('tahun_kendaraan') : $item->tahun_kendaraan }}" class="form-control @error('tahun_kendaraan') is-invalid @enderror"/>
                              <label for="tahun_kendaraan">Tahun Kendaraan</label>
                              @error('tahun_kendaraan')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-invert-colors"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="warna"
                                placeholder="Ketik warna disini"
                                aria-label="Ketik warna disini"
                                aria-describedby="warna" 
                                name="warna"
                                value="{{ old('warna') ? old('warna') : $item->warna }}" class="form-control @error('warna') is-invalid @enderror"/>
                              <label for="warna">Warna</label>
                              @error('warna')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-subtitles"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="type"
                                placeholder="Ketik tipe/merk disini"
                                aria-label="Ketik tipe/merk disini"
                                aria-describedby="type" 
                                name="type"
                                value="{{ old('type') ? old('type') : $item->type }}" class="form-control @error('type') is-invalid @enderror"/>
                              <label for="type">Merk/Type</label>
                              @error('type')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-numeric"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="no_rangka"
                                placeholder="Ketik nomor rangka disini"
                                aria-label="Ketik nomor rangka disini"
                                aria-describedby="no_rangka" 
                                name="no_rangka"
                                value="{{ old('no_rangka') ? old('no_rangka') : $item->no_rangka }}" class="form-control @error('no_rangka') is-invalid @enderror"/>
                              <label for="no_rangka">Nomor Rangka</label>
                              @error('no_rangka')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                            <span class="input-group-text"
                              ><i class="mdi mdi-numeric"></i
                            ></span>
                            <div class="form-floating form-floating-outline">
                              <input
                                type="text"
                                id="no_mesin"
                                placeholder="Ketik nomor mesin disini"
                                aria-label="Ketik nomor mesin disini"
                                aria-describedby="no_mesin" 
                                name="no_mesin"
                                value="{{ old('no_mesin') ? old('no_mesin') : $item->no_mesin }}" class="form-control @error('no_mesin') is-invalid @enderror"/>
                              <label for="no_mesin">Nomor Mesin</label>
                              @error('no_mesin')<div class="text-danger">{{ $message }}</div> @enderror 
                            </div>
                        </div>

                        <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text"
                            ><i class="mdi mdi-calendar-range"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="date"
                              id="expired_pajak"
                              class="form-control datepicker @error('expired_pajak') is-invalid @enderror"
                              aria-describedby="expired"
                              name="expired_pajak" value="{{ old('expired_pajak') ? old('expired_pajak') : substr($item->expired_pajak, 0, 10) }}" />
                            <label for="expired_pajak">Masa Berlaku Pajak</label>
                          </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                          <span id="expired_stnk" class="input-group-text"
                            ><i class="mdi mdi-calendar-range"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="date"
                              id="expired_stnk"
                              class="form-control datepicker @error('expired_stnk') is-invalid @enderror"
                              aria-describedby="expired_stnk"
                              name="expired_stnk" value="{{ old('expired_stnk') ? old('expired_stnk') : substr($item->expired_stnk, 0, 10) }}" />
                            <label for="expired_stnk">Masa Berlaku STNK</label>
                          </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-car-2-plus"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="head_kir"
                            placeholder="Ketik head KIR disini"
                            aria-label="Ketik head KIR disini"
                            aria-describedby="head_kir" 
                            name="head_kir"
                            value="{{ old('head_kir') ? old('head_kir') : $item->head_kir }}" class="form-control @error('head_kir') is-invalid @enderror"/>
                          <label for="head_kir">Head KIR</label>
                          @error('head_kir')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text"
                            ><i class="mdi mdi-calendar-range"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="date"
                              id="expired_head_kir"
                              class="form-control datepicker @error('expired_head_kir') is-invalid @enderror"
                              aria-describedby="expired_head_kir"
                              name="expired_head_kir" value="{{ old('expired_head_kir') ? old('expired_head_kir') : substr($item->expired_head_kir, 0, 10) }}" />
                            <label for="expired_head_kir">Masa Berlaku Head KIR</label>
                          </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-car-cog"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="chasis_kir"
                            placeholder="Ketik chasis KIR disini"
                            aria-label="Ketik chasis KIR disini"
                            aria-describedby="chasis_kir" 
                            name="chasis_kir"
                            value="{{ old('chasis_kir') ? old('chasis_kir') : $item->chasis_kir }}" class="form-control @error('chasis_kir') is-invalid @enderror"/>
                          <label for="chasis_kir">Chasis KIR</label>
                          @error('chasis_kir')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                          <span class="input-group-text"
                            ><i class="mdi mdi-calendar-range"></i
                          ></span>
                          <div class="form-floating form-floating-outline">
                            <input
                              type="date"
                              id="expired_chasis_kir"
                              class="form-control datepicker @error('expired_chasis_kir') is-invalid @enderror"
                              aria-describedby="expired_chasis_kir"
                              name="expired_chasis_kir" value="{{ old('expired_chasis_kir') ? old('expired_chasis_kir') : substr($item->expired_chasis_kir, 0, 10) }}" />
                            <label for="expired_chasis_kir">Masa Berlaku Chasis KIR</label>
                          </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-domain"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="nama_pt_chasis_kir"
                            placeholder="Ketik nama PT disini"
                            aria-label="Ketik nama PT disini"
                            aria-describedby="nama_pt_chasis_kir" 
                            name="nama_pt_chasis_kir"
                            value="{{ old('nama_pt_chasis_kir') ? old('nama_pt_chasis_kir') : $item->nama_pt_chasis_kir }}" class="form-control @error('nama_pt_chasis_kir') is-invalid @enderror"/>
                          <label for="nama_pt_chasis_kir">Nama PT Chasis KIR</label>
                          @error('nama_pt_chasis_kir')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-train-car-hopper"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="jenis_chasis"
                            placeholder="Ketik jenis chasis disini"
                            aria-label="Ketik jenis_chasis disini"
                            aria-describedby="jenis_chasis" 
                            name="jenis_chasis"
                            value="{{ old('jenis_chasis') ? old('jenis_chasis') : $item->jenis_chasis }}" class="form-control @error('jenis_chasis') is-invalid @enderror"/>
                          <label for="jenis_chasis">Jenis Chasis</label>
                          @error('jenis_chasis')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-numeric"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="nomor_chasis"
                            placeholder="Ketik nomor chasis disini"
                            aria-label="Ketik nomor chasis disini"
                            aria-describedby="nomor_chasis" 
                            name="nomor_chasis"
                            value="{{ old('nomor_chasis') ? old('nomor_chasis') : $item->nomor_chasis }}" class="form-control @error('nomor_chasis') is-invalid @enderror"/>
                          <label for="nomor_chasis">Nomor Chasis</label>
                          @error('nomor_chasis')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-train-car-hopper-covered"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="model_chasis"
                            placeholder="Ketik model chasis disini"
                            aria-label="Ketik model chasis disini"
                            aria-describedby="model_chasis" 
                            name="model_chasis"
                            value="{{ old('model_chasis') ? old('model_chasis') : $item->model_chasis }}" class="form-control @error('model_chasis') is-invalid @enderror"/>
                          <label for="model_chasis">Model Chasis</label>
                          @error('model_chasis')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-account-hard-hat"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="divisi_chasis"
                            placeholder="Ketik divisi chasis disini"
                            aria-label="Ketik divisi chasis disini"
                            aria-describedby="divisi_chasis" 
                            name="divisi_chasis"
                            value="{{ old('divisi_chasis') ? old('divisi_chasis') : $item->divisi_chasis }}" class="form-control @error('divisi_chasis') is-invalid @enderror"/>
                          <label for="divisi_chasis">Divisi Chasis</label>
                          @error('divisi_chasis')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-numeric"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="no_rekom_b3_klhk"
                            placeholder="Ketik nomor rekom b3 disini"
                            aria-label="Ketik nomor rekom b3 disini"
                            aria-describedby="no_rekom_b3_klhk" 
                            name="no_rekom_b3_klhk"
                            value="{{ old('no_rekom_b3_klhk') ? old('no_rekom_b3_klhk') : $item->no_rekom_b3_klhk }}" class="form-control @error('no_rekom_b3_klhk') is-invalid @enderror"/>
                          <label for="no_rekom_b3_klhk">Nomor Rekom B3 KLHK</label>
                          @error('no_rekom_b3_klhk')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-calendar-range"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="date"
                            id="expired_rekom"
                            class="form-control datepicker @error('expired_rekom') is-invalid @enderror"
                            aria-describedby="expired_rekom"
                            name="expired_rekom" value="{{ old('expired_rekom') ? old('expired_rekom') : substr($item->expired_rekom, 0, 10) }}" />
                          <label for="expired_rekom">Masa Berlaku Rekom</label>
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-calendar-range"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="date"
                            id="expired_kartu_kemenhub"
                            class="form-control datepicker @error('expired_kartu_kemenhub') is-invalid @enderror"
                            aria-describedby="expired_kartu_kemenhub"
                            name="expired_kartu_kemenhub" value="{{ old('expired_kartu_kemenhub') ? old('expired_kartu_kemenhub') : substr($item->expired_kartu_kemenhub, 0, 10) }}" />
                          <label for="expired_kartu_kemenhub">Masa Berlaku Kartu Kemenhub</label>
                        </div>
                      </div>

                      
                        {{-- <div class="input-group input-group-merge mb-4">
                            <div class="form-floating form-floating-outline">
                              <select name="status" id="status" class="select2 form-select form-control @error('status') is-invalid @enderror" data-allow-clear="true">
                                <option value="">Pilih Status</option>
                                <option value="1" {{ $item->status == '1' ? 'selected' : '' }}>Tersedia</option>
                                <option value="0" {{ $item->status == '0' ? 'selected' : '' }}>Tidak Tersedia</option>
                              </select>
                              <label for="status">Status</label>
                              @error('status')<div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div> --}}
                    </div> 
                    <div class="col-md-6"> 
                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-numeric"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="no_single_tid"
                            placeholder="Ketik nomor single TID disini"
                            aria-label="Ketik nomor single TID disini"
                            aria-describedby="no_single_tid" 
                            name="no_single_tid"
                            value="{{ old('no_single_tid') ? old('no_single_tid') : $item->no_single_tid }}" class="form-control @error('no_single_tid') is-invalid @enderror"/>
                          <label for="no_single_tid">Nomor Single TID</label>
                          @error('no_single_tid')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-calendar-range"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="date"
                            id="expired_single_tid"
                            class="form-control datepicker @error('expired_single_tid') is-invalid @enderror"
                            aria-describedby="expired_single_tid"
                            name="expired_single_tid" value="{{ old('expired_single_tid') ? old('expired_single_tid') : substr($item->expired_single_tid, 0, 10) }}" />
                          <label for="expired_single_tid">Masa Berlaku Single TID</label>
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"
                          ><i class="mdi mdi-cellphone-marker"></i
                        ></span>
                        <div class="form-floating form-floating-outline">
                          <input
                            type="text"
                            id="nama_gps"
                            placeholder="Ketik nama GPS disini"
                            aria-label="Ketik nama GPS disini"
                            aria-describedby="nama_gps" 
                            name="nama_gps"
                            value="{{ old('nama_gps') ? old('nama_gps') : $item->nama_gps }}" class="form-control @error('nama_gpsF') is-invalid @enderror"/>
                          <label for="nama_gps">Nama GPS</label>
                          @error('nama_gps')<div class="text-danger">{{ $message }}</div> @enderror 
                        </div>
                      </div>

                      <div class="input-group input-group-merge mb-4">
                        <span class="input-group-text"><i class="mdi mdi-note-text-outline"></i></span>
                        <div class="form-floating form-floating-outline">
                            <textarea
                                id="keterangan"
                                placeholder="Ketik catatan disini"
                                aria-label="Ketik catatan disini"
                                aria-describedby="keterangan"
                                style="height: 70px"
                                name="keterangan"
                                class="form-control @error('keterangan') is-invalid @enderror"
                            >{{ old('keterangan') ? old('keterangan') : $item->keterangan }}</textarea>
                            <label for="keterangan">Keterangan</label>
                            @error('keterangan')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div> 

                      {{-- <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <select name="area_id" id="area_id" class="select2 form-select form-control @error('area_id') is-invalid @enderror" data-allow-clear="true">
                            <option value="">Pilih Area</option>
                            @foreach($areas as $area)
                              <option value="{{ $area->id }}" {{ $area->id == $item->area_id ? 'selected' : '' }}>
                                  {{ $area->name }}
                              </option>
                            @endforeach
                          </select>
                          <label for="area_id">Area</label>
                          @error('area_id')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div> --}}

                      <div class="input-group input-group-merge mb-4">
                          <div class="form-floating form-floating-outline">
                            <select name="driver_id" id="driver_id" class="select2 form-select form-control @error('driver_id') is-invalid @enderror" data-allow-clear="true">
                              <option value="">Pilih Supir</option>
                              @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ $driver->id == $item->driver_id ? 'selected' : '' }}>
                                    {{ $driver->name }}
                                </option>
                              @endforeach
                            </select>
                            <label for="driver_id">Supir</label>
                            @error('driver_id')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>
                      </div> 
                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <select name="group_id" id="group_id" class="select2 form-select form-control @error('group_id') is-invalid @enderror" data-allow-clear="true">
                            <option value="">Pilih Group</option>
                            @foreach($groups as $group)
                              <option value="{{ $group->id }}" {{ $group->id == $item->group_id ? 'selected' : '' }}>
                                {{ $group->name }}
                            @endforeach
                          </select>
                          <label for="group_id">Group</label>
                          @error('group_id')<div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                      </div>

                      @if(auth()->user()->customer_id == 1)
                        <div class="input-group input-group-merge mb-4"> 
                          <div class="form-floating form-floating-outline">
                              <select name="status" class="form-control @error('status') is-invalid @enderror">
                                  <option value="">- Pilih Status -</option>
                                  <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Aktif</option>
                                  <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                              </select>
                              <label for="status">Status</label>
                              @error('status')<div class="text-danger">{{ $message }}</div> @enderror
                          </div>                                                
                        </div>
                      @endif                  
                        <div class="row">
                            <div class="col-md-6">
                              <div class="card" style="width: 100%;">
                                <div class="card-header">STNK</div>
                                @if (!empty($item->photo_stnk))
                                  @if(pathinfo($item->photo_stnk, PATHINFO_EXTENSION) == 'pdf')
                                    <img src="{{ url('backend/img/file-pdf.png') }}" class="card-img-top card-img-bottom">
                                  @else
                                    <img id="previewImageSTNK" src="{{ url('storage/' . $item->photo_stnk) }}" class="card-img-top" alt="...">
                                  @endif                                    
                                @else
                                  <img id="previewImageSTNK" src="{{ url('backend/img/stnk.png') }}" class="card-img-top" alt="...">
                                @endif
                                <div class="card-body">
                                    <input class="form-control" type="file" id="photo_stnk" name="photo_stnk" onchange="previewFileSTNK()">
                                    @error('photo_stnk')<div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="card" style="width: 100%;">
                                <div class="card-header">Head KIR</div>
                                @if (!empty($item->photo_head_kir))
                                  @if(pathinfo($item->photo_stnk, PATHINFO_EXTENSION) == 'pdf')
                                    <img src="{{ url('backend/img/file-pdf.png') }}" class="card-img-top card-img-bottom">
                                  @else
                                  <img id="previewImageHeadKir" src="{{ url('storage/' . $item->photo_head_kir) }}" class="card-img-top" alt="...">
                                  @endif                                    
                                @else
                                    <img id="previewImageHeadKir" src="{{ url('backend/img/head_kir.png') }}" class="card-img-top" alt="...">
                                @endif
                                <div class="card-body">
                                    <input class="form-control" type="file" id="photo_head_kir" name="photo_head_kir" onchange="previewFileHeadKir()">
                                    @error('photo_head_kir')<div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                              </div>
                            </div>
                        </div>
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="card mt-3" style="width: 100%;">
                              <div class="card-header">Chasis KIR</div>
                              @if (!empty($item->photo_chasis_kir))
                                @if(pathinfo($item->photo_stnk, PATHINFO_EXTENSION) == 'pdf')
                                  <img src="{{ url('backend/img/file-pdf.png') }}" class="card-img-top card-img-bottom">
                                @else
                                <img id="previewImageChasisKIR" src="{{ url('storage/' . $item->photo_chasis_kir) }}" class="card-img-top" alt="...">
                                @endif                                   
                              @else
                                  <img id="previewImageChasisKIR" src="{{ url('backend/img/chasis_kir.png') }}" class="card-img-top" alt="...">
                              @endif
                              <div class="card-body">
                                  <input class="form-control" type="file" id="photo_chasis_kir" name="photo_chasis_kir" onchange="previewFileChasisKIR()">
                                  @error('photo_chasis_kir')<div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="card mt-3" style="width: 100%;">
                              <div class="card-header">B3 KLHK</div>
                              @if (!empty($item->photo_b3_klhk))
                                @if(pathinfo($item->photo_stnk, PATHINFO_EXTENSION) == 'pdf')
                                  <img src="{{ url('backend/img/file-pdf.png') }}" class="card-img-top card-img-bottom">
                                @else
                                  <img id="previewImageB3KLHK" src="{{ url('storage/' . $item->photo_b3_klhk) }}" class="card-img-top" alt="...">
                                @endif                                   
                              @else
                                  <img id="previewImageB3KLHK" src="{{ url('backend/img/b3_klhk.png') }}" class="card-img-top" alt="...">
                              @endif
                              <div class="card-body">
                                  <input class="form-control" type="file" id="photo_b3_klhk" name="photo_b3_klhk" onchange="previewFileHeadB3KLHK()">
                                  @error('photo_b3_klhk')<div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6">
                            <div class="card mt-3" style="width: 100%;">
                              <div class="card-header">Kartu Pengawasan Kemenhub</div>
                              @if (!empty($item->photo_kartu_pengawasan_kemenhub))
                                @if(pathinfo($item->photo_stnk, PATHINFO_EXTENSION) == 'pdf')
                                  <img src="{{ url('backend/img/file-pdf.png') }}" class="card-img-top card-img-bottom">
                                @else
                                <img id="previewImageKemenhub" src="{{ url('storage/' . $item->photo_kartu_pengawasan_kemenhub) }}" class="card-img-top" alt="...">
                                @endif 
                                  
                              @else
                                  <img id="previewImageKemenhub" src="{{ url('backend/img/kemenhub.png') }}" class="card-img-top" alt="...">
                              @endif
                              <div class="card-body">
                                  <input class="form-control" type="file" id="photo_kartu_pengawasan_kemenhub" name="photo_kartu_pengawasan_kemenhub" onchange="previewFileKemenhub()">
                                  @error('photo_kartu_pengawasan_kemenhub')<div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            
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
<!-- jQuery dan Select2 dimuat di bagian bawah halaman -->
{{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- ... Kode CSS dan JavaScript lainnya ... -->

<!-- Inisialisasi Select2 di dalam blok $(document).ready -->
<script>
    $(document).ready(function() {
    $('#driver_id').select2({
        allowClear: true,
        placeholder: 'Pilih Supir',
        dropdownAutoWidth: true,
        width: '100%',
    });

    $('#group_id').select2({
        allowClear: true,
        placeholder: 'Pilih Group',
        dropdownAutoWidth: true,
        width: '100%',
    });
});

</script>

<script>
    function previewFileSTNK() {
        var preview = document.getElementById('previewImageSTNK');
        var fileInput = document.getElementById('photo_stnk');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            //reader.readAsDataURL(file);
            if (file.type === 'application/pdf') {
                preview.src = "{{ url('backend/img/file-pdf.png') }}";
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        } else {
            preview.src = "{{ url('backend/img/stnk.png') }}";
        }
    }

    function previewFileHeadKir() {
        var preview = document.getElementById('previewImageHeadKir');
        var fileInput = document.getElementById('photo_head_kir');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            //reader.readAsDataURL(file);
            if (file.type === 'application/pdf') {
                preview.src = "{{ url('backend/img/file-pdf.png') }}";
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        } else {
            preview.src = "{{ url('backend/img/head_kir.png') }}";
        }
    }

    function previewFileChasisKIR() {
        var preview = document.getElementById('previewImageChasisKIR');
        var fileInput = document.getElementById('photo_chasis_kir');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            //reader.readAsDataURL(file);
            if (file.type === 'application/pdf') {
                preview.src = "{{ url('backend/img/file-pdf.png') }}";
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        } else {
            preview.src = "{{ url('backend/img/chasis_kir.png') }}";
        }
    }

    function previewFileHeadB3KLHK() {
        var preview = document.getElementById('previewImageB3KLHK');
        var fileInput = document.getElementById('photo_b3_klhk');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            //reader.readAsDataURL(file);
            if (file.type === 'application/pdf') {
                preview.src = "{{ url('backend/img/file-pdf.png') }}";
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        } else {
            preview.src = "{{ url('backend/img/b3_klhk.png') }}";
        }
    }

    function previewFileKemenhub() {
        var preview = document.getElementById('previewImageKemenhub');
        var fileInput = document.getElementById('photo_kartu_pengawasan_kemenhub');
        var file = fileInput.files[0];

        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        // if (file) {
        //     reader.readAsDataURL(file);
        // } else {
        //     preview.src = "{{ url('backend/img/kemenhub.png') }}";
        // }

        if (file) {
            if (file.type === 'application/pdf') {
                preview.src = "{{ url('backend/img/file-pdf.png') }}";
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        } else {
            preview.src = "{{ url('backend/img/kemenhub.png') }}";
        }

    }
</script>
@endpush