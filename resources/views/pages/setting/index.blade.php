@extends('layouts.admin')
@section('title', 'Pengaturan Notifikasi')
@section('content')
<div class="container-fluid">
    @if(session('pesan'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
    @endif

    <div class="row">        
        <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Pengaturan Notifikasi</h5>
        <div class="mb-4"><small>Pengataturan notifikasi even-even kendaraan</small> </div>                        
    </div>

    <div class="row">
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card">
                    <div class="card-body">
                        <!-- Toggle Switch suara -->
                        <label class="switch">
                        <input 
                            type="checkbox" 
                            onchange="saveSetting('suara', this.checked)" 
                            {{ auth()->user()->settings['suara'] ?? false ? 'checked' : '' }}
                        >
                        <span class="slider"></span>
                        </label>
                        <span>Notifikasi Suara</span>

                        <!-- Menu Dump (Muncul jika aktif) -->
                        @if(auth()->user()->settings['suara'] ?? false)
                        <div class="mt-2">
                            <small class="text-primary">Notifikasi Suara Aktif</small>
                        </div>
                        @else
                        <div class="mt-2">
                            <small class="text-danger">Notifikasi Suara Tidak Aktif</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- @if(auth()->user()->customer_id == 4)
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card">
                    <div class="card-body">
                        <!-- Toggle Switch Dump -->
                        <label class="switch">
                        <input 
                            type="checkbox" 
                            onchange="saveSetting('dump', this.checked)" 
                            {{ auth()->user()->settings['dump'] ?? false ? 'checked' : '' }}
                        >
                        <span class="slider"></span>
                        </label>
                        <span>Notifikasi Dump</span>

                        <!-- Menu Dump (Muncul jika aktif) -->
                        @if(auth()->user()->settings['dump'] ?? false)
                        <div class="mt-2">
                            <small class="text-primary">Notifikasi Dump Aktif</small>
                        </div>
                        @else
                        <div class="mt-2">
                            <small class="text-danger">Notifikasi Dump Tidak Aktif</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif --}}
        
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card">
                    <div class="card-body">
                        <!-- Toggle Switch device -->
                        <label class="switch">
                        <input 
                            type="checkbox" 
                            onchange="saveSetting('device', this.checked)" 
                            {{ auth()->user()->settings['device'] ?? false ? 'checked' : '' }}
                        >
                        <span class="slider"></span>
                        </label>
                        <span>Notifikasi Device</span>

                        <!-- Menu Dump (Muncul jika aktif) -->
                        @if(auth()->user()->settings['device'] ?? false)
                        <div class="mt-2">
                            <small class="text-primary">Notifikasi Device Aktif</small>
                        </div>
                        @else
                        <div class="mt-2">
                            <small class="text-danger">Notifikasi Device Tidak Aktif</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card">
                    <div class="card-body">
                        <!-- Toggle Switch roll -->
                        <label class="switch">
                        <input 
                            type="checkbox" 
                            onchange="saveSetting('roll', this.checked)" 
                            {{ auth()->user()->settings['roll'] ?? false ? 'checked' : '' }}
                        >
                        <span class="slider"></span>
                        </label>
                        <span>Notifikasi Kemiringan > 3&deg;</span>

                        <!-- Menu kemiringan (Muncul jika aktif) -->
                        @if(auth()->user()->settings['roll'] ?? false)
                        <div class="mt-2">
                            <small class="text-primary">Notifikasi Kemiringan Aktif</small>
                        </div>
                        @else
                        <div class="mt-2">
                            <small class="text-danger">Notifikasi Kemiringan Tidak Aktif</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  function saveSetting(key, value) {
    fetch('/settings/save', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ [key]: value })
    }).then(() => {
      location.reload(); // Refresh untuk update UI
    });
  }
</script>
@endpush
