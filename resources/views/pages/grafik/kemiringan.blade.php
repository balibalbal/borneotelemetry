@push('style')
    <style>
        #vehicle_id+.select2-container .select2-selection--single {
            height: 45px;
            padding: 10px;
        }
    </style>
@endpush
@extends('layouts.admin')
@section('title', 'Grafik Distance')
@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-header header-elements">
              <div>
                <h5 class="card-title mb-0">Sudut Kemiringan</h5>
                <small class="text-muted">Laporan yang dapat di-generate ke Excel maksimal 1 bulan</small>
              </div>
            </div>
    
            <div class="card-body">
                <div class="card border border-primary shadow-sm">
                  <div class="card-body">
                    <div class="row">
    
                      <!-- Pilih Nopol -->
                      <div class="col-lg-4">
                        <div class="form-floating form-floating-outline mb-3">
                          <select name="vehicle_id" id="vehicle_id" class="form-select">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                              <option value="{{ $vehicle->id }}">{{ $vehicle->no_pol }}</option>
                            @endforeach
                          </select>
                          <label for="vehicle_id">Plat Number</label>
                        </div>
                      </div>
        
                      <!-- Tanggal Mulai -->
                      <div class="col-lg-4">
                        <div class="form-floating form-floating-outline mb-3">
                          <input type="date" id="start_date" name="start_date" class="form-control" required value="{{ old('start_date') }}">
                          <label id="label_start_date" for="start_date">Start Date</label></label>
                        </div>
                      </div>
    
                      <!-- Tanggal Akhir -->
                      <div class="col-lg-4">
                        <div class="form-floating form-floating-outline mb-3">
                          <input type="date" id="end_date" name="end_date" class="form-control" required value="{{ old('end_date') }}">
                          <label id="label_end_date" for="end_date">End_date</label>
                        </div>
                      </div>
    
                      <!-- Tombol Search -->
                      <div class="col-lg-2 mt-3">
                        <button class="btn rounded-pill btn-primary waves-effect waves-light" type="button" id="searchButton">
                            <i class="mdi mdi-car-search me-sm-1"></i> Search
                        </button>
                      </div>
    
                      <!-- Alert Error -->
                      <div class="col-12">
                        <div id="dateAlert" class="alert alert-danger alert-dismissible d-none mt-3" role="alert">
                          <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert()"></button>
                        </div>
                      </div>
    
                    </div> <!-- row -->
                  </div> <!-- card-body -->
                </div> 
            </div>
        </div>

        <!-- Grafik Distance -->
        <canvas id="distanceChart" width="800" height="400" class="mt-4" style="display: none;"></canvas>

        {{-- <div class="card shadow mt-4">
            <!-- Tabel Data -->
            <table class="table table-striped mt-4" id="dataTable" style="display: none;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Roll</th>
                        <th>Pitch</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi dengan AJAX -->
                </tbody>
            </table>
        </div> --}}
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Include SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {

            let chartInstance = null;

            // ================================
            //  GABUNGAN PLUGIN (Zona Merah + Label Arah + Panah)
            // ================================
            const tiltPlugin = {
                id: "tiltPlugin",
                afterDraw(chart) {
                    const { ctx, chartArea, scales } = chart;
                    if (!chartArea) return;

                    const yTop = scales.y.getPixelForValue(3);
                    const yBottom = scales.y.getPixelForValue(-3);
                    const { left, right, top, bottom } = chartArea;

                    ctx.save();

                    // ----------------------------
                    // 1) ZONA MERAH (ATAS & BAWAH)
                    // ----------------------------
                    ctx.fillStyle = "rgba(255,0,0,0.12)";
                    ctx.fillRect(left, top, right - left, yTop - top);           // zona atas
                    ctx.fillRect(left, yBottom, right - left, bottom - yBottom); // zona bawah

                    // ----------------------------
                    // 2) LABEL & PANAH ARAH
                    // ----------------------------
                    ctx.font = "13px Arial";
                    ctx.fillStyle = "#000";
                    ctx.textAlign = "center";

                    // Atas (+)
                    ctx.fillText(
                        "↗ +Roll: Miring ke Kanan   |   +Pitch: Miring Ke Belakang ↗",
                        (left + right) / 2,
                        top + 18
                    );

                    // Bawah (–)
                    ctx.fillText(
                        "↙ -Roll: Miring Ke Kiri     |     -Pitch: Miring Ke Depan ↙",
                        (left + right) / 2,
                        bottom - 10
                    );

                    ctx.restore();
                }
            };

            // ===============================================
            //  EVENT SEARCH
            // ===============================================
            $('#vehicle_id').select2({
                allowClear: true,
                placeholder: 'Select Vehicle',
                dropdownAutoWidth: true,
                width: '100%',
            });

            $('#searchButton').on('click', function () {
                if (!validateDates()) return;

                $('#distanceChart').show();
                // $('#dataTable').show();

                let vehicleId = $('#vehicle_id').val();
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                if (!vehicleId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Plat Number Empty',
                        text: 'Plat number must be fill',
                    });
                    return;
                }

                let url = `/grafik/kemiringan?vehicle_id=${vehicleId}&start_date=${startDate}&end_date=${endDate}`;

                $('#distanceChart').hide();
                // $('#dataTable').hide();

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {

                        if (response.distances.length === 0) {
                            if (chartInstance) chartInstance.destroy();

                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Not Found',
                                text: 'Tidak ada data untuk kendaraan ini pada periode yang dipilih.',
                            });

                            return;
                        }

                        if (chartInstance) chartInstance.destroy();

                        const data = response.distances;
                        const labels = data.map(i => i.time);
                        const rollData = data.map(i => i.roll);
                        const pitchData = data.map(i => i.pitch);

                        const ctx = document.getElementById('distanceChart').getContext('2d');

                        chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Roll (Kanan/Kiri)',
                                        data: rollData,
                                        borderColor: '#007bff',
                                        borderWidth: 2,
                                        pointRadius: 0,
                                        tension: 0.2
                                    },
                                    {
                                        label: 'Pitch (Depan/Belakang)',
                                        data: pitchData,
                                        borderColor: '#ff8800',
                                        borderWidth: 2,
                                        pointRadius: 0,
                                        tension: 0.2
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        min: -10,
                                        max: 10,
                                        title: {
                                            display: true,
                                            text: 'Derajat Kemiringan'
                                        }
                                    }
                                },
                                plugins: {
                                    annotation: {
                                        annotations: {
                                            batasAtas: {
                                                type: 'line',
                                                yMin: 3,
                                                yMax: 3,
                                                borderColor: 'red',
                                                borderWidth: 1.8,
                                                borderDash: [6, 6],
                                                label: {
                                                    display: true,
                                                    content: ['+3° Batas Aman'],
                                                    backgroundColor: 'rgba(255,0,0,0.25)',
                                                    yAdjust: -10,
                                                    padding: 6,
                                                    color: '#000',
                                                    font: { size: 11, weight: 'bold' }
                                                }
                                            },
                                            batasBawah: {
                                                type: 'line',
                                                yMin: -3,
                                                yMax: -3,
                                                borderColor: 'red',
                                                borderWidth: 1.8,
                                                borderDash: [6, 6],
                                                label: {
                                                    display: true,
                                                    content: ['-3° Batas Aman'],
                                                    backgroundColor: 'rgba(255,0,0,0.25)',
                                                    yAdjust: 10,
                                                    padding: 6,
                                                    color: '#000',
                                                    font: { size: 11, weight: 'bold' }
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            plugins: [tiltPlugin]
                        });

                        // ========== TABLE ==========
                        // let tbody = $('#dataTable tbody');
                        // tbody.empty();

                        // data.forEach(d => {
                        //     tbody.append(`
                        //         <tr>
                        //             <td>${d.time}</td>
                        //             <td>${d.roll}</td>
                        //             <td>${d.pitch}</td>
                        //         </tr>
                        //     `);
                        // });

                        $('#distanceChart').show();
                        // $('#dataTable').show();
                    }
                });
            });


            // ===============================================
            // VALIDASI TANGGAL
            // ===============================================
            function validateDates() {
                var s = $("#start_date").val();
                var e = $("#end_date").val();

                if (s === "" || e === "") {
                    showDateError("Tanggal awal dan tanggal akhir tidak boleh kosong.");
                    return false;
                }

                var diff = (new Date(e) - new Date(s)) / (1000 * 3600 * 24);

                if (diff > 31) {
                    showDateError("Rentang tanggal tidak boleh lebih dari 31 hari.");
                    return false;
                }

                $("#dateAlert").addClass("d-none");
                return true;
            }

            function showDateError(msg) {
                let alertBox = $("#dateAlert");
                alertBox.removeClass("d-none");
                alertBox.html(`${msg} <button class="btn-close" onclick="closeAlert()"></button>`);
            }
        });

        function closeAlert() {
            $("#dateAlert").addClass("d-none");
        }
    </script>

@endpush


